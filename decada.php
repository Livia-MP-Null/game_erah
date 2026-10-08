<?php
// Página pública de uma década: todos os jogos dela, um embaixo do outro.
// Quem chega aqui clicou num botão como  decada.php?d=1960

require_once __DIR__ . '/includes/config.php';              // BASE_URL + sessão
require_once __DIR__ . '/database/conect.php';              // cria $conexao
require_once __DIR__ . '/includes/functions_jogos.php';
require_once __DIR__ . '/includes/functions_interacao.php';

$decadas = decadas_site();

// Qual década? Vem da URL (?d=1960). Se não existir, volta para a escolha.
$d = (int) ($_GET['d'] ?? 0);

if (!isset($decadas[$d])) {
    header("Location: " . BASE_URL . "/jogos.php");
    exit();
}

// ---- Quem está vendo a página
$logado       = !empty($_SESSION['id']);
$usuarioAtual = (int) ($_SESSION['id'] ?? 0);
$ehAdmin      = !empty($_SESSION['admin']);

// ---- Jogos desta década (a MESMA consulta do relatório, só com filtro)
$jogos   = jogos_listar($conexao, $d);
$classif = classificacoes_todas($conexao);
$ids     = array_map('intval', array_column($jogos, 'id'));

// ---- Curtidas, favoritos e comentários.
// Se essas tabelas ainda não existirem no banco (correcoes_2.sql), a página
// continua funcionando, só sem essa parte.
try {
    $interacoes  = interacoes_dos_jogos($conexao, $ids, $usuarioAtual ?: null);
    $comentarios = comentarios_por_jogo($conexao, $ids);
} catch (Exception $e) {
    error_log("Interações indisponíveis: " . $e->getMessage());
    $interacoes  = [];
    $comentarios = [];
}

// Mensagem de erro deixada por interagir.php (mostra uma vez só)
$erroInteracao = $_SESSION['erro_interacao'] ?? '';
unset($_SESSION['erro_interacao']);

// ---- Década anterior e próxima
$chaves   = array_keys($decadas);
$pos      = array_search($d, $chaves);
$anterior = $chaves[$pos - 1] ?? null;
$proxima  = $chaves[$pos + 1] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/jogos.css">
    <title><?= esc_html($decadas[$d]) ?> | game.erah</title>
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <?php include __DIR__ . '/includes/view_decada.php'; ?>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
