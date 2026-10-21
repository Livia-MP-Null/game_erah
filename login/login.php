<?php
// ======================================================================
// login.php  |  ENTRAR NO SITE
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

// Limites do bloqueio por tentativas erradas.
const LOGIN_MAX_TENTATIVAS    = 5;
const LOGIN_BLOQUEIO_SEGUNDOS = 300; // 5 minutos

// Quem já está logado é direcionado para a página certa.
if (!empty($_SESSION['id'])) {
    redirecionar(BASE_URL . (!empty($_SESSION['admin']) ? '/app/painel.php' : '/index.php'));
}

$erro = "";
$emailDigitado = "";

// Executado quando o formulário é enviado (POST).
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        // 1) Confere o token CSRF do formulário.
        csrf_verificar();

        $agora = time();

        // 2) Bloqueio por tentativas erradas.
        $bloqueadoAte = (int) ($_SESSION['login_bloqueado_ate'] ?? 0);

        if ($bloqueadoAte > $agora) {
            $minutos = (int) ceil(($bloqueadoAte - $agora) / 60);
            throw new ErroValidacao('Muitas tentativas. Tente de novo em ' . $minutos . ' minuto(s).');
        }

        // 3) Verifica conexão com o banco.
        if (!isset($conexao) || !($conexao instanceof PDO)) {
            throw new ErroValidacao('Sistema indisponível no momento. Tente novamente mais tarde.');
        }

        // 4) Lê o e-mail (sem espaços e em minúsculas) e a senha (que NÃO leva trim).
        $email = strtolower(trim(ler_post('email')));
        $senha = ler_post('senha');

        // Guarda o e-mail para devolvê-lo ao campo (só se tiver um tamanho razoável).
        $emailDigitado = strlen($email) <= EMAIL_MAX ? $email : '';

        // 5) Campos vazios.
        if ($email === '' || $senha === '') {
            throw new ErroValidacao('Informe o e-mail e a senha.');
        }

        // 6) Procura o usuário pelo e-mail. Se o e-mail for gigante, nem consulta o banco.
        $usuario = false;
        if (strlen($email) <= EMAIL_MAX) {
            $usuario = usuario_buscar_por_email($conexao, $email);
        }

        // 7) Confere a senha.
        $senhaOk = false;

        if ($usuario && strlen($senha) <= 1000) {
            $senhaOk = password_verify($senha, $usuario['senha']);
        } else {
            password_hash(substr($senha, 0, 72), PASSWORD_DEFAULT);
        }

        // 8) Senha errada ou e-mail inexistente.
        if (!$senhaOk) {

            $_SESSION['login_tentativas'] = (int) ($_SESSION['login_tentativas'] ?? 0) + 1;

            if ($_SESSION['login_tentativas'] >= LOGIN_MAX_TENTATIVAS) {
                $_SESSION['login_bloqueado_ate'] = $agora + LOGIN_BLOQUEIO_SEGUNDOS;
                $_SESSION['login_tentativas']    = 0;
            }

            throw new ErroValidacao('E-mail ou senha incorretos.');
        }

        // 9) Verifica se a conta está ativa.
        if (array_key_exists('ativo', $usuario) && $usuario['ativo'] !== null && !pg_para_bool($usuario['ativo'])) {
            throw new ErroValidacao('Esta conta está desativada. Procure o administrador do site.');
        }

        // 10) Tudo certo: inicia a sessão.
        session_regenerate_id(true);

        unset($_SESSION['login_tentativas'], $_SESSION['login_bloqueado_ate'], $_SESSION['csrf']);

        $_SESSION['id']    = (int) $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nome']  = $usuario['nome'];
        $_SESSION['admin'] = pg_para_bool($usuario['admin']);

        redirecionar(BASE_URL . ($_SESSION['admin'] ? '/app/painel.php' : '/index.php'));
    } catch (ErroValidacao $e) {
        $erro = $e->getMessage();
    } catch (Throwable $e) {
        error_log('login.php: ' . $e->getMessage());

        // Exibe o erro real na tela para diagnóstico no ambiente local/rede:
        $erro = 'Erro no sistema: ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
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
                    <a href="login.php" class="ativa">Login</a>
                    <a href="cadastrar.php">Cadastrar</a>
                </nav>

                <!-- MENSAGEM DE ERRO -->
                <?php if ($erro != "") { ?>
                    <p class="auth-erro">
                        <?= escapar($erro) ?>
                    </p>
                <?php } ?>

                <!-- FORMULÁRIO -->
                <form class="auth-form" action="" method="POST">

                    <!-- Token CSRF escondido -->
                    <?= csrf_campo() ?>

                    <!-- E-MAIL -->
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Digite seu e-mail"
                        maxlength="<?= EMAIL_MAX ?>"
                        autocomplete="username"
                        value="<?= escapar($emailDigitado) ?>"
                        required>

                    <!-- SENHA -->
                    <label for="senha">Senha</label>
                    <div class="campo-senha">
                        <input
                            type="password"
                            name="senha"
                            id="senha"
                            placeholder="Digite sua senha"
                            maxlength="1000"
                            autocomplete="current-password"
                            required>

                        <button
                            type="button"
                            class="ver-senha"
                            aria-label="Mostrar senha">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>

                    <!-- OPÇÕES -->
                    <div class="auth-opcoes">
                        <label>
                            <input type="checkbox" name="lembrar">
                            Lembrar de mim
                        </label>

                        <a href="#">Esqueceu a senha?</a>
                    </div>

                    <!-- BOTÃO ENTRAR -->
                    <div class="auth-acoes">
                        <button type="submit" class="auth-botao">
                            Entrar
                        </button>
                    </div>

                </form>

            </div>

        </main>

    </div>

    <!-- MOSTRAR / ESCONDER SENHA -->
    <script>
        document.querySelectorAll('.ver-senha').forEach(function(botao) {
            botao.addEventListener('click', function() {
                var campo = botao.parentElement.querySelector('input');
                var mostrando = campo.type === 'text';
                campo.type = mostrando ? 'password' : 'text';
                botao.setAttribute('aria-label', mostrando ? 'Mostrar senha' : 'Esconder senha');
            });
        });
    </script>

</body>

</html>