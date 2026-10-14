<?php

session_start();

require_once __DIR__ . '/../database/conect.php';
require_once __DIR__ . '/../includes/functions.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = consulta_user($conexao, $email);

    if ($usuario) {

        if (password_verify($senha, $usuario['senha'])) {

            session_regenerate_id(true);

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['admin'] = (bool) $usuario['admin'];

            if ($_SESSION['admin']) {
                header("Location: ../app/select.php");
            } else {
                header("Location: ../index.php");
            }

            exit();

        } else {

            $erro = "Senha incorreta.";
        }

    } else {

        $erro = "E-mail não cadastrado.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - gameverah</title>

    <link rel="stylesheet" href="../style/auth.css">

</head>

<body class="auth-body">

    <div class="auth">

        <!-- IMAGEM DO LADO ESQUERDO -->
        <div class="auth-imagem"></div>


        <!-- PAINEL DO LOGIN -->
        <main class="auth-painel">

            <div class="auth-caixa">

                <!-- TÍTULO -->
                <h1 class="auth-titulo">
                    Bem vindo(a) ao gameverah!
                </h1>


                <!-- ABAS LOGIN / CADASTRAR -->
                <nav class="auth-abas">

                    <a href="login.php" class="ativa">
                        Login
                    </a>

                    <a href="cadastrar.php">
                        Cadastrar
                    </a>

                </nav>


                <!-- MENSAGEM DE ERRO -->
                <?php if ($erro != "") { ?>

                    <p class="auth-erro">
                        <?php echo htmlspecialchars($erro); ?>
                    </p>

                <?php } ?>


                <!-- FORMULÁRIO -->
                <form class="auth-form" method="POST">

                    <!-- E-MAIL -->
                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Digite seu e-mail"
                        required
                    >


                    <!-- SENHA -->
                    <label for="senha">
                        Senha
                    </label>

                    <div class="campo-senha">

                        <input
                            type="password"
                            name="senha"
                            id="senha"
                            placeholder="Digite sua senha"
                            required
                        >

                        <button
                            type="button"
                            class="ver-senha"
                            aria-label="Mostrar senha"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />

                                <circle cx="12" cy="12" r="3" />

                            </svg>

                        </button>

                    </div>


                    <!-- OPÇÕES -->
                    <div class="auth-opcoes">

                        <label>

                            <input
                                type="checkbox"
                                name="lembrar"
                            >

                            Lembrar de mim

                        </label>


                        <a href="#">
                            Esqueceu a senha?
                        </a>

                    </div>


                    <!-- BOTÃO ENTRAR -->
                    <div class="auth-acoes">

                        <button
                            type="submit"
                            class="auth-botao"
                        >
                            Entrar
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>


    <!-- MOSTRAR / ESCONDER SENHA -->
    <script>

        document.querySelectorAll('.ver-senha').forEach(function (botao) {

            botao.addEventListener('click', function () {

                var campo = botao.parentElement.querySelector('input');

                var mostrando = campo.type === 'text';

                campo.type = mostrando ? 'password' : 'text';

                botao.setAttribute(
                    'aria-label',
                    mostrando ? 'Mostrar senha' : 'Esconder senha'
                );

            });

        });

    </script>

</body>

</html>