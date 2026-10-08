<?php
// Barra do administrador (incluída pelo header.php quando $admin é verdadeiro).
// Para acrescentar uma área nova no futuro, basta adicionar um <a> aqui.
?>
<nav class="admin-bar" aria-label="Área do administrador">

    <span class="admin-bar-titulo">ADMIN</span>

    <a href="<?= BASE_URL ?>/app/painel.php">Início do admin</a>

    <!-- Grupo: usuários -->
    <div class="admin-grupo" tabindex="0">
        <span>Usuários ▾</span>
        <div class="admin-menu">
            <a href="<?= BASE_URL ?>/app/create.php">Cadastrar usuário</a>
            <a href="<?= BASE_URL ?>/app/select.php">Relatório de usuários</a>
            <a href="<?= BASE_URL ?>/app/select_w.php">Consultar usuário</a>
            <a href="<?= BASE_URL ?>/app/update.php">Atualizar usuário</a>
            <a href="<?= BASE_URL ?>/app/delete.php">Excluir usuário</a>
        </div>
    </div>

    <!-- Grupo: jogos -->
    <div class="admin-grupo" tabindex="0">
        <span>Jogos ▾</span>
        <div class="admin-menu">
            <a href="<?= BASE_URL ?>/app/jogos_create.php">Cadastrar jogo</a>
            <a href="<?= BASE_URL ?>/app/jogos_select.php">Relatório de jogos</a>
        </div>
    </div>

    <a href="<?= BASE_URL ?>/jogos.php">Ver o site</a>

</nav>
