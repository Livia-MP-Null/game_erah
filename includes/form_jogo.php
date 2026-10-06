<?php
// Formulário usado por jogos_create.php e jogos_update.php
// Espera: $dados (titulo, desenvolvedora, ano_lancamento, descricao), $textoBotao e, na edição, $jogoAtual
?>
<form action="" method="post" enctype="multipart/form-data" class="form-jogo">

    <label for="titulo">Nome do jogo:</label>
    <input type="text" name="titulo" id="titulo" maxlength="255" required
           value="<?= esc_html($dados['titulo']) ?>">

    <label for="desenvolvedora">Quem criou (desenvolvedora):</label>
    <input type="text" name="desenvolvedora" id="desenvolvedora" maxlength="255" required
           value="<?= esc_html($dados['desenvolvedora']) ?>">

    <label for="ano_lancamento">Ano de lançamento:</label>
    <input type="number" name="ano_lancamento" id="ano_lancamento"
           min="1950" max="<?= (int) date('Y') + 2 ?>" required
           value="<?= esc_html($dados['ano_lancamento']) ?>">
    <small>A década é calculada automaticamente a partir do ano.</small>

    <label for="descricao">Descrição:</label>
    <textarea name="descricao" id="descricao" rows="6" required><?= esc_html($dados['descricao']) ?></textarea>

    <label for="imagem">Imagem (JPG, PNG ou WebP, até 5 MB):</label>
    <?php if (!empty($jogoAtual['imagem'])): ?>
        <img class="previa" src="<?= URL_UPLOADS . esc_html($jogoAtual['imagem']) ?>" alt="Imagem atual">
        <small>Envie outra imagem apenas se quiser trocar a atual.</small>
    <?php endif; ?>
    <input type="file" name="imagem" id="imagem" accept="image/jpeg,image/png,image/webp">

    <label for="video">Vídeo (MP4 ou WebM, até 100 MB):</label>
    <?php if (!empty($jogoAtual['video'])): ?>
        <video class="previa" src="<?= URL_UPLOADS . esc_html($jogoAtual['video']) ?>" controls></video>
        <small>Envie outro vídeo apenas se quiser trocar o atual.</small>
    <?php endif; ?>
    <input type="file" name="video" id="video" accept="video/mp4,video/webm">

    <div class="botoes">
        <input type="submit" value="<?= esc_html($textoBotao) ?>">
        <a href="jogos_select.php">Cancelar</a>
    </div>

</form>
