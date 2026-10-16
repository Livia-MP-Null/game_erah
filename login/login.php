<?php
// ======================================================================
// login.php  |  ENTRAR NO SITE
//
// Segurança aplicada:
//   - token CSRF no formulário
//   - a mesma mensagem para "e-mail não existe" e "senha errada"
//     (assim ninguém descobre quais e-mails estão cadastrados)
//   - bloqueio temporário depois de várias tentativas erradas
//   - conta desativada (ativo = não) não consegue entrar
//   - o ID da sessão é trocado ao entrar (impede "fixação de sessão")
//   - detalhes técnicos de erro vão só para o log, nunca para a tela
// ======================================================================

// Configuração geral: define BASE_URL e INICIA a sessão antes de qualquer HTML.
require_once __DIR__ . '/../includes/config.php';

// Conexão com o banco (cria a variável $conexao).
require_once __DIR__ . '/../database/conect.php';

// Ferramentas de segurança (escapar, CSRF, redirecionar...).
require_once __DIR__ . '/../includes/seguranca.php';

// Funções de usuários (aqui usamos usuario_buscar_por_email).
require_once __DIR__ . '/../includes/functions_usuarios.php';

// Cabeçalhos HTTP de proteção.
cabecalhos_seguranca();

// Limites do bloqueio por tentativas erradas (mude aqui se quiser).
const LOGIN_MAX_TENTATIVAS    = 5;     // erros seguidos permitidos
const LOGIN_BLOQUEIO_SEGUNDOS = 300;   // tempo de bloqueio (300 s = 5 minutos)

// Quem já está logado não precisa ver a tela de login: manda para o lugar certo.
if (!empty($_SESSION['id'])) {
    redirecionar(BASE_URL . (!empty($_SESSION['admin']) ? '/app/painel.php' : '/index.php'));
}

// Mensagem de erro a mostrar (começa sem erro).
$erro = "";

// E-mail que volta para dentro do campo se der erro (a senha nunca volta).
$emailDigitado = "";

// Este bloco só roda quando o formulário foi ENVIADO (método POST).
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        // 1) Confere o token CSRF do formulário.
        csrf_verificar();

        // Hora atual (em segundos), usada no bloqueio por tentativas.
        $agora = time();

        // 2) Está bloqueado por muitas tentativas erradas?
        $bloqueadoAte = (int) ($_SESSION['login_bloqueado_ate'] ?? 0);

        if ($bloqueadoAte > $agora) {
            // ceil arredonda para cima: faltando 61 segundos, mostra "2 minuto(s)".
            $minutos = (int) ceil(($bloqueadoAte - $agora) / 60);
            throw new ErroValidacao('Muitas tentativas. Tente de novo em ' . $minutos . ' minuto(s).');
        }

        // 3) O banco precisa estar acessível.
        if (!isset($conexao) || !($conexao instanceof PDO)) {
            throw new ErroValidacao('Sistema indisponível no momento. Tente novamente mais tarde.');
        }

        // 4) Lê o e-mail (sem espaços e em minúsculas) e a senha (que NÃO leva trim).
        $email = mb_strtolower(trim(ler_post('email')));
        $senha = ler_post('senha');

        // Guarda o e-mail para devolvê-lo ao campo (só se tiver um tamanho razoável).
        $emailDigitado = mb_strlen($email) <= EMAIL_MAX ? $email : '';

        // 5) Campos vazios.
        if ($email === '' || $senha === '') {
            throw new ErroValidacao('Informe o e-mail e a senha.');
        }

        // 6) Procura o usuário pelo e-mail. Se o e-mail for gigante, nem consulta o banco.
        $usuario = false;
        if (mb_strlen($email) <= EMAIL_MAX) {
            $usuario = usuario_buscar_por_email($conexao, $email);
        }

        // 7) Confere a senha.
        $senhaOk = false;

        if ($usuario && strlen($senha) <= 1000) {
            // password_verify compara a senha digitada com o hash guardado no banco.
            $senhaOk = password_verify($senha, $usuario['senha']);
        } else {
            // O e-mail não existe: gastamos o mesmo tempo que uma conferência de verdade.
            // Sem isso, a resposta mais rápida revelaria que o e-mail não está cadastrado.
            password_hash(substr($senha, 0, 72), PASSWORD_DEFAULT);
        }

        // 8) Senha errada ou e-mail inexistente: MESMA mensagem para os dois casos.
        if (!$senhaOk) {

            // Soma uma tentativa errada.
            $_SESSION['login_tentativas'] = (int) ($_SESSION['login_tentativas'] ?? 0) + 1;

            // Passou do limite: bloqueia por alguns minutos e zera o contador.
            if ($_SESSION['login_tentativas'] >= LOGIN_MAX_TENTATIVAS) {
                $_SESSION['login_bloqueado_ate'] = $agora + LOGIN_BLOQUEIO_SEGUNDOS;
                $_SESSION['login_tentativas']    = 0;
            }

            throw new ErroValidacao('E-mail ou senha incorretos.');
        }

        // 9) Senha certa, mas a conta está desativada (ativo = Não).
        //    (Só dizemos isso DEPOIS de a senha estar certa, para não revelar contas a estranhos.)
        if (array_key_exists('ativo', $usuario) && $usuario['ativo'] !== null && !pg_para_bool($usuario['ativo'])) {
            throw new ErroValidacao('Esta conta está desativada. Procure o administrador do site.');
        }

        // 10) Tudo certo: o login é válido.

        // Troca o ID da sessão por um novo (impede o ataque de "fixação de sessão").
        session_regenerate_id(true);

        // Apaga os contadores de erro e o token antigo (um novo é criado na próxima página).
        unset($_SESSION['login_tentativas'], $_SESSION['login_bloqueado_ate'], $_SESSION['csrf']);

        // Guarda na sessão quem entrou.
        $_SESSION['id']    = (int) $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nome']  = $usuario['nome'];
        $_SESSION['admin'] = pg_para_bool($usuario['admin']);

        // Admin vai para o painel; usuário comum vai para a página inicial.
        redirecionar(BASE_URL . ($_SESSION['admin'] ? '/app/painel.php' : '/index.php'));

    } catch (ErroValidacao $e) {
        // Erro causado pela pessoa: a mensagem pode ser mostrada.
        $erro = $e->getMessage();

    } catch (Throwable $e) {
        // Erro interno: detalhes só no log (terminal do servidor).
        error_log('login.php: ' . $e->getMessage());

        // Mensagem genérica na tela.
        $erro = 'Não foi possível entrar agora. Tente novamente.';
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


                <!-- MENSAGEM DE ERRO (escapar() protege o texto) -->
                <?php if ($erro != "") { ?>

                    <p class="auth-erro">
                        <?= escapar($erro) ?>
                    </p>

                <?php } ?>


                <!-- FORMULÁRIO -->
                <form class="auth-form" method="POST">

                    <!-- Token CSRF escondido -->
                    <?= csrf_campo() ?>

                    <!-- E-MAIL -->
                    <label for="email">
                        E-mail
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Digite seu e-mail"
                        maxlength="<?= EMAIL_MAX ?>"
                        autocomplete="username"
                        value="<?= escapar($emailDigitado) ?>"
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
                            maxlength="1000"
                            autocomplete="current-password"
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


                    <!-- OPÇÕES (ainda não funcionam: "Lembrar de mim" e "Esqueceu a senha") -->
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
