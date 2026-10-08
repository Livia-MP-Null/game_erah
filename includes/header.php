<?php
require_once __DIR__ . '/config.php';   // BASE_URL + sessão

$logado = !empty($_SESSION['id']);
$admin  = !empty($_SESSION['admin']);
?>

<header class="topo">
    <nav class="navbar">

        <!-- Logo -->
        <a class="logo" href="<?= BASE_URL ?>/index.php">game.erah</a>

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

<?php
// Barra do administrador: aparece em TODAS as páginas, só para admin.
if ($admin) {
    include __DIR__ . '/admin_nav.php';
}
?>
