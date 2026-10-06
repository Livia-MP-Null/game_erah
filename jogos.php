<?php

require_once __DIR__ . '/database/conect.php';
require_once __DIR__ . '/includes/functions_jogos.php';

$decada = isset($_GET['decada']) ? (int) $_GET['decada'] : null;

$jogos    = jogos_listar($conexao, $decada);
$decadas  = jogos_por_decada($conexao);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/jogos.css">
    <title>Jogos</title>
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <h1>Jogos<?= $decada !== null ? ' dos anos ' . $decada : '' ?></h1>

        <nav class="filtro-decadas">
            <a href="jogos.php" class="<?= $decada === null ? 'ativa' : '' ?>">Todos</a>
            <?php foreach ($decadas as $d): ?>
                <a href="jogos.php?decada=<?= (int) $d['decada'] ?>"
                   class="<?= $decada === (int) $d['decada'] ? 'ativa' : '' ?>">
                    <?= (int) $d['decada'] ?>s
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if (!$jogos): ?>
            <p>Nenhum jogo cadastrado por aqui ainda.</p>
        <?php else: ?>
            <div class="grade-jogos">
                <?php foreach ($jogos as $j): ?>
                    <a class="cartao-jogo" href="jogo.php?id=<?= (int) $j['id'] ?>">
                        <?php if ($j['imagem']): ?>
                            <img src="<?= URL_UPLOADS . esc_html($j['imagem']) ?>" alt="<?= esc_html($j['titulo']) ?>">
                        <?php else: ?>
                            <div class="sem-imagem">Sem imagem</div>
                        <?php endif; ?>
                        <div class="cartao-info">
                            <h3><?= esc_html($j['titulo']) ?></h3>
                            <span><?= (int) $j['ano_lancamento'] ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
