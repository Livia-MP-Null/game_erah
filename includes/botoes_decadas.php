<?php
// Botões das décadas. Pode ser incluído no index.php ou no jogos.php.
// Antes de incluir, carregue config.php e functions_jogos.php.
?>
<nav class="decadas-botoes" aria-label="Escolha uma década">
    <?php foreach (decadas_site() as $ano => $rotulo): ?>
        <a class="btn-decada d-<?= (int) $ano ?>" href="<?= BASE_URL ?>/decada.php?d=<?= (int) $ano ?>">
            <?= esc_html($rotulo) ?>
        </a>
    <?php endforeach; ?>
</nav>
