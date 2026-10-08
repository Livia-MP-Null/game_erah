<?php

require_once __DIR__ . '/config.php';              // BASE_URL + sessão
require_once __DIR__ . '/../database/conect.php';  // cria $conexao

// Qualquer pessoa logada (visitante ou admin) passa. Quem não está, vai para o login.
if (empty($_SESSION['id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}
