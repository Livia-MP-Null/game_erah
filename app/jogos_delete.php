<?php



// Página sem HTML: apaga o jogo e volta para o relatório.

// O ID chega por POST, vindo do botão "Apagar" de jogos_select.php.

// Funções gerais e conexão com o banco.
require_once __DIR__ . '/../includes/functions.php';

// Só administrador entra (também inicia a sessão).
require_once __DIR__ . '/../includes/verifica_admin.php';

// Funções dos jogos (jogo_apagar...).
require_once __DIR__ . '/../includes/functions_jogos.php';


// Só aceita POST.
// Quem abrir a URL diretamente (digitando o endereço) é mandado de volta.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: jogos_select.php");
    exit();

}


// Lê o ID enviado pelo campo escondido do formulário.
// (int) transforma em número inteiro: um texto estranho vira 0.
$id = (int) ($_POST['id'] ?? 0);


try {

    // ID zero ou negativo não existe.
    if ($id <= 0) {

        throw new Exception("ID de jogo inválido.");

    }


    // jogo_apagar() devolve true se apagou e false se o jogo não existe.
    if (jogo_apagar($conexao, $id)) {

        header("Location: jogos_select.php?ok=apagado");

    } else {

        // O jogo não existia ou já tinha sido apagado.
        header("Location: jogos_select.php?ok=nao_encontrado");

    }


} catch (Exception $e) {

    // Registra o erro no terminal do servidor (onde o php -S está rodando).
    error_log(
        "Erro ao apagar o jogo $id: " . $e->getMessage()
    );


    // Guarda a mensagem para o relatório mostrar em vermelho.
    $_SESSION['erro_jogos'] =
        "Não foi possível apagar o jogo: " . $e->getMessage();


    header("Location: jogos_select.php");

}


// Para o script aqui (sem isso o PHP continuaria executando).
exit();