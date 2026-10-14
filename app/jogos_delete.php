<?php

// Página sem HTML: apaga o jogo e volta para o relatório.

// O ID chega por POST, vindo do botão "Apagar" de jogos_select.php.

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';


// Só aceita POST.
// Quem abrir a URL diretamente é mandado de volta.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: jogos_select.php");
    exit();

}


$id = (int) ($_POST['id'] ?? 0);


try {

    if ($id <= 0) {

        throw new Exception("ID de jogo inválido.");

    }


    if (jogo_apagar($conexao, $id)) {

        header("Location: jogos_select.php?ok=apagado");

    } else {

        // O jogo não existia ou já tinha sido apagado.

        header("Location: jogos_select.php?ok=nao_encontrado");

    }


} catch (Exception $e) {

    // Registra o erro no terminal do servidor.

    error_log(
        "Erro ao apagar o jogo $id: " . $e->getMessage()
    );


    // Guarda a mensagem para o relatório mostrar.

    $_SESSION['erro_jogos'] =
        "Não foi possível apagar o jogo: " . $e->getMessage();


    header("Location: jogos_select.php");

}


exit();