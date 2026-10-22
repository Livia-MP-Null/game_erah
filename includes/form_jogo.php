<?php
// ============================================================
// formulario_jogo.php | 
// ============================================================
//
//
// Ele é incluído por páginas como jogos_create.php
// e jogos_update.php.
//
// Variáveis esperadas da página que inclui este arquivo:
//
// $dados          -> valores atuais dos campos do formulário.
// $textoBotao     -> texto exibido no botão de envio.
// $jogoAtual      -> dados do jogo em edição ou null no cadastro.
// $categorias     -> categorias disponíveis para classificação.
// $posicoesAtuais -> posições atuais no formato
//                    [categoria_id => posição].
//
// Este arquivo não deve declarar funções novas.
// ============================================================


// Se $jogoAtual não tiver sido definida pela página que inclui
// este arquivo, atribui null para indicar que não há jogo atual.
//
// O operador ?? preserva o valor existente quando a variável
// já foi definida. Isso também ajuda a evitar avisos do editor.
$jogoAtual = $jogoAtual ?? null;

?>

<!--
    FORMULÁRIO PRINCIPAL

    action="" envia os dados para a própria página que exibiu
    o formulário.

    method="post" envia os dados pelo método HTTP POST.

    enctype="multipart/form-data" é necessário para permitir
    o envio de arquivos, como imagens.
-->
<form action="" method="post" enctype="multipart/form-data" class="form-jogo">


    <!-- ========================================================
         1. NOME DO JOGO
         ======================================================== -->

    <!-- O atributo for associa o rótulo ao campo com id="titulo". -->
    <label for="titulo">Nome do jogo:</label>

    <!--
        type="text" cria um campo de texto.
        name="titulo" identifica o campo enviado ao PHP.
        id="titulo" permite associar o label ao input.
        maxlength="255" limita o tamanho do texto no navegador.
        required exige o preenchimento no navegador.

        value recupera o título armazenado em $dados.
        ?? '' utiliza uma string vazia caso o índice não exista.
        esc_html() protege o conteúdo antes de exibi-lo no HTML.
    -->
    <input
        type="text"
        name="titulo"
        id="titulo"
        maxlength="255"
        required
        value="<?= esc_html($dados['titulo'] ?? '') ?>"
    >


    <!-- ========================================================
         2. DESENVOLVEDORA
         ======================================================== -->

    <label for="desenvolvedora">Desenvolvedora:</label>

    <!-- Campo para informar a empresa responsável pelo desenvolvimento. -->
    <input
        type="text"
        name="desenvolvedora"
        id="desenvolvedora"
        maxlength="255"
        required
        value="<?= esc_html($dados['desenvolvedora'] ?? '') ?>"
    >


    <!-- ========================================================
         3. QUEM FEZ O JOGO
         ======================================================== -->

    <label for="criado_por">Feito por:</label>

    <!-- Campo para informar quem criou ou produziu o jogo. -->
    <input
        type="text"
        name="criado_por"
        id="criado_por"
        maxlength="255"
        required
        value="<?= esc_html($dados['criado_por'] ?? '') ?>"
    >


    <!-- ========================================================
         4. ANO DE LANÇAMENTO
         ======================================================== -->

    <label for="ano_lancamento">Ano de lançamento:</label>

    <!--
        type="number" cria um campo numérico.
        min="1950" define o ano mínimo permitido no navegador.
        max limita o ano a dois anos além do ano atual.
        required exige o preenchimento.

        date('Y') retorna o ano atual com quatro dígitos.
        (int) converte o resultado para inteiro antes da soma.

        A década não é digitada: ela é calculada automaticamente
        a partir do ano de lançamento, conforme a lógica do projeto.
    -->
    <input
        type="number"
        name="ano_lancamento"
        id="ano_lancamento"
        min="1950"
        max="<?= (int) date('Y') + 2 ?>"
        required
        value="<?= esc_html($dados['ano_lancamento'] ?? '') ?>"
    >

    <!-- Texto auxiliar para orientar quem está preenchendo o formulário. -->
    <small>A década é calculada automaticamente a partir do ano.</small>


    <!-- ========================================================
         5. DESCRIÇÃO DO JOGO
         ======================================================== -->

    <label for="descricao">Descrição:</label>

    <!--
        textarea cria um campo de texto com várias linhas.
        rows="6" define a quantidade inicial de linhas visíveis.
        name="descricao" identifica o dado enviado ao PHP.
        required exige que a descrição seja preenchida.

        Diferentemente de input, o valor do textarea fica entre
        a tag de abertura e a tag de fechamento.
    -->
    <textarea
        name="descricao"
        id="descricao"
        rows="6"
        required
    ><?= esc_html($dados['descricao'] ?? '') ?></textarea>


    <!-- ========================================================
         6. CLASSIFICAÇÃO DO JOGO POR CATEGORIA
         ======================================================== -->

    <?php
    // Verifica se existem categorias disponíveis.
    // Se a lista estiver vazia, esta parte do formulário não aparece.
    if (!empty($categorias)):
    ?>

        <!-- fieldset agrupa campos relacionados. -->
        <fieldset class="classificacao">

            <!-- legend fornece o título do grupo de campos. -->
            <legend>Classificação na década (opcional)</legend>

            <!-- Explica como preencher as posições das categorias. -->
            <small>
                Se o jogo foi destaque da década dele, informe a posição em cada
                categoria (ex.: 3 = terceiro lugar). Deixe em branco o que não se aplica.
            </small>

            <?php
            // Percorre a lista de categorias.
            // Em cada repetição, $cat representa uma categoria.
            foreach ($categorias as $cat):
            ?>

                <div class="classificacao-linha">

                    <!--
                        O ID do label é montado com o ID da categoria.
                        Isso associa o texto ao campo de posição correto.
                    -->
                    <label for="posicao_<?= (int) $cat['id'] ?>">
                        <?= esc_html($cat['nome']) ?>:
                    </label>

                    <!--
                        type="number" permite informar a posição.
                        min="1" impede posições menores que 1 no navegador.
                        max="100" limita a posição a 100.
                        name="posicao[ID]" organiza os dados por categoria
                        quando o formulário é recebido pelo PHP.

                        Exemplo do formato enviado:
                        posicao[2] = 3

                        Isso significa posição 3 para a categoria de ID 2.

                        $posicoesAtuais recupera uma posição já cadastrada.
                        Se não houver posição para a categoria, o campo fica vazio.
                    -->
                    <input
                        type="number"
                        min="1"
                        max="100"
                        placeholder="posição"
                        name="posicao[<?= (int) $cat['id'] ?>]"
                        id="posicao_<?= (int) $cat['id'] ?>"
                        value="<?= esc_html($posicoesAtuais[$cat['id']] ?? '') ?>"
                    >

                </div>

            <?php endforeach; ?>

        </fieldset>

    <?php endif; ?>


    <!-- ========================================================
         7. IMAGEM DO JOGO
         ======================================================== -->

    <label for="imagem">Imagem (JPG, PNG ou WebP, até 5 MB):</label>

    <?php
    // Verifica se o jogo atual possui uma imagem cadastrada.
    //
    // No cadastro, $jogoAtual normalmente é null e não existe
    // imagem anterior para exibir.
    //
    // Na edição, o índice 'imagem' pode conter o nome do arquivo
    // salvo no banco de dados.
    if (!empty($jogoAtual['imagem'])):
    ?>

        <!--
            Exibe uma prévia da imagem atualmente cadastrada.

            URL_UPLOADS contém o caminho público para a pasta
            onde as imagens dos jogos são armazenadas.

            esc_html() protege o nome do arquivo inserido no HTML.
            alt fornece uma descrição alternativa da imagem.
        -->
        <img
            class="previa"
            src="<?= URL_UPLOADS . esc_html($jogoAtual['imagem']) ?>"
            alt="Imagem atual"
        >

        <!-- Informa que não é necessário enviar uma imagem nova. -->
        <small>Envie outra imagem apenas se quiser trocar a atual.</small>

    <?php endif; ?>

    <!--
        type="file" permite selecionar um arquivo do computador.
        name="imagem" identifica o arquivo recebido pelo PHP.
        accept restringe as opções exibidas no seletor a formatos
        de imagem comuns.

        A validação real do tipo e do tamanho também deve ocorrer
        no servidor. O atributo accept não é uma proteção de segurança.
    -->
    <input
        type="file"
        name="imagem"
        id="imagem"
        accept="image/jpeg,image/png,image/webp"
    >


    <!-- ========================================================
         8. LINK DO VÍDEO
         ======================================================== -->

    <label for="video">Link do vídeo (YouTube):</label>

    <?php
    // Define qual valor será exibido no campo de vídeo.
    //
    // PRIMEIRO CASO:
    // Se $dados['video'] existir, usa o valor recebido ou preparado
    // pela página que incluiu este formulário.
    //
    // Isso é importante quando o formulário volta a ser exibido
    // após uma validação com erro: o texto digitado pode ser mantido.
    if (isset($dados['video'])) {

        // Converte o valor para string antes de utilizá-lo no campo.
        $videoCampo = (string) $dados['video'];

    } else {

        // SEGUNDO CASO:
        // Se não existir um valor em $dados, tenta utilizar o vídeo
        // que já está salvo no registro do jogo.
        $videoCampo = (string) ($jogoAtual['video'] ?? '');

        // Confere se o valor parece ser um link reconhecido
        // pela função video_interpretar().
        //
        // Isso evita colocar no campo um nome de arquivo antigo
        // que não corresponda ao formato de link esperado.
        if (video_interpretar($videoCampo) === null) {

            // Se não for reconhecido como link válido,
            // deixa o campo vazio.
            $videoCampo = '';
        }
    }
    ?>

    <!--
        type="text" permite digitar ou colar o link.
        name="video" identifica o campo recebido pelo PHP.
        maxlength="255" limita o tamanho do valor no navegador.
        inputmode="url" sugere um teclado apropriado para URLs
        em dispositivos compatíveis.

        O placeholder mostra um exemplo de link, mas não é um valor
        enviado automaticamente ao servidor.
    -->
    <input
        type="text"
        name="video"
        id="video"
        maxlength="255"
        inputmode="url"
        placeholder="https://www.youtube.com/watch?v=..."
        value="<?= esc_html($videoCampo) ?>"
    >

    <!-- Explica que o vídeo é opcional. -->
    <small>Cole o link do vídeo. Deixe em branco para o jogo ficar sem vídeo.</small>


    <!-- ========================================================
         9. PRÉVIA DO VÍDEO
         ======================================================== -->

    <?php
    // Só exibe a prévia se o campo não estiver vazio
    // e se video_interpretar() reconhecer o link.
    //
    // video_html() é responsável por gerar o HTML da prévia.
    // A segurança dessa saída depende da implementação dessa função.
    if (
        $videoCampo !== '' &&
        video_interpretar($videoCampo) !== null
    ):
    ?>

        <!-- Texto que indica a existência de uma prévia do vídeo. -->
        <small>Prévia do vídeo:</small>

        <!-- Gera a prévia utilizando a função do projeto. -->
        <?= video_html($videoCampo) ?>

    <?php endif; ?>


    <!-- ========================================================
         10. BOTÕES DO FORMULÁRIO
         ======================================================== -->

    <div class="botoes">

        <!--
            Envia os dados para a página responsável pelo cadastro
            ou pela edição do jogo.
            O texto do botão vem da variável $textoBotao.
        -->
        <input
            type="submit"
            value="<?= esc_html($textoBotao) ?>"
        >

        <!--
            Leva o usuário de volta à listagem de jogos.
            Não salva as alterações que ainda não foram enviadas.
        -->
        <a href="jogos_select.php">Cancelar</a>

    </div>

</form>