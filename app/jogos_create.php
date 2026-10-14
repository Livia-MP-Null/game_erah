<?php


// Carrega as funções gerais do sistema.
require_once __DIR__ . '/../includes/functions.php';
// Verifica se o usuário que está acessando é administrador.
require_once __DIR__ . '/../includes/verifica_admin.php';
// Carrega as funções específicas relacionadas aos jogos.
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// VARIÁVEIS INICIAIS
// ============================================================
// Cria uma variável vazia para guardar mensagens de erro.
$erro = '';
// Cria uma variável que guarda informações principais do jogo vaziass
$dados = [
    'titulo'         => '',
    'desenvolvedora' => '',
    'criado_por'     => '',
    'ano_lancamento' => '',
    'descricao'      => ''
];
//cria uma variavel que vai conter os mesmos dados que categorias conexão
$categorias = categorias_listar($conexao);
//cria um arrey vazio para evitar erros
$posicoesAtuais = [];


// ============================================================
// PROCESSAMENTO
// ============================================================
//verifica se o que foi postado exede o limite 
if (post_excedeu_limite()) {
// se foi manda a mensagem de erro e mostra o tamanho maximo suportado pelo site
    $erro = "Os arquivos enviados passam do limite do servidor (post_max_size).";
// Senão chama a variavel server com o method post 
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
//trim corrige o que foi enviado deixando sem espaço 
//o se o dado dentro de dados (a variavel) for igual ao dado que tem na variavel ele posta o dado se não (??) ele posta um vazio e pipoca um erro na tela
    $dados['titulo']         = trim($_POST['titulo'] ?? '');
    $dados['desenvolvedora'] = trim($_POST['desenvolvedora'] ?? '');
    $dados['criado_por']     = trim($_POST['criado_por'] ?? '');
    $dados['ano_lancamento'] = trim($_POST['ano_lancamento'] ?? '');
    $dados['descricao']      = trim($_POST['descricao'] ?? '');
//A variavel posições  é igual a função ler posições que não recebe nenhum parametro
    $posicoes = ler_posicoes();
// A variavel posiçõesAtuais é igaul a variavel posições
    $posicoesAtuais = $posicoes;
//define a variavel imagem como vazia esperando para receber um valor
    $imagem = null;
//define a variavel video como vazia esperando para receber um valor
    $video = null;

    try {

        validar_jogo($dados);
        validar_posicoes($posicoes);

        $imagem = salvar_upload(
            $_FILES['imagem'] ?? null,
            IMAGEM_TIPOS,
            IMAGEM_MAX
        );

        $video = salvar_upload(
            $_FILES['video'] ?? null,
            VIDEO_TIPOS,
            VIDEO_MAX
        );

        jogo_criar(
            $conexao,
            $dados['titulo'],
            $dados['ano_lancamento'],
            $dados['desenvolvedora'],
            $dados['criado_por'],
            $dados['descricao'],
            $imagem,
            $video,
            $posicoes
        );

        header("Location: jogos_select.php?ok=criado");
        exit();

    } catch (Exception $e) {

        apagar_arquivo($imagem);
        apagar_arquivo($video);

        $erro = $e->getMessage();
    }
}

$textoBotao = 'Cadastrar jogo';
$jogoAtual = null;

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS geral -->
    <link rel="stylesheet" href="../style/style.css">

    <!-- CSS do cabeçalho -->
    <link rel="stylesheet" href="../style/header.css">

    <!-- CSS da barra ADMIN -->
    <link rel="stylesheet" href="../style/adm_style.css">

    <!-- CSS específico dos jogos -->
    <link rel="stylesheet" href="../style/jogos.css">

    <title>Novo jogo</title>

</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Cadastrar jogo:</h1>

        <?php if ($erro): ?>

            <p class="msg-erro">
                <?= esc_html($erro) ?>
            </p>

        <?php endif; ?>

        <?php include __DIR__ . '/../includes/form_jogo.php'; ?>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>