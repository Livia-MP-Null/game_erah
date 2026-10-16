<?php
// ======================================================================
// delete.php  |  APAGAR USUÁRIO  (somente administrador)
//
// Esta página recebe o ID de dois lugares:
//   - do formulário desta própria página (digitando o ID)
//   - do botão "Apagar" do relatório (select.php)
//
// Travas de segurança (em usuario_apagar):
//   - o ID precisa existir
//   - o admin não pode apagar a própria conta
//   - não é possível apagar o último administrador
// Além disso: só aceita POST e exige o token CSRF.
// ======================================================================

// Confere login + administrador e cria a conexão $conexao.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Ferramentas de segurança.
require_once __DIR__ . '/../includes/seguranca.php';

// Funções de usuários.
require_once __DIR__ . '/../includes/functions_usuarios.php';

// Cabeçalhos HTTP de proteção.
cabecalhos_seguranca();

// Para aqui se o banco não estiver acessível.
exigir_conexao($conexao ?? null);

// Só processamos quando o formulário é enviado por POST.
// (Apagar por link/GET seria perigoso: qualquer imagem ou link escondido poderia disparar.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Para onde voltar depois: só aceitamos uma de duas opções conhecidas.
    // (Nunca redirecione para um endereço que veio do usuário sem conferir.)
    $destino = (ler_post('voltar') === 'select') ? 'select.php' : 'delete.php';

    try {
        // 1) Confere o token CSRF.
        csrf_verificar();

        // 2) Confere o ID (inteiro maior que zero) ou null.
        $id = normalizar_id(ler_post('id'));

        // ID inválido: erro com mensagem clara.
        if ($id === null) {
            throw new ErroValidacao('Informe um ID válido (número inteiro maior que zero).');
        }

        // 3) Apaga. A função confere se o ID existe e aplica as travas de segurança.
        //    $_SESSION['id'] é o ID de quem está logado (o administrador).
        usuario_apagar($conexao, $id, (int) $_SESSION['id']);

        // 4) Sucesso: guarda a mensagem para a próxima tela.
        flash_definir('ok', 'Usuário ' . $id . ' apagado com sucesso.');

    } catch (ErroValidacao $e) {
        // Erro causado pela pessoa (ID inexistente, trava de segurança...): mostra a mensagem.
        flash_definir('erro', $e->getMessage());

    } catch (Throwable $e) {
        // Erro interno: detalhes só no log.
        error_log('delete.php: ' . $e->getMessage());

        // Mensagem genérica para a tela.
        flash_definir('erro', 'Não foi possível apagar agora. Tente novamente.');
    }

    // Em qualquer resultado, redireciona (assim F5 não tenta apagar de novo).
    redirecionar($destino);
}

// Mensagem "flash" (resultado da última tentativa de apagar).
$flash = flash_pegar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Os três arquivos de estilo do site (mesmos da sua página original) -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/adm_style.css">

    <title>Excluir usuário</title>

</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-conteudo">

        <div class="admin-titulo">

            <h1>Excluir usuário</h1>

            <p>
                Informe o ID do usuário que deseja excluir.
            </p>

        </div>

        <!-- Resultado da última tentativa (sucesso ou erro) -->
        <?php if ($flash): ?>
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>"><?= escapar($flash['texto']) ?></p>
        <?php endif; ?>

        <section class="admin-card">

            <!-- O onsubmit pede confirmação ANTES de enviar. Fica no formulário (e não no
                 botão) para valer também quando a pessoa aperta Enter no teclado. -->
            <form action="" method="post" class="admin-form"
                  onsubmit="return confirm('Tem certeza que deseja apagar este usuário? Isso não pode ser desfeito.');">

                <!-- Token CSRF escondido (prova que o formulário veio deste site) -->
                <?= csrf_campo() ?>

                <div class="campo">

                    <label for="id">
                        ID do usuário
                    </label>

                    <input
                        type="number"
                        name="id"
                        id="id"
                        min="1"
                        step="1"
                        required
                    >

                </div>

                <div class="admin-acoes">

                    <button type="submit" class="btn-perigo">
                        Apagar usuário
                    </button>

                    <a href="select.php" class="btn-secundario">
                        Consultar usuários
                    </a>

                </div>

            </form>

        </section>

    </main>

    <!-- Rodapé do site -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
