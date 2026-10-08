<?php

// ============================================================
// ARQUIVOS NECESSÁRIOS
// ============================================================

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// BUSCAR O JOGO PELO ID DA URL  (ex.: jogos_update.php?id=5)
// ============================================================

$id        = (int) ($_GET['id'] ?? 0);
$jogoAtual = jogo_buscar($conexao, $id);

// Jogo inexistente: volta para o relatório
if (!$jogoAtual) {
    header("Location: jogos_select.php");
    exit();
}


// ============================================================
// VARIÁVEIS
// ============================================================

$erro = '';

// O formulário começa preenchido com os dados atuais
$dados = [
    'titulo'         => $jogoAtual['titulo'],
    'desenvolvedora' => $jogoAtual['desenvolvedora'],
    'criado_por'     => $jogoAtual['criado_por'],
    'ano_lancamento' => $jogoAtual['ano_lancamento'],
    'descricao'      => $jogoAtual['descricao']
];

$categorias     = categorias_listar($conexao);
$posicoesAtuais = classificacoes_do_jogo($conexao, $id);


// ============================================================
// PROCESSAMENTO
// ============================================================

if (post_excedeu_limite()) {

    $erro = "Os arquivos enviados passam do limite do servidor (post_max_size).";

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados['titulo']         = trim($_POST['titulo'] ?? '');
    $dados['desenvolvedora'] = trim($_POST['desenvolvedora'] ?? '');
    $dados['criado_por']     = trim($_POST['criado_por'] ?? '');
    $dados['ano_lancamento'] = trim($_POST['ano_lancamento'] ?? '');
    $dados['descricao']      = trim($_POST['descricao'] ?? '');

    $posicoes       = ler_posicoes();
    $posicoesAtuais = $posicoes;

    $novaImagem = null;
    $novoVideo  = null;

    try {

        validar_jogo($dados);
        validar_posicoes($posicoes);

        $novaImagem = salvar_upload($_FILES['imagem'] ?? null, IMAGEM_TIPOS, IMAGEM_MAX);
        $novoVideo  = salvar_upload($_FILES['video'] ?? null, VIDEO_TIPOS, VIDEO_MAX);

        jogo_atualizar(
            $conexao,
            $id,
            $dados['titulo'],
            $dados['ano_lancamento'],
            $dados['desenvolvedora'],
            $dados['criado_por'],
            $dados['descricao'],
            $novaImagem,
            $novoVideo,
            $posicoes
        );

        // Só apaga o arquivo antigo se um novo foi enviado
        // (e só DEPOIS de o banco ter sido atualizado).
        if ($novaImagem) {
            apagar_arquivo($jogoAtual['imagem']);
        }

        if ($novoVideo) {
            apagar_arquivo($jogoAtual['video']);
        }

        header("Location: jogos_select.php?ok=atualizado");
        exit();

    } catch (Exception $e) {

        // Remove os arquivos novos que já tenham sido salvos
        apagar_arquivo($novaImagem);
        apagar_arquivo($novoVideo);

        $erro = $e->getMessage();
    }
}

$textoBotao = 'Salvar alterações';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/jogos.css">
    <title>Editar jogo</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Editar jogo:</h1>

        <?php if ($erro): ?>
            <p class="msg-erro"><?= esc_html($erro) ?></p>
        <?php endif; ?>

        <?php include __DIR__ . '/../includes/form_jogo.php'; ?>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
