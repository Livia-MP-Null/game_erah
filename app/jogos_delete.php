<?php
// Sem HTML: só apaga e volta para o relatório. Recebe o ID por POST.

require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    jogo_apagar($conexao, (int) ($_POST['id'] ?? 0));
    header("Location: jogos_select.php?ok=apagado");
} else {
    header("Location: jogos_select.php");
}
exit();
