<?php

// Importa o arquivo responsável pela conexão com o banco de dados
require_once __DIR__ . '/../database/conect.php';

// Importa as funções utilizadas no sistema, incluindo a função cadastrar()
require_once __DIR__ . '/../includes/functions.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    
    <meta charset="UTF-8">

   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <title>Cadastrar</title>

    <!-- Importa o arquivo CSS responsável pelo estilo da página de autenticação -->
    <link rel="stylesheet" href="../style/auth.css">

</head>

<body class="auth-body">

    <!-- Container principal da tela de autenticação -->
    <div class="auth">

        <!-- Área onde será exibida a imagem da tela de cadastro -->
        <div class="auth-imagem cadastro"></div>

        <!-- Área principal que contém o formulário -->
        <main class="auth-painel">

            <!-- Caixa que organiza o conteúdo do cadastro -->
            <div class="auth-caixa">

                <!-- Título apresentado ao usuário -->
                <h1 class="auth-titulo">Bem vindo(a) ao gameverah!</h1>

                <!-- Navegação entre as páginas de Login e Cadastro -->
                <nav class="auth-abas">

                    <!-- Link que leva para a página de login -->
                    <a href="login.php">Login</a>

                    <!-- Link da página atual, marcada com a classe "ativa" -->
                    <a href="cadastrar.php" class="ativa">Cadastrar</a>

                </nav>

                <!--
                    Formulário de cadastro.
                    action="" faz com que os dados sejam enviados para esta mesma página.
                    method="post" envia os dados através do método POST.
                -->
                <form class="auth-form" action="" method="post">

                    <!-- Identifica o campo de e-mail -->
                    <label for="email">E-mail</label>

                    <!--
                        Campo para o usuário informar o e-mail.
                        type="email" faz uma validação básica de e-mail.
                        name="email" permite que o PHP receba o valor através de $_POST.
                        required torna o preenchimento obrigatório.
                    -->
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Digite seu e-mail"
                        required>

                    <!-- Identifica o campo do nome de usuário -->
                    <label for="nome">Nome de usuário</label>

                    <!--
                        Campo para o usuário informar seu nome.
                        type="text" indica que será digitado um texto.
                    -->
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        placeholder="Digite seu nome de usuário"
                        required>

                    <!-- Identifica o campo da data de nascimento -->
                    <label for="nasc">Nascimento</label>

                    <!--
                        Campo para selecionar a data de nascimento.
                        type="date" cria um seletor de data.
                    -->
                    <input
                        type="date"
                        name="nasc"
                        id="nasc"
                        required>

                    <!-- Identifica o campo da senha -->
                    <label for="senha">Senha</label>

                    <!-- Container que agrupa o campo de senha e o botão de mostrar senha -->
                    <div class="campo-senha">

                        <!--
                            Campo onde o usuário digita a senha.
                            type="password" esconde os caracteres digitados.
                        -->
                        <input
                            type="password"
                            name="senha"
                            id="senha"
                            placeholder="Digite sua senha"
                            required>

                        <!--
                            Botão utilizado para mostrar ou esconder a senha.
                            type="button" impede que ele envie o formulário.
                        -->
                        <button
                            type="button"
                            class="ver-senha"
                            aria-label="Mostrar senha">

                            <!-- Ícone de olho usado no botão de mostrar senha -->
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <!-- Desenha o formato do olho -->
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />

                                <!-- Desenha a parte central do olho -->
                                <circle cx="12" cy="12" r="3" />

                            </svg>

                        </button>

                    </div>

                    <!-- Área que contém o botão de cadastro -->
                    <div class="auth-acoes">

                        <!--
                            Botão responsável por enviar o formulário.
                            type="submit" faz com que os dados sejam enviados.
                        -->
                        <button type="submit" class="auth-botao">
                            Cadastrar
                        </button>

                    </div>

                </form>

                <?php

                // Verifica se o formulário foi enviado utilizando o método POST
                if ($_SERVER['REQUEST_METHOD'] == "POST") {

                    // Pega o nome enviado pelo formulário
                    $name = $_POST['nome'];

                    // Pega a data de nascimento enviada pelo formulário
                    $nasc = $_POST['nasc'];

                    // Pega a senha enviada pelo formulário
                    $senha = $_POST['senha'];

                    // Pega o e-mail enviado pelo formulário
                    $email = $_POST['email'];

                    // Define o novo usuário como ativo
                    $ativo = 1;

                    // Chama a função cadastrar() para salvar o usuário no banco de dados
                    cadastrar($conexao, $name, $nasc, $senha, $email,);

                }

                ?>

            </div>

        </main>

    </div>

    <script>

        // Procura todos os botões que possuem a classe "ver-senha"
        document.querySelectorAll('.ver-senha').forEach(function (botao) {

            // Adiciona um evento que será executado quando o botão for clicado
            botao.addEventListener('click', function () {

                // Encontra o campo de senha que está dentro do mesmo elemento pai do botão
                var campo = botao.parentElement.querySelector('input');

                // Verifica se a senha está atualmente sendo mostrada
                var mostrando = campo.type === 'text';

                // Se a senha estiver visível, esconde.
                // Se estiver escondida, mostra.
                campo.type = mostrando ? 'password' : 'text';

                // Atualiza o texto de acessibilidade do botão
                // de acordo com o estado atual da senha
                botao.setAttribute(
                    'aria-label',
                    mostrando ? 'Mostrar senha' : 'Esconder senha'
                );

            });

        });

    </script>

</body>

</html>