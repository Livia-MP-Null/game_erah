<?php

require_once __DIR__ . '/includes/config.php';   // BASE_URL + sessão (sempre na 1ª linha)
require_once __DIR__ . '/includes/functions_jogos.php';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/jogos.css">
    <title>Jogos por década</title>
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="pagina-decadas">
        <h1>Escolha uma década</h1>
        <p class="sub">Cada década reúne os jogos que marcaram a época.</p>

        <?php include __DIR__ . '/includes/botoes_decadas.php'; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
