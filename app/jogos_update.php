<?php






// Carrega as funções gerais do sistema.
require_once __DIR__ . '/../includes/functions.php';

// Verifica se o usuário que está acessando é administrador.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Carrega as funções específicas relacionadas aos jogos.
require_once __DIR__ . '/../includes/functions_jogos.php';


// Cria uma variável vazia para guardar mensagens de erro.
$erro = '';

// Cria uma variável vazia para guardar mensagens de sucesso.
$sucesso = '';


// Pega o ID enviado pelo formulário ou pela URL.
// Primeiro tenta pegar $_POST['id'].
// Se não existir, tenta $_GET['id'].
// Se nenhum existir, usa 0.
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);


// Verifica se o ID é inválido.
if ($id <= 0) {

    // Para a execução do PHP e mostra uma mensagem de erro.
    die('ID do jogo inválido.');
}


// Busca o jogo no banco de dados usando o ID recebido.
$jogo = jogo_buscar($conexao, $id);


// Se nenhum jogo foi encontrado...
if (!$jogo) {

    // Para a execução e informa que o jogo não existe.
    die('Jogo não encontrado.');
}


// Busca todas as categorias existentes no banco.
$categorias = categorias_listar($conexao);

// Busca as classificações que já pertencem ao jogo.
$classificacoes = classificacoes_do_jogo($conexao, $id);


// Verifica se o formulário foi enviado usando POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Inicia um bloco que permite capturar erros usando try/catch.
    try {

        // Verifica se o tamanho dos dados enviados ultrapassou
        // o limite permitido pelo servidor.
        if (post_excedeu_limite()) {

            // Cria um erro caso o limite tenha sido ultrapassado.
            throw new Exception(
                'O envio ultrapassou o limite permitido pelo servidor.'
            );
        }


        // Pega o título enviado pelo formulário.
        // trim() remove espaços desnecessários no começo e no final.
        $titulo = trim($_POST['titulo'] ?? '');

        // Pega o nome da desenvolvedora.
        $desenvolvedora = trim($_POST['desenvolvedora'] ?? '');

        // Pega quem criou o jogo.
        $criado_por = trim($_POST['criado_por'] ?? '');

        // Pega o ano de lançamento.
        $ano = $_POST['ano_lancamento'] ?? '';

        // Pega a descrição do jogo.
        $descricao = trim($_POST['descricao'] ?? '');

        // Pega o LINK do vídeo digitado (ex.: link do YouTube).
        // Fica guardado nesta variável para voltar ao campo se acontecer um erro.
        $videoDigitado = trim($_POST['video'] ?? '');


        // Lê as posições das classificações enviadas pelo formulário.
        $posicoes = ler_posicoes();


        // Valida os dados básicos do jogo.
        validar_jogo([
            // Envia o título para validação.
            'titulo' => $titulo,

            // Envia a desenvolvedora para validação.
            'desenvolvedora' => $desenvolvedora,

            // Envia quem criou o jogo para validação.
            'criado_por' => $criado_por,

            // Envia o ano para validação.
            'ano_lancamento' => $ano,

            // Envia a descrição para validação.
            'descricao' => $descricao
        ]);


        // Verifica se as posições das classificações são válidas.
        validar_posicoes($posicoes);


        // Define inicialmente que nenhuma nova imagem foi enviada.
        $novaImagem = null;


        // Verifica se existe um arquivo chamado "imagem"
        // e se o usuário realmente enviou uma nova imagem.
        if (
            isset($_FILES['imagem']) &&
            $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            // Salva a nova imagem usando a função de upload.
            $novaImagem = salvar_upload(

                // Arquivo enviado pelo formulário.
                $_FILES['imagem'],

                // Tipos de imagem permitidos.
                IMAGEM_TIPOS,

                // Tamanho máximo permitido para a imagem.
                IMAGEM_MAX
            );
        }


        // O vídeo agora é um LINK (não mais um arquivo enviado).
        // Assim o site fica leve: o vídeo fica hospedado no YouTube.
        //
        // video_normalizar() confere o link e devolve a forma padrão dele:
        //   - campo vazio    -> null (o jogo fica SEM vídeo)
        //   - link válido    -> o link limpo (ex.: https://www.youtube.com/watch?v=ID)
        //   - link inválido  -> lança um erro (cai no catch e a mensagem aparece na tela)
        $video = video_normalizar($videoDigitado);


        // Atualiza os dados do jogo no banco de dados.
        jogo_atualizar(

            // Conexão com o banco de dados.
            $conexao,

            // ID do jogo que será atualizado.
            $id,

            // Novo título.
            $titulo,

            // Novo ano.
            $ano,

            // Nova desenvolvedora.
            $desenvolvedora,

            // Novo criador.
            $criado_por,

            // Nova descrição.
            $descricao,

            // Nova imagem.
            // Se for null, a imagem antiga é mantida.
            $novaImagem,

            // Link do vídeo.
            // Se for null, o vídeo é REMOVIDO do jogo (o campo do link ficou vazio).
            $video,

            // Novas classificações.
            $posicoes
        );


        // Só apaga a imagem antiga se:
        // 1. Uma nova imagem foi enviada;
        // 2. O jogo tinha uma imagem antiga.
        if ($novaImagem !== null && !empty($jogo['imagem'])) {

            // Apaga o arquivo antigo do servidor.
            apagar_arquivo($jogo['imagem']);
        }


        // Se o jogo ainda tinha um vídeo ANTIGO guardado como arquivo (de antes do
        // sistema de links), apaga esse arquivo do servidor para liberar espaço.
        // apagar_arquivo() ignora links, então só mexe em arquivos de verdade.
        if (!empty($jogo['video']) && $jogo['video'] !== $video) {

            apagar_arquivo($jogo['video']);
        }


        // Redireciona o administrador para a página de listagem dos jogos.
        // O parâmetro "ok=atualizado" pode ser usado para mostrar
        // uma mensagem de sucesso.
        header('Location: jogos_select.php?ok=atualizado');

        // Interrompe a execução depois do redirecionamento.
        exit;
    } catch (Throwable $e) {
        // Throwable captura TODO tipo de erro (inclusive erros internos do PHP),
        // e não só as "Exception". Assim a mensagem aparece na caixa vermelha da página
        // em vez de a pessoa ver a tela de "HTTP ERROR 500".

        // Se alguma coisa der errado durante a atualização,
        // verifica se uma nova imagem chegou a ser criada.
        if (!empty($novaImagem)) {

            // Apaga a nova imagem para não deixar arquivo desnecessário.
            apagar_arquivo($novaImagem);
        }


        // Guarda a mensagem do erro na variável $erro.
        // Ela será mostrada no formulário.
        $erro = $e->getMessage();


        // Mantém no formulário os valores que o administrador digitou
        // antes de acontecer o erro.
        $jogo['titulo'] = $titulo ?? $jogo['titulo'];

        // Mantém a desenvolvedora digitada.
        $jogo['desenvolvedora'] =
            $desenvolvedora ?? $jogo['desenvolvedora'];

        // Mantém quem criou o jogo.
        $jogo['criado_por'] =
            $criado_por ?? $jogo['criado_por'];

        // Mantém o ano digitado.
        $jogo['ano_lancamento'] =
            $ano ?? $jogo['ano_lancamento'];

        // Mantém a descrição digitada.
        $jogo['descricao'] =
            $descricao ?? $jogo['descricao'];

        // Mantém o link do vídeo digitado.
        $jogo['video'] = $videoDigitado ?? $jogo['video'];

        // Mantém as classificações selecionadas.
        $classificacoes = $posicoes ?? $classificacoes;
    }
}


// Calcula a década com base no ano de lançamento.
$decada = decada_do_ano($jogo['ano_lancamento']);

?>

<!DOCTYPE html>
<!-- Informa ao navegador que o documento usa HTML5. -->

<html lang="pt-br">
<!-- Inicia o documento HTML e informa que o idioma é português do Brasil. -->

<head>
    <!-- Inicia a área de configurações da página. -->

    <meta charset="UTF-8">
    <!-- Permite utilizar acentos e caracteres especiais. -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Faz a página se adaptar ao tamanho da tela, inclusive celular. -->

    <link rel="stylesheet" href="../style/style.css">
    <!-- Carrega o CSS principal do site. -->

    <link rel="stylesheet" href="../style/header.css">
    <!-- Carrega o CSS responsável pelo cabeçalho. -->

    <link rel="stylesheet" href="../style/adm_style.css">
    <!-- Carrega o CSS utilizado pela área administrativa. -->

    <link rel="stylesheet" href="../style/jogos.css">
    <!-- Carrega o CSS específico das páginas de jogos. -->

    <title>Editar jogo</title>
    <!-- Define o título que aparece na aba do navegador. -->

</head>

<body>
    <!-- Inicia o conteúdo visível da página. -->

    <?php include __DIR__ . '/../includes/header.php'; ?>
    <!-- Inclui o cabeçalho do site.
     __DIR__ representa a pasta atual do arquivo. -->

    <main class="admin-conteudo">
        <!-- Cria a área principal da página administrativa. -->

        <div class="admin-titulo">
            <!-- Área que contém o título da página. -->

            <h1>Editar jogo</h1>
            <!-- Título principal. -->

            <p>
                Altere as informações do jogo abaixo.
            </p>
            <!-- Pequena explicação para o administrador. -->

        </div>


        <?php if ($erro): ?>
            <!-- Só entra neste trecho se $erro tiver algum conteúdo. -->

            <p class="msg-erro">
                <?= esc_html($erro) ?>
            </p>
            <!-- Mostra a mensagem de erro.
             esc_html() protege o conteúdo contra HTML indesejado. -->

        <?php endif; ?>


        <form class="form-admin" method="post" enctype="multipart/form-data">
            <!-- Cria o formulário.
         method="post" envia os dados através de POST.
         multipart/form-data permite enviar arquivos. -->


            <input type="hidden" name="id" value="<?= (int) $jogo['id'] ?>">
            <!-- Campo invisível que guarda o ID do jogo.
             Ele é enviado junto com o formulário. -->


            <div class="campo">
                <!-- Agrupa o campo do nome do jogo. -->

                <label for="titulo">
                    Nome do jogo
                </label>
                <!-- Texto que identifica o campo. -->

                <input type="text" id="titulo" name="titulo" maxlength="255" required
                    value="<?= esc_html($jogo['titulo']) ?>">
                <!-- Campo onde o administrador pode editar o nome.
                 maxlength limita o tamanho.
                 required torna o preenchimento obrigatório.
                 value coloca o nome atual do jogo no campo. -->

            </div>


            <div class="campo">

                <label for="desenvolvedora">
                    Desenvolvedora
                </label>
                <!-- Identifica o campo da desenvolvedora. -->

                <input type="text" id="desenvolvedora" name="desenvolvedora" maxlength="255" required
                    value="<?= esc_html($jogo['desenvolvedora']) ?>">
                <!-- Campo para alterar a desenvolvedora. -->

            </div>


            <div class="campo">

                <label for="criado_por">
                    Feito por
                </label>
                <!-- Identifica quem criou o jogo. -->

                <input type="text" id="criado_por" name="criado_por" maxlength="255" required
                    value="<?= esc_html($jogo['criado_por']) ?>">
                <!-- Campo para editar o criador do jogo. -->

            </div>


            <div class="campo">

                <label for="ano_lancamento">
                    Ano de lançamento
                </label>
                <!-- Identifica o campo do ano. -->

                <input type="number" id="ano_lancamento" name="ano_lancamento" min="1950"
                    max="<?= (int) date('Y') + 2 ?>" required value="<?= (int) $jogo['ano_lancamento'] ?>">
                <!-- Campo numérico para alterar o ano.
                 min define o menor ano permitido.
                 date('Y') pega o ano atual.
                 +2 permite cadastrar até dois anos à frente. -->

            </div>


            <div class="campo">

                <label>
                    Década
                </label>
                <!-- Identifica a informação da década. -->

                <input type="text" value="<?= (int) $decada ?>s" readonly>
                <!-- Mostra a década calculada automaticamente.
                 readonly impede o administrador de editar esse campo. -->

                <small>
                    A década é calculada automaticamente pelo ano de lançamento.
                </small>
                <!-- Explica por que a década não pode ser digitada manualmente. -->

            </div>


            <div class="campo">

                <label for="descricao">
                    Descrição
                </label>
                <!-- Identifica o campo da descrição. -->

                <textarea id="descricao" name="descricao" rows="6"
                    required><?= esc_html($jogo['descricao']) ?></textarea>
                <!-- Área de texto para editar a descrição do jogo.
                 rows="6" deixa o campo com aproximadamente 6 linhas. -->

            </div>


            <div class="campo">

                <label>
                    Classificação
                </label>
                <!-- Título da parte das classificações. -->


                <?php foreach ($categorias as $categoria): ?>
                    <!-- Percorre todas as categorias encontradas no banco. -->

                    <?php

                    // Pega o ID da categoria atual.
                    $categoriaId = (int) $categoria['id'];

                    // Começa sem nenhuma posição.
                    $valorPosicao = '';

                    // Verifica se essa categoria já possui uma classificação
                    // para o jogo que está sendo editado.
                    if (isset($classificacoes[$categoriaId])) {

                        // Se existir, pega a posição salva.
                        $valorPosicao = $classificacoes[$categoriaId];
                    }

                    ?>

                    <div class="classificacao-campo">
                        <!-- Cria o espaço visual para uma classificação. -->

                        <label>
                            <?= esc_html($categoria['nome']) ?>
                        </label>
                        <!-- Mostra o nome da categoria. -->

                        <input type="number" name="posicao[<?= $categoriaId ?>]" min="1" max="100"
                            value="<?= esc_html($valorPosicao) ?>" placeholder="Posição">
                        <!-- Campo para informar a posição do jogo.
                         O nome posicao[ID] permite enviar várias posições
                         associadas aos IDs das categorias. -->

                    </div>

                <?php endforeach; ?>
                <!-- Finaliza o loop das categorias. -->

            </div>


            <div class="campo">

                <label for="imagem">
                    Nova imagem
                </label>
                <!-- Identifica o campo de upload da imagem. -->


                <?php if (!empty($jogo['imagem'])): ?>
                    <!-- Verifica se o jogo já possui uma imagem. -->

                    <p>
                        Imagem atual:
                        <?= esc_html($jogo['imagem']) ?>
                    </p>
                    <!-- Mostra o nome do arquivo da imagem atual. -->

                    <img class="miniatura" src="<?= URL_UPLOADS . esc_html($jogo['imagem']) ?>"
                        alt="<?= esc_html($jogo['titulo']) ?>">
                    <!-- Mostra uma miniatura da imagem atual.
                     URL_UPLOADS indica a pasta onde os uploads estão. -->

                <?php endif; ?>


                <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp">
                <!-- Campo para selecionar uma nova imagem.
                 accept limita os tipos de arquivo mostrados/aceitos
                 pelo navegador. -->

                <small>
                    Deixe vazio para manter a imagem atual.
                </small>
                <!-- Explica que o upload é opcional. -->

            </div>


            <div class="campo">

                <label for="video">
                    Link do vídeo (YouTube)
                </label>
                <!-- Agora o vídeo é um LINK. Assim o site fica leve. -->


                <?php
                // O campo só mostra o link se o valor guardado for um link válido.
                // (Um jogo antigo pode ter o NOME DE UM ARQUIVO no lugar; nesse caso o
                // campo fica vazio, e é só colar o link do vídeo.)
                $videoCampo = video_interpretar($jogo['video'] ?? '') !== null
                    ? $jogo['video']
                    : '';

                // Se houve erro, mostra o que a pessoa tinha digitado.
                if (isset($videoDigitado)) {
                    $videoCampo = $videoDigitado;
                }
                ?>

                <input type="text" id="video" name="video" maxlength="255" inputmode="url"
                    placeholder="https://www.youtube.com/watch?v=..." value="<?= esc_html($videoCampo) ?>">
                <!-- Campo para colar (ou corrigir) o link do vídeo.
                 inputmode="url" mostra o teclado de endereços no celular. -->

                <small>
                    Para trocar o vídeo, edite o link. Para remover, apague o link e salve.
                </small>
                <!-- Explica como corrigir ou remover o vídeo. -->


                <?php if ($videoCampo !== '' && video_interpretar($videoCampo) !== null): ?>
                    <!-- Só mostra a prévia se o link for válido. -->

                    <p>Vídeo atual (clique no play para testar):</p>

                    <?= video_html($videoCampo) ?>
                    <!-- Mostra a miniatura do vídeo; ao clicar, o player abre. -->

                <?php endif; ?>

            </div>


            <div class="admin-acoes">
                <!-- Área onde ficam os botões. -->

                <button type="submit" class="btn-admin">
                    Salvar alterações
                </button>
                <!-- Botão que envia o formulário para o PHP.
                 Como type é submit, os dados são enviados. -->


                <a href="jogos_select.php" class="btn-secundario">
                    Cancelar
                </a>
                <!-- Link que leva de volta para a lista de jogos. -->

            </div>

        </form>


    </main>



    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <!-- Inclui o rodapé do site. -->

</body>


</html>