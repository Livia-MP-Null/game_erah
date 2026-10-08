<?php
// Página sem HTML: apaga o jogo e volta para o relatório.
// O ID chega por POST, vindo do botão "Apagar" de jogos_select.php.

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';   // (já inicia a sessão)
require_once __DIR__ . '/../includes/functions_jogos.php';

// Só aceita POST. Quem abrir a URL direto é mandado de volta.
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
        // O jogo não existia (por exemplo, já tinha sido apagado)
        header("Location: jogos_select.php?ok=nao_encontrado");
    }

} catch (Exception $e) {

    // Registra no terminal do servidor (onde o "php -S" está rodando)...
    error_log("Erro ao apagar o jogo $id: " . $e->getMessage());

    // ...e guarda o motivo para o relatório mostrar em vermelho.
    // (Esta página é só de admin; no site público, mostre uma mensagem genérica.)
    $_SESSION['erro_jogos'] = "Não foi possível apagar o jogo: " . $e->getMessage();

    header("Location: jogos_select.php");
}

exit();
