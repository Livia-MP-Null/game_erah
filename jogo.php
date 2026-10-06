<?php

require_once __DIR__ . '/database/conect.php';
require_once __DIR__ . '/includes/functions_jogos.php';

$jogo = jogo_buscar($conexao, (int) ($_GET['id'] ?? 0));

if (!$jogo) {
    header("Location: jogos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/jogos.css">
    <title><?= esc_html($jogo['titulo']) ?></title>
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="detalhe-jogo">
        <a href="jogos.php?decada=<?= (int) $jogo['decada'] ?>">← Voltar aos jogos dos anos <?= (int) $jogo['decada'] ?></a>

        <h1><?= esc_html($jogo['titulo']) ?></h1>
        <p class="detalhe-meta"><?= (int) $jogo['ano_lancamento'] ?> · Anos <?= (int) $jogo['decada'] ?></p>

        <?php if ($jogo['imagem']): ?>
            <img class="detalhe-imagem" src="<?= URL_UPLOADS . esc_html($jogo['imagem']) ?>" alt="<?= esc_html($jogo['titulo']) ?>">
        <?php endif; ?>

        <p class="detalhe-descricao"><?= nl2br(esc_html($jogo['descricao'])) ?></p>

        <?php if ($jogo['video']): ?>
            <video class="detalhe-video" src="<?= URL_UPLOADS . esc_html($jogo['video']) ?>" controls></video>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
