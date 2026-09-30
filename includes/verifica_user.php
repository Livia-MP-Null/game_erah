<?php

require_once __DIR__ . '/../database/conect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: ../login/login.php");
    exit();
}

?>