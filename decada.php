<?php

require_once __DIR__ . '/database/conect.php';
require_once __DIR__ . '/includes/functions_jogos.php';

$decadas = decadas_site();
$d = (int) ($_GET['d'] ?? 0);

// Década inexistente (ex.: ?d=1955) → volta para a escolha
if (!isset($decadas[$d])) {
    header("Location: jogos.php");
    exit();
}

$jogos = jogos_listar($conexao, $d);

// Década anterior e próxima, para navegar sem voltar ao menu
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
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/jogos.css">
    <title><?= esc_html($decadas[$d]) ?> | game.erah</title>
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="pagina-decada">

        <a class="voltar" href="jogos.php">← Voltar às décadas</a>

        <div class="decada-topo">
            <span class="btn-decada grande d-<?= $d ?>"><?= esc_html($decadas[$d]) ?></span>
            <p class="sub">
                <?= count($jogos) ?> <?= count($jogos) === 1 ? 'jogo cadastrado' : 'jogos cadastrados' ?>
            </p>
        </div>

        <?php if (!$jogos): ?>
            <p class="vazio">Ainda não há jogos desta década. Volte em breve!</p>
        <?php endif; ?>

        <?php foreach ($jogos as $j): ?>
            <article class="jogo-secao">

                <div class="jogo-texto">
                    <h2 class="jogo-titulo"><?= esc_html($j['titulo']) ?></h2>
                    <p class="jogo-meta">
                        <strong>Criado por:</strong> <?= esc_html($j['desenvolvedora']) ?>
                        &nbsp;·&nbsp;
                        <strong>Lançamento:</strong> <?= (int) $j['ano_lancamento'] ?>
                    </p>
                    <p class="jogo-descricao"><?= nl2br(esc_html($j['descricao'])) ?></p>
                </div>

                <?php if ($j['imagem'] || $j['video']): ?>
                    <div class="jogo-midia">
                        <?php if ($j['imagem']): ?>
                            <img src="<?= URL_UPLOADS . esc_html($j['imagem']) ?>" alt="<?= esc_html($j['titulo']) ?>">
                        <?php endif; ?>
                        <?php if ($j['video']): ?>
                            <video src="<?= URL_UPLOADS . esc_html($j['video']) ?>" controls preload="metadata"></video>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </article>
        <?php endforeach; ?>

        <nav class="decada-nav">
            <?php if ($anterior): ?>
                <a class="btn-decada d-<?= $anterior ?>" href="decada.php?d=<?= $anterior ?>">← <?= esc_html($decadas[$anterior]) ?></a>
            <?php else: ?><span></span><?php endif; ?>

            <?php if ($proxima): ?>
                <a class="btn-decada d-<?= $proxima ?>" href="decada.php?d=<?= $proxima ?>"><?= esc_html($decadas[$proxima]) ?> →</a>
            <?php else: ?><span></span><?php endif; ?>
        </nav>

    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
