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

// função é definida no arquivo de segurança do sistema Cabeçalhos HTTP de proteção.
//X-Content-Type-Options: evitando truques em que um texto simples é executado indevidamente como um script.   
// X-Frame-Options: DENY: Impede que outro site coloque esta página dentro de um <iframe>, o que protege o sistema contra ataques de clickjacking (tentativas de enganar cliques do administrador).   
// Referrer-Policy: same-origin: Define que o endereço da página anterior só é enviado para o próprio domínio, nunca sendo partilhado com sites externos.   
// Cache-Control: no-store: Garante que páginas que lidam com dados confidenciais ou pessoais não fiquem guardadas na cache do navegador.
cabecalhos_seguranca();

// Limites do bloqueio por tentativas erradas.
const LOGIN_MAX_TENTATIVAS = 10;
const LOGIN_BLOQUEIO_SEGUNDOS = 30;

// Quem já está logado é direcionado para a página certa.
if (!empty($_SESSION['id'])) {
    redirecionar(BASE_URL . (!empty($_SESSION['admin']) ? '/app/painel.php' : '/index.php'));
}
//Cria uma variável vazia chamada $erro. Ela é usada para armazenar mensagens de erro (como "E-mail ou senha incorretos" ou "Muitas tentativas") caso aconteça alguma falha durante o login.
$erro = "";
//Cria uma variável vazia chamada $emailDigitado. Ela serve para guardar o e-mail que a pessoa preencheu no campo do formulário. Se o login falhar por algum motivo, essa variável permite que o e-mail continue preenchido na caixinha da página, evitando que o usuário precise digitar o endereço do zero novamente.
$emailDigitado = "";

// Executado quando o formulário é enviado (POST).
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        // 1) Ela confere se o token de segurança enviado pelo formulário bate exatamente com o token secreto que foi guardado na sessão do usuário. 
        // Se o token estiver errado, ausente ou expirado, a função bloqueia o envio e lança um erro de validação.
        //Ela vem do arquivo de ferramentas de segurança do sistema, localizado em includes/seguranca.php
        csrf_verificar();
        //retorna o chamado timestamp, que é o número total de segundos que se passaram desde 1 de janeiro de 1970 até o momento atual.
        //A variável $agora armazena esse número para controlar o bloqueio temporário por tentativas incorretas de login. O sistema compara o valor de $agora com o tempo limite salvo na sessão ($_SESSION['login_bloqueado_ate']) para descobrir se o tempo de espera do usuário já acabou e se ele pode tentar entrar novamente.
        //A função time() pertence ao núcleo do PHP
        $agora = time();

        // 2) Bloqueio por tentativas erradas.
        $bloqueadoAte = (int) ($_SESSION['login_bloqueado_ate'] ?? 0);
        //cria uma variavel que busca na sessão se existe um horário limite gravado onde o usuário foi bloqueado por errar a senha muitas vezes. Se não houver nenhum bloqueio registrado, ele assume o valor 0.
        if ($bloqueadoAte > $agora) {
            //Aqui o sistema compara o horário em que o bloqueio termina ($bloqueadoAte) com o horário atual ($agora). Se o horário do bloqueio for maior que o atual, significa que o usuário ainda está no período de espera.
            $minutos = (int) ceil(($bloqueadoAte - $agora) / 60);
            //Calcula quantos segundos faltam para acabar o bloqueio ($bloqueadoAte - $agora), divide por 60 para transformar em minutos e arredonda o número para cima (ceil).
            throw new ErroValidacao('Muitas tentativas. Tente de novo em ' . $minutos . ' minuto(s).');
            //Interrompe a tentativa de login imediatamente e gera um erro avisando o usuário que ele precisa esperar alguns minutos antes de tentar de novo.
        }

        // 3) Verifica conexão com o banco.
        if (!isset($conexao) || !($conexao instanceof PDO)) {
            //Verifica se a variável $conexao existe (isset) e se ela é realmente uma conexão válida do tipo PDO
            throw new ErroValidacao('Sistema indisponível no momento. Tente novamente mais tarde.');
            //Se a conexão não existir ou falhar por algum motivo, o script para a execução e exibe uma mensagem amigável avisando que o sistema está temporariamente fora do ar.
        }

        // 4) Lê o e-mail (sem espaços e em minúsculas) e a senha (que NÃO leva trim)
        //strtolower	Deixa tudo minúsculo

        $email = strtolower(trim(ler_post('email')));
        $senha = ler_post('senha');

        // Guarda o e-mail para devolvê-lo ao campo (só se tiver um tamanho razoável).
        //strlen() conta e retorna a quantidade de caracteres
        $emailDigitado = strlen($email) <= EMAIL_MAX ? $email : '';

        // 5) Campos vazios se tiver manda a galera inserir.
        if ($email === '' || $senha === '') {
            throw new ErroValidacao('Informe o e-mail e a senha.');
        }

        // 6) Procura o usuário pelo e-mail.Se for ok passa Se o e-mail for gigante, nem consulta o banco.
        $usuario = false;
        if (strlen($email) <= EMAIL_MAX) {
            $usuario = usuario_buscar_por_email($conexao, $email);
            //procurar o registro desse usuário no banco de dados.
        }

        // 7) Confere a senha.
        $senhaOk = false;

        if ($usuario && strlen($senha) <= 1000) {
            $senhaOk = password_verify($senha, $usuario['senha']);
        } else {
            password_hash(substr($senha, 0, 72), PASSWORD_DEFAULT);
        }
        //Mesmo quando o usuário erra ou nem existe, o sistema finge que vai processar a senha e roda essa função pesada de hash no vazio. Assim, o tempo de resposta do servidor é sempre idêntico, não importa se o usuário errou a senha ou se o e-mail nem existe. Isso cega o invasor!

        // 8) Senha errada ou e-mail inexistente.
        if (!$senhaOk) {

            $_SESSION['login_tentativas'] = (int) ($_SESSION['login_tentativas'] ?? 0) + 1;

            if ($_SESSION['login_tentativas'] >= LOGIN_MAX_TENTATIVAS) {
                $_SESSION['login_bloqueado_ate'] = $agora + LOGIN_BLOQUEIO_SEGUNDOS;
                $_SESSION['login_tentativas'] = 0;
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

        $_SESSION['id'] = (int) $usuario['id'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['nome'] = $usuario['nome'];
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
                    <input type="email" name="email" id="email" placeholder="Digite seu e-mail"
                        maxlength="<?= EMAIL_MAX ?>" autocomplete="username" value="<?= escapar($emailDigitado) ?>"
                        required>

                    <!-- SENHA -->
                    <label for="senha">Senha</label>
                    <div class="campo-senha">
                        <input type="password" name="senha" id="senha" placeholder="Digite sua senha" maxlength="1000"
                            autocomplete="current-password" required>

                        <button type="button" class="ver-senha" aria-label="Mostrar senha">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
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
    <!-- O que acontece passo a passo:
document.querySelectorAll('.ver-senha').forEach(...)

O que faz: O JavaScript varre a página inteira procurando por todos os botões que tenham a classe ver-senha (o ícone do olhinho). Se houver mais de um (por exemplo, no login ou no cadastro), ele aplica a regra para todos eles.

botao.addEventListener('click', ...)

O que faz: Ele coloca um "vigia" em cima de cada botão. Ele fica esperando o usuário dar um clique no ícone. Quando o clique acontece, o código de dentro é acionado na hora.

var campo = botao.parentElement.querySelector('input');

O que faz: O botão precisa saber qual caixinha de texto ele deve revelar. O parentElement sobe um nível na estrutura HTML (o container que abraça o input e o botão) e o querySelector('input') acha exatamente a caixinha de senha que está ali do lado.

var mostrando = campo.type === 'text';

O que faz: Cria uma pergunta lógica: "Será que o tipo atual do campo é texto (text)?"

Se a senha estiver visível, essa variável guarda true (verdadeiro).

Se a senha estiver escondida (tipo padrão password), ela guarda false (falso).

campo.type = mostrando ? 'password' : 'text';

O que faz: É o interruptor (liga/desliga)!

Se a senha estava aparecendo (mostrando era verdadeiro), ele troca o tipo para password (escondendo os caracteres com bolinhas).

Se a senha estava escondida, ele troca o tipo para text (revelando a senha em texto puro).

botao.setAttribute('aria-label', ...)

O que faz: Atualiza a descrição de acessibilidade do botão para leitores de tela (usados por pessoas cegas ou com baixa visão), alternando o aviso entre "Mostrar senha" e "Esconder senha" conforme o estado atual. -->
    <script>
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