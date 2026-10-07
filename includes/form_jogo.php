<?php
// ============================================================
// FORMULÁRIO DE JOGO
// ============================================================
// Este arquivo NÃO deve possuir funções.
// Ele apenas mostra os campos usados por jogos_create.php
// e jogos_update.php.
?>

<form action="" method="post" enctype="multipart/form-data" class="form-jogo">

    <!-- ======================================================
         NOME DO JOGO
         ====================================================== -->

    <label for="titulo">Nome do jogo:</label>

    <input
        type="text"
        name="titulo"
        id="titulo"
        maxlength="255"
        required
        value="<?= esc_html($dados['titulo'] ?? '') ?>"
    >


    <!-- ======================================================
         DESENVOLVEDORA
         ====================================================== -->

    <label for="desenvolvedora">
        Desenvolvedora:
    </label>

    <input
        type="text"
        name="desenvolvedora"
        id="desenvolvedora"
        maxlength="255"
        required
        value="<?= esc_html($dados['desenvolvedora'] ?? '') ?>"
    >


    <!-- ======================================================
         QUEM FEZ O JOGO
         ====================================================== -->

    <label for="criado_por">
        Feito por:
    </label>

    <input
        type="text"
        name="criado_por"
        id="criado_por"
        maxlength="255"
        required
        value="<?= esc_html($dados['criado_por'] ?? '') ?>"
    >


    <!-- ======================================================
         ANO DE LANÇAMENTO
         ====================================================== -->

    <label for="ano_lancamento">
        Ano de lançamento:
    </label>

    <input
        type="number"
        name="ano_lancamento"
        id="ano_lancamento"
        min="1950"
        max="<?= (int) date('Y') + 2 ?>"
        required
        value="<?= esc_html($dados['ano_lancamento'] ?? '') ?>"
    >

    <small>
        A década é calculada automaticamente a partir do ano.
    </small>


    <!-- ======================================================
         DESCRIÇÃO
         ====================================================== -->

    <label for="descricao">
        Descrição:
    </label>

    <textarea
        name="descricao"
        id="descricao"
        rows="6"
        required
    ><?= esc_html($dados['descricao'] ?? '') ?></textarea>


    <!-- ======================================================
         IMAGEM
         ====================================================== -->

    <label for="imagem">
        Imagem (JPG, PNG ou WebP, até 5 MB):
    </label>

    <?php if (!empty($jogoAtual['imagem'])): ?>

        <img
            class="previa"
            src="<?= URL_UPLOADS . esc_html($jogoAtual['imagem']) ?>"
            alt="Imagem atual"
        >

        <small>
            Envie outra imagem apenas se quiser trocar a atual.
        </small>

    <?php endif; ?>

    <input
        type="file"
        name="imagem"
        id="imagem"
        accept="image/jpeg,image/png,image/webp"
    >


    <!-- ======================================================
         VÍDEO
         ====================================================== -->

    <label for="video">
        Vídeo (MP4 ou WebM, até 100 MB):
    </label>

    <?php if (!empty($jogoAtual['video'])): ?>

        <video
            class="previa"
            src="<?= URL_UPLOADS . esc_html($jogoAtual['video']) ?>"
            controls
        ></video>

        <small>
            Envie outro vídeo apenas se quiser trocar o atual.
        </small>

    <?php endif; ?>

    <input
        type="file"
        name="video"
        id="video"
        accept="video/mp4,video/webm"
    >


    <!-- ======================================================
         BOTÕES
         ====================================================== -->

    <div class="botoes">

        <input
            type="submit"
            value="<?= esc_html($textoBotao) ?>"
        >

        <a href="jogos_select.php">
            Cancelar
        </a>

    </div>

</form>
