<?php
// ============================================================
// FORMULÁRIO DE JOGO
// ============================================================
// Este arquivo NÃO deve possuir funções.
// Ele só mostra os campos usados por jogos_create.php e jogos_update.php.
//
// Variáveis esperadas (definidas pela página que inclui este arquivo):
//   $dados          -> valores dos campos de texto (inclui 'video' = link do vídeo)
//   $textoBotao     -> texto do botão de enviar
//   $jogoAtual      -> jogo em edição (ou null no cadastro)
//   $categorias     -> lista de categorias (id, nome)
//   $posicoesAtuais -> [categoria_id => posição] já preenchidas
?>

<form action="" method="post" enctype="multipart/form-data" class="form-jogo">

    <!-- NOME DO JOGO -->
    <label for="titulo">Nome do jogo:</label>
    <input type="text" name="titulo" id="titulo" maxlength="255" required
           value="<?= esc_html($dados['titulo'] ?? '') ?>">

    <!-- DESENVOLVEDORA -->
    <label for="desenvolvedora">Desenvolvedora:</label>
    <input type="text" name="desenvolvedora" id="desenvolvedora" maxlength="255" required
           value="<?= esc_html($dados['desenvolvedora'] ?? '') ?>">

    <!-- QUEM FEZ O JOGO -->
    <label for="criado_por">Feito por:</label>
    <input type="text" name="criado_por" id="criado_por" maxlength="255" required
           value="<?= esc_html($dados['criado_por'] ?? '') ?>">

    <!-- ANO DE LANÇAMENTO -->
    <label for="ano_lancamento">Ano de lançamento:</label>
    <input type="number" name="ano_lancamento" id="ano_lancamento"
           min="1950" max="<?= (int) date('Y') + 2 ?>" required
           value="<?= esc_html($dados['ano_lancamento'] ?? '') ?>">
    <small>A década é calculada automaticamente a partir do ano.</small>

    <!-- DESCRIÇÃO -->
    <label for="descricao">Descrição:</label>
    <textarea name="descricao" id="descricao" rows="6" required><?= esc_html($dados['descricao'] ?? '') ?></textarea>

    <!-- CLASSIFICAÇÃO NA DÉCADA (opcional) -->
    <?php if (!empty($categorias)): ?>

        <fieldset class="classificacao">

            <legend>Classificação na década (opcional)</legend>

            <small>
                Se o jogo foi destaque da década dele, informe a posição em cada
                categoria (ex.: 3 = terceiro lugar). Deixe em branco o que não se aplica.
            </small>

            <?php foreach ($categorias as $cat): ?>

                <div class="classificacao-linha">

                    <label for="posicao_<?= (int) $cat['id'] ?>">
                        <?= esc_html($cat['nome']) ?>:
                    </label>

                    <input type="number" min="1" max="100" placeholder="posição"
                           name="posicao[<?= (int) $cat['id'] ?>]"
                           id="posicao_<?= (int) $cat['id'] ?>"
                           value="<?= esc_html($posicoesAtuais[$cat['id']] ?? '') ?>">

                </div>

            <?php endforeach; ?>

        </fieldset>

    <?php endif; ?>

    <!-- IMAGEM -->
    <label for="imagem">Imagem (JPG, PNG ou WebP, até 5 MB):</label>

    <?php if (!empty($jogoAtual['imagem'])): ?>
        <img class="previa" src="<?= URL_UPLOADS . esc_html($jogoAtual['imagem']) ?>" alt="Imagem atual">
        <small>Envie outra imagem apenas se quiser trocar a atual.</small>
    <?php endif; ?>

    <input type="file" name="imagem" id="imagem" accept="image/jpeg,image/png,image/webp">

    <!-- VÍDEO (por LINK, ex.: YouTube: deixa o site leve) -->
    <label for="video">Link do vídeo (YouTube):</label>

    <?php
    // Valor que aparece no campo:
    //  - se a página enviou $dados['video'] (o que a pessoa digitou), usa esse texto;
    //  - senão, usa o vídeo guardado no jogo, mas só se for um LINK válido
    //    (jogos antigos podem ter o nome de um arquivo no lugar; aí o campo fica vazio).
    if (isset($dados['video'])) {
        $videoCampo = (string) $dados['video'];
    } else {
        $videoCampo = (string) ($jogoAtual['video'] ?? '');

        if (video_interpretar($videoCampo) === null) {
            $videoCampo = '';
        }
    }
    ?>

    <input type="text" name="video" id="video" maxlength="255" inputmode="url"
           placeholder="https://www.youtube.com/watch?v=..."
           value="<?= esc_html($videoCampo) ?>">
    <small>Cole o link do vídeo. Deixe em branco para o jogo ficar sem vídeo.</small>

    <?php if ($videoCampo !== '' && video_interpretar($videoCampo) !== null): ?>
        <!-- Prévia: clique para testar o link -->
        <?= video_html($videoCampo) ?>
    <?php endif; ?>

    <!-- BOTÕES -->
    <div class="botoes">
        <input type="submit" value="<?= esc_html($textoBotao) ?>">
        <a href="jogos_select.php">Cancelar</a>
    </div>

</form>
