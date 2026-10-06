<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Não logado → vai para o login
if (empty($_SESSION['id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Logado, mas não é admin → vai para a página inicial
if (empty($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}