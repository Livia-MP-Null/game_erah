<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logado = !empty($_SESSION['id']);
$admin  = !empty($_SESSION['admin']);
?>

<header class="topo">
    <nav class="navbar">

        <!-- Logo -->
        <a class="logo" href="/game_erah/index.php">game.erah</a>

        <!-- Links do sistema -->
        <div class="menu">
            <a href="/game_erah/index.php">Início</a>

            <?php if ($admin): ?>
                <a href="/game_erah/app/create.php">Cadastrar</a>
                <a href="/game_erah/app/delete.php">Excluir</a>
                <a href="/game_erah/app/update.php">Atualizar</a>
                <a href="/game_erah/app/select.php">Relatório</a>
                <a href="/game_erah/app/select_w.php">Consultar</a>
                <br>
                <a href="/game_erah/app/jogos_create.php">Cadastrar Jogos</a>
                <a href="/game_erah/app/jogos_delete.php">Excluir jogos</a>
                <a href="/game_erah/app/jogos_update.php">Atualizar jogos</a>
                <a href="/game_erah/app/jogos_select.php">Relatório jogos</a>
            <?php endif; ?>
        </div>

        <!-- Links de login -->
        <div class="login">
            <?php if ($logado): ?>
                <a href="/game_erah/login/logout.php" class="btn-contorno">Sair</a>
            <?php else: ?>
                <a href="/game_erah/login/cadastrar.php" class="btn-contorno">Cadastre-se</a>
                <a href="/game_erah/login/login.php" class="btn-laranja">Entrar</a>
            <?php endif; ?>
        </div>

    </nav>
</header>