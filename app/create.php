<?php
// ======================================================================
// create.php  |  CADASTRAR USUÁRIO  (somente administrador)
//
// Fluxo de segurança desta página:
//   1. confere se quem acessa é administrador
//   2. confere o token CSRF
//   3. valida TODOS os campos (todos são obrigatórios)
//   4. confere se o e-mail já existe
//   5. grava (senha com hash) e redireciona
// ======================================================================

// Confere se há alguém logado E se é administrador. Caso contrário, manda a
// pessoa embora e para o arquivo aqui. Também cria a conexão $conexao com o banco.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Carrega as ferramentas de segurança (escapar, CSRF, mensagens...).
require_once __DIR__ . '/../includes/seguranca.php';

// Carrega as funções que validam e gravam usuários.
require_once __DIR__ . '/../includes/functions_usuarios.php';

// Envia os cabeçalhos HTTP de proteção.
cabecalhos_seguranca();

// Para a página aqui se o banco de dados não estiver acessível.
exigir_conexao($conexao ?? null);

// Lista onde guardamos as mensagens de erro para mostrar na tela.
$erros = [];



// Valores dos campos. Começam vazios; se houver erro, voltam preenchidos
// para a pessoa não precisar digitar tudo de novo.
$dados = ['nome' => '', 'email' => '', 'nasc' => '', 'ativo' => ''];

// Este bloco só roda quando o formulário foi ENVIADO (método POST).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // 1) Confere o token CSRF. Se for inválido, lança um erro e pula para o "catch".
        csrf_verificar();

        // 2) Lê os campos do formulário (ler_post só aceita texto) e limpa os espaços.
        $dados = usuario_normalizar([
            'nome'  => ler_post('nome'),
            'email' => ler_post('email'),
            'senha' => ler_post('senha'),
            'nasc'  => ler_post('nasc'),
            'ativo' => ler_post('ativo'),
        ]);

        // 3) Valida os campos. "true, true" = senha obrigatória e "ativo" obrigatório.
        $erros = usuario_erros_validacao($dados, true, true);

        // 4) Só consulta o banco se os campos estiverem corretos.
        if (!$erros && usuario_email_em_uso($conexao, $dados['email'])) {
            // Já existe alguém com este e-mail.
            $erros[] = 'Já existe um usuário com este e-mail.';
        }

        // 5) Se não houve nenhum erro, grava no banco.
        if (!$erros) {

            // Cria o usuário (a função aplica o hash na senha).
            usuario_criar($conexao, $dados);

            // Guarda uma mensagem de sucesso para a próxima tela.
            flash_definir('ok', 'Usuário cadastrado com sucesso.');

            // Redireciona para a mesma página. Assim, F5 não cadastra o usuário de novo.
            redirecionar('create.php');
        }

    } catch (ErroValidacao $e) {
        // Erro causado pela pessoa (ex.: token vencido): a mensagem pode ser mostrada.
        $erros[] = $e->getMessage();

    } catch (Throwable $e) {
        // Erro interno: registra os detalhes no log (terminal do servidor)...
        error_log('create.php: ' . $e->getMessage());

        // ...e mostra à pessoa só uma mensagem genérica.
        $erros[] = 'Não foi possível cadastrar agora. Tente novamente.';
    }
}

// Por segurança, a senha nunca volta para o formulário.
unset($dados['senha']);

// A variável $flash serve para armazenar e exibir mensagens temporárias de feedback (como "Usuário cadastrado com sucesso") na tela.
$flash = flash_pegar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Os três arquivos de estilo do site (caminhos relativos: funcionam por IP ou localhost) -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/adm_style.css">

    <title>Cadastrar</title>
</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-conteudo">

        <div class="admin-titulo">

            <h1>Cadastro de usuário</h1>

            <p>Preencha todos os campos. O usuário será cadastrado como usuário comum.</p>

        </div>

        <!-- Mensagem de sucesso (aparece uma vez, depois de cadastrar) -->
        <?php if ($flash): ?>
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>"><?= escapar($flash['texto']) ?></p>
        <?php endif; ?>

        <!-- Mensagens de erro: uma linha para cada erro -->
        <?php foreach ($erros as $erro): ?>
            <p class="msg-erro"><?= escapar($erro) ?></p>
        <?php endforeach; ?>

        <section class="admin-card">

            <form action="" method="post" class="admin-form">

                <!-- Token CSRF escondido (prova que o formulário veio deste site) -->
                <?= csrf_campo() ?>

                <div class="campo">
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" id="nome" required
                           minlength="<?= NOME_MIN ?>" maxlength="<?= NOME_MAX ?>"
                           value="<?= escapar($dados['nome']) ?>">
                </div>

                <div class="campo">
                    <label for="senha">Senha (mínimo <?= SENHA_MIN ?> caracteres):</label>
                    <!-- autocomplete="new-password" evita que o navegador preencha com a senha do admin -->
                    <input type="password" name="senha" id="senha" required
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
                    <!-- max = hoje: o navegador não deixa escolher uma data futura -->
                    <input type="date" name="nasc" id="nasc" required
                           min="1900-01-01" max="<?= date('Y-m-d') ?>"
                           value="<?= escapar($dados['nasc']) ?>">
                </div>

                <div class="campo">
                    <label>Ativo:</label>

                    <!-- "required" no primeiro botão basta para o grupo todo ser obrigatório -->
                    <div>
                        <input type="radio" name="ativo" id="ativo_sim" value="true" required
                               <?= $dados['ativo'] === 'true' ? 'checked' : '' ?>>
                        <label for="ativo_sim">Sim</label>

                        <input type="radio" name="ativo" id="ativo_nao" value="false"
                               <?= $dados['ativo'] === 'false' ? 'checked' : '' ?>>
                        <label for="ativo_nao">Não</label>
                    </div>
                </div>

                <div class="admin-acoes">

                    <button type="submit" class="btn-admin">
                        Cadastrar
                    </button>

                    <a href="select.php" class="btn-secundario">
                        Ver relatório
                    </a>

                </div>

            </form>

        </section>

    </main>

    <!-- Rodapé do site -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
