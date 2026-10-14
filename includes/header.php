<?php

require_once __DIR__ . '/config.php';

$logado = !empty($_SESSION['id']);
$admin  = !empty($_SESSION['admin']);

?>

<header class="topo">

    <nav class="navbar">

        <a class="logo" href="<?= BASE_URL ?>/index.php">
            game.erah
        </a>

        <div class="menu">

            <a href="<?= BASE_URL ?>/index.php">
                Início
            </a>

            <a href="<?= BASE_URL ?>/jogos.php">
                Jogos
            </a>

            <?php if ($admin): ?>

                <a href="<?= BASE_URL ?>/app/painel.php">
                    Painel Admin
                </a>

            <?php endif; ?>

        </div>

        <div class="login">

            <?php if ($logado): ?>

                <a href="<?= BASE_URL ?>/login/logout.php" class="btn-contorno">
                    Sair
                </a>

            <?php else: ?>

                <a href="<?= BASE_URL ?>/login/cadastrar.php" class="btn-contorno">
                    Cadastre-se
                </a>

                <a href="<?= BASE_URL ?>/login/login.php" class="btn-laranja">
                    Entrar
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>


<?php if ($admin): ?>

    <nav class="admin-bar">

        <span class="admin-bar-titulo">
            ADMIN
        </span>

        <a href="<?= BASE_URL ?>/app/painel.php">
            Início do admin
        </a>

        <div class="admin-grupo">

            <span>
                Usuários ▾
            </span>

            <div class="admin-menu">

                <a href="<?= BASE_URL ?>/app/create.php">
                    Cadastrar usuário
                </a>

                <a href="<?= BASE_URL ?>/app/select.php">
                    Relatório de usuários
                </a>

                <a href="<?= BASE_URL ?>/app/select_w.php">
                    Consultar usuário
                </a>

                <a href="<?= BASE_URL ?>/app/update.php">
                    Atualizar usuário
                </a>

                <a href="<?= BASE_URL ?>/app/delete.php">
                    Excluir usuário
                </a>

            </div>

        </div>

        <div class="admin-grupo">

            <span>
                Jogos ▾
            </span>

            <div class="admin-menu">

                <a href="<?= BASE_URL ?>/app/jogos_create.php">
                    Cadastrar jogo
                </a>

                <a href="<?= BASE_URL ?>/app/jogos_select.php">
                    Relatório de jogos
                </a>

            </div>

        </div>

        <a href="<?= BASE_URL ?>/jogos.php">
            Ver o site
        </a>

    </nav>

<?php endif; ?>