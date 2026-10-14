<?php

// ATENÇÃO: não deixe NENHUMA linha em branco antes do "<?php" acima.
// Qualquer espaço antes dele faz o header("Location: ...") falhar
// com o erro "headers already sent".

// ============================================================
// ARQUIVOS NECESSÁRIOS
// ============================================================

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// VARIÁVEIS INICIAIS
// ============================================================

$erro = '';

$dados = [
    'titulo'         => '',
    'desenvolvedora' => '',
    'criado_por'     => '',
    'ano_lancamento' => '',
    'descricao'      => ''
];

$categorias = categorias_listar($conexao);
$posicoesAtuais = [];


// ============================================================
// PROCESSAMENTO
// ============================================================

if (post_excedeu_limite()) {

    $erro = "Os arquivos enviados passam do limite do servidor (post_max_size).";

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados['titulo']         = trim($_POST['titulo'] ?? '');
    $dados['desenvolvedora'] = trim($_POST['desenvolvedora'] ?? '');
    $dados['criado_por']     = trim($_POST['criado_por'] ?? '');
    $dados['ano_lancamento'] = trim($_POST['ano_lancamento'] ?? '');
    $dados['descricao']      = trim($_POST['descricao'] ?? '');

    $posicoes = ler_posicoes();
    $posicoesAtuais = $posicoes;

    $imagem = null;
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