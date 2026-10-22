<?php

require_once __DIR__ . '/config.php';
//É ele que executa o session_start(). Sem ele rodar antes de qualquer outra coisa, as variáveis de sessão ($_SESSION) simplesmente não existiriam.

$logado = !empty($_SESSION['id']);
//Verifica se a variável de sessão id existe e não está vazia (!empty). Se o usuário fez login com sucesso, ele tem um ID guardado na sessão, logo $logado assume o valor true (verdadeiro). Se ninguém estiver logado, assume false (falso).
$admin  = !empty($_SESSION['admin']);
//O que faz: Verifica se a credencial de administrador está ativa na sessão do usuário atual. Se estiver, $admin vira true; caso contrário, vira false.
// Lista de páginas onde o botão "← Voltar" NÃO deve aparecer no topo
$paginaAtual   = basename($_SERVER['SCRIPT_NAME'] ?? '');
$semVoltar     = ['index.php', 'painel.php', 'decada.php', 'preview_decada.php', 'jogos.php'];
$mostrarVoltar = !in_array($paginaAtual, $semVoltar, true);

// Destino padrão caso o histórico do navegador esteja vazio
$destinoVoltar = BASE_URL . ($admin ? '/app/painel.php' : '/index.php');
?>

<!-- Estilo da logo do menu comum -->
<link rel="stylesheet" href="<?= BASE_URL ?>/style/logo.css">

<div class="cabecalho">
    <?php if ($admin): ?>

        <!-- ====== Menu do Administrador ====== -->
        <?php include __DIR__ . '/admin_nav.php'; ?>

    <?php else: ?>

        <!-- ====== Menu Comum do Site ====== -->
        <header class="topo">
            <nav class="navbar">

                <a class="logo" href="<?= BASE_URL ?>/index.php" aria-label="game.erah, página inicial">
                    <img class="logo-img" src="<?= BASE_URL ?>/img/logo.png" alt="game.erah">
                </a>

                <div class="menu">
                    <a href="<?= BASE_URL ?>/index.php">Início</a>
                    <a href="<?= BASE_URL ?>/jogos.php">Jogos</a>
                </div>

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

    <?php endif; ?>
</div>

<?php if ($mostrarVoltar): ?>
    <div class="barra-voltar">
        <!-- Mostra apenas a seta dentro do círculo perfeito -->
        <a class="btn-voltar" href="<?= $destinoVoltar ?>"
           onclick="if (history.length > 1) { history.back(); return false; }"
           aria-label="Voltar" title="Voltar">←</a>
    </div>
<?php endif; ?>