<?php
// Botões das décadas. Pode ser incluído no index.php ou no jogos.php.
// Antes de incluir, carregue: require_once __DIR__ . '/functions_jogos.php';
?>
<nav class="decadas-botoes" aria-label="Escolha uma década">
    <?php foreach (decadas_site() as $ano => $rotulo): ?>
        <a class="btn-decada d-<?= (int) $ano ?>" href="/game_erah/decada.php?d=<?= (int) $ano ?>">
            <?= esc_html($rotulo) ?>
        </a>
    <?php endforeach; ?>
</nav>
