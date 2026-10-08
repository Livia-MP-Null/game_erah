<?php

require_once __DIR__ . '/config.php';              // BASE_URL + sessão
require_once __DIR__ . '/../database/conect.php';  // cria $conexao

// Não logado: vai para o login
if (empty($_SESSION['id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// Logado, mas não é admin: vai para a página inicial
if (empty($_SESSION['admin'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
