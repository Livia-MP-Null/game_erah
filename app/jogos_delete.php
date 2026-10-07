<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        jogo_apagar($conexao, $id);
    }

    header("Location: jogos_select.php?ok=apagado");
    exit();
}

header("Location: jogos_select.php");
exit();
