<?php
// Recebe os cliques de favoritar, curtir, comentar e apagar comentário.
// Não tem HTML: faz o que foi pedido e volta para o jogo na página da década.

require_once __DIR__ . '/includes/config.php';              // BASE_URL + sessão
require_once __DIR__ . '/database/conect.php';              // cria $conexao
require_once __DIR__ . '/includes/functions_jogos.php';
require_once __DIR__ . '/includes/functions_interacao.php';

$jogoId = (int) ($_POST['jogo_id'] ?? 0);
$decada = (int) ($_POST['decada'] ?? 0);

// Para onde voltar: a década (se veio) e o jogo exato (#jogo-N)
$volta = BASE_URL . '/jogos.php';

if ($decada > 0) {
    $volta = BASE_URL . '/decada.php?d=' . $decada . '#jogo-' . $jogoId;
}

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $volta);
    exit();
}

// Precisa estar logado (visitante ou admin)
if (empty($_SESSION['id'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

$usuarioId = (int) $_SESSION['id'];
$ehAdmin   = !empty($_SESSION['admin']);

try {

    switch ($_POST['acao'] ?? '') {

        case 'favoritar':
            alternar_marca($conexao, 'favorito', $usuarioId, $jogoId);
            break;

        case 'curtir':
            alternar_marca($conexao, 'curtida', $usuarioId, $jogoId);
            break;

        case 'comentar':
            comentario_criar($conexao, $usuarioId, $jogoId, $_POST['texto'] ?? '');
            break;

        case 'apagar_comentario':
            comentario_apagar($conexao, (int) ($_POST['comentario_id'] ?? 0), $usuarioId, $ehAdmin);
            break;

        default:
            throw new Exception("Ação inválida.");
    }

} catch (Exception $e) {

    error_log("Erro em interagir.php: " . $e->getMessage());

    // A página da década mostra esta mensagem uma vez
    $_SESSION['erro_interacao'] = $e->getMessage();
}

header("Location: " . $volta);
exit();
