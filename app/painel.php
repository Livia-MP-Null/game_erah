<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';

// Nome para a saudação (o login guarda em $_SESSION['nome']; se não houver, usa "admin")
$nome = $_SESSION['nome'] ?? 'admin';

// Números rápidos. Se algo falhar, a página abre mesmo assim.
$totalJogos    = null;
$totalUsuarios = null;

try {
    $totalJogos    = jogos_total($conexao);
    $totalUsuarios = (int) $conexao->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
} catch (Exception $e) {
    error_log("Painel: não foi possível contar: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/jogos.css">
    <title>Painel do administrador</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Bem-vindo, <?= esc_html($nome) ?>!</h1>
        <p class="sub">
            Você está na área do administrador. Use a barra no topo da página
            para ir a qualquer área, ou os atalhos abaixo.
        </p>

        <!-- Números rápidos -->
        <div class="resumo">

            <div class="resumo-item">
                <strong><?= $totalJogos ?? '—' ?></strong>
                <span>jogos</span>
            </div>

            <div class="resumo-item">
                <strong><?= $totalUsuarios ?? '—' ?></strong>
                <span>usuários</span>
            </div>

        </div>

        <!-- Atalhos -->
        <div class="painel-grade">

            <section class="painel-cartao">
                <h2>Usuários</h2>
                <a href="create.php">Cadastrar usuário</a>
                <a href="select.php">Relatório de usuários</a>
                <a href="select_w.php">Consultar usuário</a>
                <a href="update.php">Atualizar usuário</a>
                <a href="delete.php">Excluir usuário</a>
            </section>

            <section class="painel-cartao">
                <h2>Jogos</h2>
                <a href="jogos_create.php">Cadastrar jogo</a>
                <a href="jogos_select.php">Relatório e lista de jogos</a>
            </section>

            <section class="painel-cartao">
                <h2>Site</h2>
                <a href="../index.php">Página inicial</a>
                <a href="../jogos.php">Jogos por década</a>
            </section>

        </div>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
