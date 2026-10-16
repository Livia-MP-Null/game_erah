<?php
// ======================================================================
// update.php  |  ATUALIZAR USUÁRIO  (somente administrador)
//
// Pode ser usada quantas vezes for preciso. Segurança:
//   - só admin; token CSRF; o ID precisa existir
//   - nome, e-mail e nascimento são obrigatórios e validados
//   - senha em branco = mantém a senha atual
//   - o e-mail não pode pertencer a OUTRO usuário
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

// Lista de erros a mostrar.
$erros = [];

// Valores do formulário (começam vazios).
$dados = ['id' => '', 'nome' => '', 'email' => '', 'nasc' => ''];

// ---- Pré-preenchimento: o relatório tem um link "Editar" que abre esta
// página assim: update.php?id=5. Aí o formulário já vem com os dados atuais.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['id'])) {

    // Confere se o ID da URL é um número válido (ou null).
    $idUrl = normalizar_id(ler_get('id'));

    // Se o ID é válido, procura o usuário no banco.
    $encontrado = $idUrl ? usuario_buscar($conexao, $idUrl) : false;

    if ($encontrado) {
        // Achou: preenche o formulário com os dados atuais.
        $dados = [
            'id'    => (string) $encontrado['id'],
            'nome'  => $encontrado['nome'],
            'email' => $encontrado['email'],
            'nasc'  => $encontrado['nasc'],
        ];
    } else {
        // ID inválido ou inexistente: avisa.
        $erros[] = 'Não existe usuário com o ID informado.';
    }
}

// ---- Envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // 1) Confere o token CSRF.
        csrf_verificar();

        // 2) Confere o ID. normalizar_id devolve um número inteiro ou null.
        $id = normalizar_id(ler_post('id'));

        // Se o ID é inválido (vazio, letras, negativo...), lança um erro.
        if ($id === null) {
            throw new ErroValidacao('Informe um ID válido (número inteiro maior que zero).');
        }

        // 3) O ID precisa existir no banco.
        if (!usuario_buscar($conexao, $id)) {
            throw new ErroValidacao('Não existe usuário com o ID ' . $id . '.');
        }

        // 4) Lê e limpa os campos. O ID fica guardado para o formulário.
        $dados = usuario_normalizar([
            'nome'  => ler_post('nome'),
            'email' => ler_post('email'),
            'senha' => ler_post('senha'),
            'nasc'  => ler_post('nasc'),
        ]);
        $dados['id'] = (string) $id;

        // 5) Valida os campos. "false, false" = senha opcional e "ativo" não é usado aqui.
        $erros = usuario_erros_validacao($dados, false, false);

        // 6) O e-mail não pode ser de outro usuário (o próprio usuário pode manter o dele).
        if (!$erros && usuario_email_em_uso($conexao, $dados['email'], $id)) {
            $erros[] = 'Este e-mail já pertence a outro usuário.';
        }

        // 7) Sem erros: atualiza no banco.
        if (!$erros) {

            usuario_atualizar($conexao, $id, $dados);

            // Mensagem de sucesso para a próxima tela.
            flash_definir('ok', 'Usuário ' . $id . ' atualizado com sucesso.');

            // Redireciona (evita reenvio com F5). Pode atualizar de novo quantas vezes quiser.
            redirecionar('update.php?id=' . $id);
        }

    } catch (ErroValidacao $e) {
        // Erro causado pela pessoa: mostra a mensagem.
        $erros[] = $e->getMessage();

    } catch (Throwable $e) {
        // Erro interno: detalhes só no log.
        error_log('update.php: ' . $e->getMessage());

        // Mensagem genérica na tela.
        $erros[] = 'Não foi possível atualizar agora. Tente novamente.';
    }
}

// A senha nunca volta para o formulário.
unset($dados['senha']);

// Mensagem "flash" (sucesso vinda do redirect).
$flash = flash_pegar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Os três arquivos de estilo do site (mesmos das suas páginas originais) -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/adm_style.css">
    <title>Atualizar usuário</title>
</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Atualiza dados:</h1>

        <!-- Mensagem de sucesso -->
        <?php if ($flash): ?>
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>"><?= escapar($flash['texto']) ?></p>
        <?php endif; ?>

        <!-- Mensagens de erro -->
        <?php foreach ($erros as $erro): ?>
            <p class="msg-erro"><?= escapar($erro) ?></p>
        <?php endforeach; ?>

        <form action="" method="post" class="meu-formulario">

            <!-- Token CSRF -->
            <?= csrf_campo() ?>

            <div class="campo">
                <label for="id">ID:</label>
                <input type="number" name="id" id="id" required min="1" step="1"
                       placeholder="Insira o ID para atualizar"
                       value="<?= escapar($dados['id']) ?>">
            </div>

            <div class="campo">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required
                       minlength="<?= NOME_MIN ?>" maxlength="<?= NOME_MAX ?>"
                       value="<?= escapar($dados['nome']) ?>">
            </div>

            <div class="campo">
                <!-- Aqui a senha é OPCIONAL: em branco, a senha atual é mantida -->
                <label for="senha">Nova senha (deixe em branco para manter a atual):</label>
                <input type="password" name="senha" id="senha"
                       minlength="<?= SENHA_MIN ?>" maxlength="<?= SENHA_MAX ?>"
                       autocomplete="new-password">
            </div>

            <div class="campo">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required
                       maxlength="<?= EMAIL_MAX ?>"
                       value="<?= escapar($dados['email']) ?>">
            </div>

            <div class="campo">
                <label for="nasc">Nascimento:</label>
                <input type="date" name="nasc" id="nasc" required
                       min="1900-01-01" max="<?= date('Y-m-d') ?>"
                       value="<?= escapar($dados['nasc']) ?>">
            </div>

            <div class="botoes">
                <input type="submit" value="Atualizar">
                <input type="reset" value="Limpar">
            </div>

        </form>

    </main>

    <!-- Rodapé do site -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
