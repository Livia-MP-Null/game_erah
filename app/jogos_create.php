<?php

require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';

$erro  = '';
$dados = ['titulo' => '', 'ano_lancamento' => '', 'descricao' => ''];

if (post_excedeu_limite()) {
    $erro = "Os arquivos enviados passam do limite do servidor (post_max_size no php.ini).";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados['titulo']         = trim($_POST['titulo'] ?? '');
    $dados['ano_lancamento'] = trim($_POST['ano_lancamento'] ?? '');
    $dados['descricao']      = trim($_POST['descricao'] ?? '');

    $imagem = $video = null;

    try {
        validar_jogo($dados);

        $imagem = salvar_upload($_FILES['imagem'] ?? null, IMAGEM_TIPOS, IMAGEM_MAX);
        $video  = salvar_upload($_FILES['video'] ?? null, VIDEO_TIPOS, VIDEO_MAX);

        jogo_criar($conexao, $dados['titulo'], $dados['ano_lancamento'], $dados['descricao'], $imagem, $video);

        header("Location: jogos_select.php?ok=criado");
        exit();
    } catch (Exception $e) {
        apagar_arquivo($imagem);   // não deixa arquivo órfão se algo falhou
        apagar_arquivo($video);
        $erro = $e->getMessage();
    }
}

$textoBotao = 'Cadastrar jogo';
$jogoAtual  = null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/jogos.css">
    <title>Novo jogo</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <h1>Cadastrar jogo:</h1>

        <?php if ($erro): ?>
            <p class="msg-erro"><?= esc_html($erro) ?></p>
        <?php endif; ?>

        <?php include __DIR__ . '/../includes/form_jogo.php'; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
