


<?php

// ============================================================
// ARQUIVOS NECESSÁRIOS
// ============================================================

// Carrega a conexão com o banco e funções gerais do sistema.
require_once __DIR__ . '/../includes/functions.php';

// Verifica se o usuário está logado e possui permissão de administrador.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Carrega as funções específicas dos jogos.
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// VARIÁVEIS INICIAIS
// ============================================================

// Guarda mensagens de erro que serão exibidas na tela.
$erro = '';


// Dados do jogo.
// Esses valores começam vazios porque estamos cadastrando um novo jogo.
$dados = [
    'titulo'         => '',
    'desenvolvedora' => '',
    'criado_por'     => '',
    'ano_lancamento' => '',
    'descricao'      => ''
];


// ============================================================
// VERIFICAÇÃO DO TAMANHO DO ENVIO
// ============================================================

// Verifica se o tamanho total dos arquivos enviados ultrapassou
// o limite configurado no PHP (post_max_size).
if (post_excedeu_limite()) {

    $erro = "Os arquivos enviados passam do limite do servidor (post_max_size no php.ini).";


// ============================================================
// PROCESSAMENTO DO FORMULÁRIO
// ============================================================

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recebe os dados enviados pelo formulário.
    // trim() remove espaços desnecessários no começo e no final.

    $dados['titulo'] = trim(
        $_POST['titulo'] ?? ''
    );

    $dados['desenvolvedora'] = trim(
        $_POST['desenvolvedora'] ?? ''
    );

    // Novo campo: quem fez/criou o jogo.
    $dados['criado_por'] = trim(
        $_POST['criado_por'] ?? ''
    );

    $dados['ano_lancamento'] = trim(
        $_POST['ano_lancamento'] ?? ''
    );

    $dados['descricao'] = trim(
        $_POST['descricao'] ?? ''
    );


    // ========================================================
    // VARIÁVEIS DOS ARQUIVOS
    // ========================================================

    // Começam como null.
    // Se o usuário enviar imagem ou vídeo, receberão o nome
    // do arquivo salvo no servidor.
    $imagem = null;
    $video  = null;


    try {

        // ====================================================
        // VALIDAÇÃO DOS DADOS
        // ====================================================

        // Confere se título, desenvolvedora, ano e descrição
        // estão preenchidos corretamente.
        validar_jogo($dados);


        // ====================================================
        // UPLOAD DA IMAGEM
        // ====================================================

        // Tenta salvar a imagem enviada pelo usuário.
        //
        // IMAGEM_TIPOS = tipos permitidos
        // IMAGEM_MAX   = tamanho máximo permitido
        $imagem = salvar_upload(
            $_FILES['imagem'] ?? null,
            IMAGEM_TIPOS,
            IMAGEM_MAX
        );


        // ====================================================
        // UPLOAD DO VÍDEO
        // ====================================================

        // Tenta salvar o vídeo enviado pelo usuário.
        //
        // VIDEO_TIPOS = tipos permitidos
        // VIDEO_MAX   = tamanho máximo permitido
        $video = salvar_upload(
            $_FILES['video'] ?? null,
            VIDEO_TIPOS,
            VIDEO_MAX
        );


        // ====================================================
        // SALVAR O JOGO NO BANCO DE DADOS
        // ====================================================

        // Envia todos os dados para a função jogo_criar().
        //
        // A ordem dos parâmetros precisa ser a mesma definida
        // na função jogo_criar() dentro de functions_jogos.php.

        jogo_criar(
            $conexao,
            $dados['titulo'],
            $dados['ano_lancamento'],
            $dados['desenvolvedora'],
            $dados['criado_por'],
            $dados['descricao'],
            $imagem,
            $video
        );


        // ====================================================
        // CADASTRO CONCLUÍDO
        // ====================================================

        // Depois de cadastrar, volta para o relatório de jogos.
        // O parâmetro "ok=criado" faz aparecer a mensagem
        // "Jogo cadastrado com sucesso."
        header("Location: jogos_select.php?ok=criado");

        exit();


    } catch (Exception $e) {

        // ====================================================
        // SE ALGUMA COISA DER ERRADO
        // ====================================================

        // Se a imagem já tiver sido salva, mas o cadastro falhar,
        // apagamos a imagem para não deixar arquivo órfão.
        apagar_arquivo($imagem);

        // Mesma coisa para o vídeo.
        apagar_arquivo($video);

        // Guarda a mensagem do erro para mostrar na página.
        $erro = $e->getMessage();
    }
}


// ============================================================
// CONFIGURAÇÕES DO FORMULÁRIO
// ============================================================

// Texto que será usado no botão do formulário.
$textoBotao = 'Cadastrar jogo';

// Indica que não estamos editando um jogo existente.
$jogoAtual = null;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <!-- CSS geral do site -->
    <link
        rel="stylesheet"
        href="../style/style.css"
    >

    <!-- CSS específico dos jogos -->
    <link
        rel="stylesheet"
        href="../style/jogos.css"
    >

    <title>Novo jogo</title>

</head>

<body>

    <!-- Cabeçalho do sistema -->
    <?php include __DIR__ . '/../includes/header.php'; ?>


    <main>

        <h1>Cadastrar jogo:</h1>


        <!-- ==================================================
             MENSAGEM DE ERRO
             ================================================== -->

        <?php if ($erro): ?>

            <p class="msg-erro">
                <?= esc_html($erro) ?>
            </p>

        <?php endif; ?>


        <?php include __DIR__ . '/../includes/form_jogo.php'; ?>

    </main>


    <!-- Rodapé do sistema -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>

