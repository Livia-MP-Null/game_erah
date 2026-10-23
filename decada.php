<?php

// Página pública de uma década: todos os jogos dela, um embaixo do outro.
// Quem chega aqui clicou em um botão como decada.php?d=1960

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/database/conect.php';
require_once __DIR__ . '/includes/functions_jogos.php';
require_once __DIR__ . '/includes/functions_interacao.php';
require_once __DIR__ . '/includes/video.php'; // <-- NECESSÁRIO PARA O VÍDEO DO YOUTUBE

$decadas = decadas_site();

// Qual década? Vem da URL (?d=1960).
// Se não existir, volta para a escolha.
$d = (int) ($_GET['d'] ?? 0);

if (!isset($decadas[$d])) {
    header("Location: " . BASE_URL . "/jogos.php");
    exit();
}

// ---- Quem está vendo a página

$logado       = !empty($_SESSION['id']);
$usuarioAtual = (int) ($_SESSION['id'] ?? 0);
$ehAdmin      = !empty($_SESSION['admin']);

// ---- Jogos desta década

$jogos   = jogos_listar($conexao, $d);
$classif = classificacoes_todas($conexao);

$ids = array_map('intval', array_column($jogos, 'id'));

// ---- Curtidas, favoritos e comentários

try {

    $interacoes  = interacoes_dos_jogos(
        $conexao,
        $ids,
        $usuarioAtual ?: null
    );

    $comentarios = comentarios_por_jogo(
        $conexao,
        $ids
    );

} catch (Exception $e) {

    error_log("Interações indisponíveis: " . $e->getMessage());

    $interacoes  = [];
    $comentarios = [];
}

// Mensagem de erro deixada por interagir.php

$erroInteracao = $_SESSION['erro_interacao'] ?? '';

unset($_SESSION['erro_interacao']);

// ---- Década anterior e próxima

$chaves = array_keys($decadas);
//Pega todas as chaves (que são os anos das décadas, como 1980, 1990, 2000) do array $decadas e cria uma lista simples e ordenada com elas.
$pos = array_search($d, $chaves);
//Pesquisa em qual posição numérica (índice) a década atual ($d) está dentro dessa lista de chaves.
$anterior = $chaves[$pos - 1] ?? null;
//Pega o elemento localizado na posição anterior ($pos - 1). O operador ?? null (chamado de null coalescing) é uma rede de segurança: se o usuário estiver na primeira década e não houver nenhuma antes dela, o resultado não quebra o site, assumindo o valor null (vazio).
$proxima  = $chaves[$pos + 1] ?? null;
//Pega o elemento localizado na posição seguinte ($pos + 1), representando a próxima década. Da mesma forma, se o usuário estiver na última década registrada, o operador garante que o valor será null para que o botão "Próxima" não seja exibido incorretamente.

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS geral -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">

    <!-- CSS do cabeçalho -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/header.css">

    <!-- CSS da barra do administrador -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/adm_style.css">

    <!-- CSS das páginas de jogos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/jogos.css">

    <title><?= esc_html($decadas[$d]) ?> | game.erah</title>

</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <?php include __DIR__ . '/includes/view_decada.php'; ?>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>