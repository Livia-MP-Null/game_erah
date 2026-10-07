<?php

// ============================================================
// ARQUIVOS NECESSÁRIOS
// ============================================================

// Carrega a conexão com o banco e funções gerais.
require_once __DIR__ . '/../includes/functions.php';

// Verifica se o usuário é administrador.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Carrega as funções específicas dos jogos.
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// PEGAR O ID DO JOGO
// ============================================================

// Pega o ID enviado pela URL.
// Exemplo: jogos_update.php?id=5
$id = (int) ($_GET['id'] ?? 0);


// ============================================================
// BUSCAR O JOGO
// ============================================================

$jogoAtual = jogo_buscar($conexao, $id);


// Se o jogo não existir, volta para o relatório.
if (!$jogoAtual) {
    header("Location: jogos_select.php");
    exit();
}


// ============================================================
// VARIÁVEIS
// ============================================================

$erro = '';


// Preenche o formulário com os dados atuais do jogo.
$dados = [
    'titulo'         => $jogoAtual['titulo'],
    'desenvolvedora' => $jogoAtual['desenvolvedora'],
    'criado_por'     => $jogoAtual['criado_por'],
    'ano_lancamento' => $jogoAtual['ano_lancamento'],
    'descricao'      => $jogoAtual['descricao']
];


// ============================================================
// VERIFICAR LIMITE DE UPLOAD
// ============================================================

if (post_excedeu_limite()) {

    $erro = "Os arquivos enviados passam do limite do servidor (post_max_size no php.ini).";


// ============================================================
// PROCESSAR FORMULÁRIO
// ============================================================

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // --------------------------------------------------------
    // RECEBER DADOS DO FORMULÁRIO
    // --------------------------------------------------------

    $dados['titulo'] = trim(
        $_POST['titulo'] ?? ''
    );

    $dados['desenvolvedora'] = trim(
        $_POST['desenvolvedora'] ?? ''
    );

    // Campo novo: quem fez/criou o jogo.
    $dados['criado_por'] = trim(
        $_POST['criado_por'] ?? ''
    );

    $dados['ano_lancamento'] = trim(
        $_POST['ano_lancamento'] ?? ''
    );

    $dados['descricao'] = trim(
        $_POST['descricao'] ?? ''
    );


    // --------------------------------------------------------
    // ARQUIVOS
    // --------------------------------------------------------

    $novaImagem = null;
    $novoVideo = null;


    try {

        // ----------------------------------------------------
        // VALIDAR DADOS
        // ----------------------------------------------------

        validar_jogo($dados);


        // ----------------------------------------------------
        // SALVAR NOVA IMAGEM
        // ----------------------------------------------------

        $novaImagem = salvar_upload(
            $_FILES['imagem'] ?? null,
            IMAGEM_TIPOS,
            IMAGEM_MAX
        );


        // ----------------------------------------------------
        // SALVAR NOVO VÍDEO
        // ----------------------------------------------------

        $novoVideo = salvar_upload(
            $_FILES['video'] ?? null,
            VIDEO_TIPOS,
            VIDEO_MAX
        );


        // ----------------------------------------------------
        // ATUALIZAR NO BANCO
        // ----------------------------------------------------

        jogo_atualizar(
            $conexao,
            $id,
            $dados['titulo'],
            $dados['ano_lancamento'],
            $dados['desenvolvedora'],
            $dados['criado_por'],
            $dados['descricao'],
            $novaImagem,
            $novoVideo
        );


        // ----------------------------------------------------
        // APAGAR ARQUIVOS ANTIGOS
        // ----------------------------------------------------

        // Só apagamos a imagem antiga se uma nova foi enviada.
        if ($novaImagem) {
            apagar_arquivo($jogoAtual['imagem']);
        }


        // Só apagamos o vídeo antigo se um novo foi enviado.
        if ($novoVideo) {
            apagar_arquivo($jogoAtual['video']);
        }


        // ----------------------------------------------------
        // VOLTAR PARA O RELATÓRIO
        // ----------------------------------------------------

        header("Location: jogos_select.php?ok=atualizado");
        exit();


    } catch (Exception $e) {


        // Se algo der errado, remove os arquivos novos
        // que eventualmente já tenham sido salvos.

        apagar_arquivo($novaImagem);
        apagar_arquivo($novoVideo);


        // Mostra o erro na página.
        $erro = $e->getMessage();
    }
}


// Texto que aparece no botão.
$textoBotao = 'Salvar alterações';


// Indica que estamos editando um jogo.
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="../style/style.css"
    >

    <link
        rel="stylesheet"
        href="../style/jogos.css"
    >

    <title>Editar jogo</title>

</head>

<body>


    <!-- ======================================================
         HEADER
         ====================================================== -->

    <?php include __DIR__ . '/../includes/header.php'; ?>


    <main>

        <h1>Editar jogo:</h1>


        <!-- ==================================================
             MENSAGEM DE ERRO
             ================================================== -->

        <?php if ($erro): ?>

            <p class="msg-erro">
                <?= esc_html($erro) ?>
            </p>

        <?php endif; ?>


        <!-- ==================================================
             FORMULÁRIO
             ================================================== -->

        <?php include __DIR__ . '/../includes/form_jogo.php'; ?>

    </main>


    <!-- ======================================================
         FOOTER
         ====================================================== -->

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>