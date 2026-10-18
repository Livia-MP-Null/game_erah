<?php
require_once __DIR__ . '/config.php';   // BASE_URL + sessão

$logado = !empty($_SESSION['id']);
$admin  = !empty($_SESSION['admin']);

// Botão "← Voltar": aparece em todas as páginas, menos nestas
// (a inicial, o painel e a década, que já tem o botão redondo dela).
$paginaAtual   = basename($_SERVER['SCRIPT_NAME'] ?? '');
$semVoltar     = ['index.php', 'painel.php', 'decada.php', 'preview_decada.php'];
$mostrarVoltar = !in_array($paginaAtual, $semVoltar, true);

// Se a pessoa abriu a página direto (sem página anterior), o botão leva para cá:
$destinoVoltar = BASE_URL . ($admin ? '/app/painel.php' : '/index.php');
?>

<!-- Estilo da logo (só a logo; não altera o resto do site) -->
<link rel="stylesheet" href="<?= BASE_URL ?>/style/logo.css">

<!-- .cabecalho = barra do admin + menu do site, juntos e fixos no topo -->
<div class="cabecalho">

    <?php
    // Barra do administrador: aparece em TODAS as páginas, só para admin.
    if ($admin) {
        include __DIR__ . '/admin_nav.php';
    }
    ?>

    <header class="topo">
        <nav class="navbar">

            <!-- Logo -->
            <!-- A logo é uma imagem que também é um link para a página inicial.
                 alt="game.erah" é o texto lido por leitores de tela e mostrado se a imagem não carregar.
                 width/height evitam que a página "pule" enquanto a imagem carrega. -->
            <a class="logo" href="<?= BASE_URL ?>/index.php" aria-label="game.erah, página inicial">
                <img class="logo-img" src="<?= BASE_URL ?>/img/logo.png" alt="game.erah" width="200" height="120">
            </a>

            <!-- Links do site -->
            <div class="menu">
                <a href="<?= BASE_URL ?>/index.php">Início</a>
                <a href="<?= BASE_URL ?>/jogos.php">Jogos</a>

                <?php if ($admin): ?>
                    <a href="<?= BASE_URL ?>/app/painel.php">Painel Admin</a>
                <?php endif; ?>
            </div>

            <!-- Login / cadastro / sair -->
            <div class="login">
                <?php if ($logado): ?>
                    <a href="<?= BASE_URL ?>/login/logout.php" class="btn-contorno">Sair</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login/cadastrar.php" class="btn-contorno">Cadastre-se</a>
                    <a href="<?= BASE_URL ?>/login/login.php" class="btn-laranja">Entrar</a>
                <?php endif; ?>
            </div>

        </nav>
    </header>

</div>

<?php if ($mostrarVoltar): ?>
    <div class="barra-voltar">
        <!-- Com histórico: volta para a página anterior. Sem histórico: usa o link. -->
        <a class="btn-voltar" href="<?= $destinoVoltar ?>"
           onclick="if (history.length > 1) { history.back(); return false; }">← Voltar</a>
    </div>
<?php endif; ?>
