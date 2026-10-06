<?php

require_once __DIR__ . '/../includes/verifica_admin.php';

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
        <h1>Painel do administrador</h1>

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
                <a href="../jogos.php">Ver jogos como visitante</a>
            </section>

        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
