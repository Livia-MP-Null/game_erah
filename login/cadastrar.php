<?php

require_once __DIR__ . '/../database/conect.php';
require_once __DIR__ . '/../includes/functions.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar</title>
    <link rel="stylesheet" href="../style/auth.css">
</head>
<body class="auth-body">

    <div class="auth">

        <div class="auth-imagem cadastro"></div>

        <main class="auth-painel">
            <div class="auth-caixa">

                <h1 class="auth-titulo">Bem vindo(a) ao gameverah!</h1>

                <nav class="auth-abas">
                    <a href="login.php">Login</a>
                    <a href="cadastrar.php" class="ativa">Cadastrar</a>
                </nav>

                <form class="auth-form" action="" method="post">

                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Digite seu e-mail"
                        required>

                    <label for="nome">Nome de usuário</label>
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        placeholder="Digite seu nome de usuário"
                        required>

                    <label for="nasc">Nascimento</label>
                    <input
                        type="date"
                        name="nasc"
                        id="nasc"
                        required>

                    <label for="senha">Senha</label>
                    <div class="campo-senha">
                        <input
                            type="password"
                            name="senha"
                            id="senha"
                            placeholder="Digite sua senha"
                            required>
                        <button type="button" class="ver-senha" aria-label="Mostrar senha">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>

                    <div class="auth-acoes">
                        <button type="submit" class="auth-botao">Cadastrar</button>
                    </div>

                </form>

                <?php

                // Só roda quando o formulário for enviado
                if ($_SERVER['REQUEST_METHOD'] == "POST") {

                    $name = $_POST['nome'];
                    $nasc = $_POST['nasc'];
                    $senha = $_POST['senha'];
                    $email = $_POST['email'];

                    // O campo "Ativo" saiu da tela: todo novo usuário entra como ativo (1)
                    $ativo = 1;

                    cadastrar($conexao, $name, $nasc, $senha, $email, $ativo);
                }

                ?>

            </div>
        </main>

    </div>

    <script>
        // Mostra / esconde a senha ao clicar no olhinho
        document.querySelectorAll('.ver-senha').forEach(function (botao) {
            botao.addEventListener('click', function () {
                var campo = botao.parentElement.querySelector('input');
                var mostrando = campo.type === 'text';
                campo.type = mostrando ? 'password' : 'text';
                botao.setAttribute('aria-label', mostrando ? 'Mostrar senha' : 'Esconder senha');
            });
        });
    </script>

</body>

</html>