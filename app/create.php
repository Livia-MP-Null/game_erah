<?php
// ======================================================================
// create.php  |  CADASTRAR USUÁRIO  (somente administrador)
// ======================================================================

// Importa as configurações do site (define BASE_URL)
require_once __DIR__ . '/../includes/config.php';

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

$erro = '';

// Processa o formulário enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1) Confere o token CSRF
        csrf_verificar();

        // 2) Coleta e limpa os dados do formulário
        $nome  = trim(ler_post('nome'));
        $email = trim(ler_post('email'));
        $senha = ler_post('senha');
        $nasc  = ler_post('nasc');
        $ativo = (ler_post('ativo') === '1');
        $admin = (ler_post('admin') === '1');

        // Validações básicas
        if ($nome === '' || $email === '' || $senha === '' || $nasc === '') {
            throw new ErroValidacao('Preencha todos os campos obrigatórios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ErroValidacao('Informe um e-mail válido.');
        }

        // Estrutura os dados num array para evitar o erro de offset de string
        $dados = [
            'nome'  => $nome,
            'email' => $email,
            'senha' => $senha,
            'nasc'  => $nasc,
            'ativo' => $ativo,
            'admin' => $admin
        ];

        // 3) Chama a função de criação passando o array ou tenta inserção direta
        if (function_exists('usuario_cadastrar')) {
            try {
                usuario_cadastrar($conexao, $dados);
            } catch (Throwable $e) {
                usuario_cadastrar($conexao, $nome, $email, $senha, $nasc, $ativo, $admin);
            }
        } elseif (function_exists('usuario_criar')) {
            try {
                usuario_criar($conexao, $dados);
            } catch (Throwable $e) {
                usuario_criar($conexao, $nome, $email, $senha, $nasc, $ativo, $admin);
            }
        } elseif (function_exists('usuario_inserir')) {
            try {
                usuario_inserir($conexao, $dados);
            } catch (Throwable $e) {
                usuario_inserir($conexao, $nome, $email, $senha, $nasc, $ativo, $admin);
            }
        } else {
            // Inserção direta via PDO como garantia
            $sql = "INSERT INTO usuarios (nome, email, senha, nasc, ativo, admin) 
                    VALUES (:nome, :email, :senha, :nasc, :ativo, :admin)";
            $stmt = $conexao->prepare($sql);
            $stmt->execute([
                ':nome'  => $nome,
                ':email' => $email,
                ':senha' => password_hash($senha, PASSWORD_DEFAULT),
                ':nasc'  => $nasc,
                ':ativo' => $ativo ? 't' : 'f',
                ':admin' => $admin ? 't' : 'f'
            ]);
        }

        // 4) Sucesso: define a mensagem e redireciona para o relatório
        flash_definir('ok', 'Usuário "' . $nome . '" criado com sucesso!');
        redirecionar('select.php');

    } catch (ErroValidacao $e) {
        $erro = $e->getMessage();
    } catch (Throwable $e) {
        error_log('create.php: ' . $e->getMessage());
        $erro = 'Não foi possível cadastrar o usuário. Verifique se o e-mail já existe ou tente novamente.';
    }
}

$flash = flash_pegar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS padronizado do site -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/adm_style.css">

    <title>Cadastrar Usuário</title>
</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-conteudo">

        <div class="admin-titulo">
            <h1>Cadastrar Novo Usuário</h1>
            <p>Preencha os campos abaixo para registrar um novo usuário no sistema.</p>
        </div>

        <!-- Mensagens de retorno -->
        <?php if ($flash): ?>
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>"><?= escapar($flash['texto']) ?></p>
        <?php endif; ?>

        <?php if ($erro): ?>
            <p class="msg-erro"><?= escapar($erro) ?></p>
        <?php endif; ?>

        <section class="admin-card">

            <form action="" method="post" class="admin-form">

                <!-- Token CSRF escondido -->
                <?= csrf_campo() ?>

                <div class="campo">
                    <label for="nome">Nome Completo</label>
                    <input type="text" name="nome" id="nome" value="<?= escapar($_POST['nome'] ?? '') ?>" required placeholder="Digite o nome completo">
                </div>

                <div class="campo">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" value="<?= escapar($_POST['email'] ?? '') ?>" required placeholder="exemplo@email.com">
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" required placeholder="Digite a senha inicial">
                </div>

                <div class="campo">
                    <label for="nasc">Data de Nascimento</label>
                    <input type="date" name="nasc" id="nasc" value="<?= escapar($_POST['nasc'] ?? '') ?>" required>
                </div>

                <div class="campo">
                    <label for="ativo">Status da Conta</label>
                    <select name="ativo" id="ativo" style="width: 100%; padding: 10px; background: #000; color: #fff; border: 1px solid #555; border-radius: 6px; margin-bottom: 18px;">
                        <option value="1" selected>Sim (Ativo)</option>
                        <option value="0">Não (Inativo/Bloqueado)</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="admin">Tipo de Usuário</label>
                    <select name="admin" id="admin" style="width: 100%; padding: 10px; background: #000; color: #fff; border: 1px solid #555; border-radius: 6px; margin-bottom: 18px;">
                        <option value="0" selected>Comum</option>
                        <option value="1">Administrador</option>
                    </select>
                </div>

                <div class="admin-acoes" style="display: flex; gap: 15px; margin-top: 10px;">
                    <button type="submit" class="btn-laranja" style="border: none; cursor: pointer; padding: 10px 22px; font-weight: bold;">
                        Cadastrar
                    </button>

                    <a href="select.php" style="padding: 10px 18px; border: 1px solid #555; border-radius: 20px; color: #fff; text-decoration: none; font-size: 14px;">
                        Ver Relatório
                    </a>
                </div>

            </form>

        </section>

    </main>

    <!-- Rodapé do site -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>