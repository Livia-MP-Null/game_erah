<?php
// ======================================================================
// seguranca.php
// Ferramentas de segurança usadas pelas páginas de usuários.
//
// O que existe aqui:
//   - ErroValidacao ........ erro cuja mensagem PODE ser mostrada ao usuário
//   - escapar() ............ protege textos antes de mostrá-los (anti-XSS)
//   - cabecalhos_seguranca() cabeçalhos HTTP de proteção
//   - ler_post / ler_get ... leem campos do formulário com segurança
//   - normalizar_id() ...... confere se um ID é um número inteiro válido
//   - csrf_*() ............. token que impede que OUTRO site envie formulários
//                            em nome do admin (ataque CSRF)
//   - flash_*() ............ mensagem que aparece uma vez só, depois do redirect
//   - exigir_conexao() ..... para a página se o banco não estiver acessível
// ======================================================================

// Carrega a configuração geral do site (define BASE_URL e inicia a sessão).
// "require_once" garante que o arquivo é carregado uma única vez.
require_once __DIR__ . '/config.php';


// ----------------------------------------------------------------------
// ErroValidacao
// ----------------------------------------------------------------------
// Existem dois tipos de erro neste sistema:
//   1) Erros que a PESSOA causou (e-mail repetido, ID que não existe...).
//      Usam esta classe, e a mensagem é mostrada na tela.
//   2) Erros internos (banco fora do ar, bug...). Esses NUNCA são mostrados
//      na tela, porque revelam detalhes do sistema para quem quiser atacá-lo.
//      Eles só vão para o log (terminal do servidor).
class ErroValidacao extends Exception
{
    // A classe fica vazia de propósito: ela só serve para "marcar" o tipo do erro.
}


// ----------------------------------------------------------------------
// escapar(): protege um texto antes de colocá-lo no HTML.
// ----------------------------------------------------------------------
// Sem isso, alguém poderia cadastrar o nome <script>...</script> e esse
// código rodaria no navegador de quem abrisse o relatório (ataque XSS).
function escapar($texto)
{
    // (string) garante que o valor é texto (null vira "", número vira "123").
    // ENT_QUOTES troca também as aspas simples e duplas por códigos seguros.
    // 'UTF-8' diz qual é a codificação dos acentos.
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}


// ----------------------------------------------------------------------
// cabecalhos_seguranca(): envia cabeçalhos HTTP que protegem a página.
// ----------------------------------------------------------------------
function cabecalhos_seguranca()
{
    // Se o PHP já enviou texto para o navegador, não dá mais para mandar
    // cabeçalhos; então saímos da função sem fazer nada.
    if (headers_sent()) {
        return;
    }

    // Impede o navegador de "adivinhar" o tipo de um arquivo (evita truques
    // em que um texto é executado como script).
    header('X-Content-Type-Options: nosniff');

    // Impede que outro site coloque esta página dentro de um <iframe>
    // (ataque de "clickjacking": enganar o clique do admin).
    header('X-Frame-Options: DENY');

    // Só envia o endereço da página anterior para o próprio site, nunca
    // para sites externos.
    header('Referrer-Policy: same-origin');

    // Páginas com dados pessoais não devem ficar guardadas no cache do navegador.
    header('Cache-Control: no-store');
}


// ----------------------------------------------------------------------
// ler_post() e ler_get(): leem um campo enviado pelo navegador.
// ----------------------------------------------------------------------
// Um atacante pode enviar "nome[]=x" (uma LISTA em vez de um texto), o que
// causa erros no PHP. Estas funções só aceitam texto de verdade.
function ler_post($campo)
{
    // Pega o campo em $_POST; se não existir, usa "" (texto vazio).
    $valor = $_POST[$campo] ?? '';

    // Só devolve o valor se for texto. Qualquer outra coisa vira "".
    return is_string($valor) ? $valor : '';
}

function ler_get($campo)
{
    // Igual a ler_post, mas para dados que vêm na URL (?id=5).
    $valor = $_GET[$campo] ?? '';

    // Só aceita texto.
    return is_string($valor) ? $valor : '';
}


// ----------------------------------------------------------------------
// normalizar_id(): confere se o valor é um ID válido.
// ----------------------------------------------------------------------
// Devolve o número inteiro, ou null se for inválido (vazio, letras,
// negativo, zero, decimal, gigante demais...).
function normalizar_id($valor)
{
    // Só aceitamos texto ou número. Listas, objetos etc. são recusados.
    if (!is_string($valor) && !is_int($valor)) {
        return null;
    }

    // filter_var confere se é um inteiro dentro dos limites:
    //   min_range 1            -> IDs começam em 1 (0 e negativos não existem)
    //   max_range 2147483647   -> maior número que o PostgreSQL aceita em INT
    $id = filter_var($valor, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647]
    ]);

    // filter_var devolve false quando é inválido. Trocamos por null.
    return $id === false ? null : $id;
}


// ----------------------------------------------------------------------
// CSRF: token que prova que o formulário veio DESTE site.
//Falsificação de Solicitação entre Sites
// ----------------------------------------------------------------------
// Sem token, um site malicioso poderia esconder um formulário que apaga
// usuários e fazer o admin enviá-lo sem perceber (só por abrir o site).

// csrf_token(): cria (uma vez por sessão) e devolve o token.
function csrf_token()
{
    // Se ainda não existe um token na sessão...
    if (empty($_SESSION['csrf'])) {
        // ...cria um com 32 bytes aleatórios, escritos em letras e números.
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    // Devolve o token da sessão.
    return $_SESSION['csrf'];
}

// csrf_campo(): devolve o <input> escondido que vai dentro de cada formulário.
function csrf_campo()
{
    // O token é gerado por nós, mas escapamos por hábito (regra: sempre escapar).
    return '<input type="hidden" name="csrf" value="' . escapar(csrf_token()) . '">';
}

// csrf_verificar(): confere o token do formulário recebido.
function csrf_verificar()
{
    // Token que veio no formulário (só aceita texto).
    $enviado = ler_post('csrf');

    // Token que guardamos na sessão do admin.
    $esperado = $_SESSION['csrf'] ?? '';

    // hash_equals compara de um jeito seguro contra ataques de tempo.
    // Se o token da sessão estiver vazio ou não bater, bloqueia.
    if ($esperado === '' || !hash_equals($esperado, $enviado)) {
        throw new ErroValidacao(
            "O formulário expirou ou é inválido. Recarregue a página e tente de novo."
        );
    }
}


// ----------------------------------------------------------------------
// MENSAGENS "FLASH": aparecem uma única vez, depois de um redirect.
// ----------------------------------------------------------------------
// Padrão usado nas páginas: processar o formulário -> guardar uma mensagem
// -> redirecionar. Assim, se a pessoa apertar F5, o navegador NÃO reenvia
// o formulário (o que cadastraria o mesmo usuário duas vezes).

// flash_definir(): guarda a mensagem. $tipo é 'ok' ou 'erro'.
function flash_definir($tipo, $texto)
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

// flash_pegar(): devolve a mensagem guardada e a apaga (por isso é "uma vez só").
function flash_pegar()
{
    // Lê a mensagem (ou null se não houver).
    $flash = $_SESSION['flash'] ?? null;

    // Apaga da sessão para não aparecer de novo.
    unset($_SESSION['flash']);

    return $flash;
}

// redirecionar(): manda o navegador para outra página e PARA este arquivo.
function redirecionar($destino)
{
    // Pede ao navegador para ir ao endereço $destino.
    header('Location: ' . $destino);

    // exit é obrigatório: sem ele o PHP continuaria executando o resto do arquivo.
    exit();
}


// ----------------------------------------------------------------------
// exigir_conexao(): garante que o banco está acessível.
// ----------------------------------------------------------------------
function exigir_conexao($conexao)
{
    // $conexao precisa ser um objeto PDO (a ligação com o banco).
    if (!($conexao instanceof PDO)) {

        // 503 = "serviço indisponível".
        http_response_code(503);

        // Mensagem genérica. Detalhes técnicos não devem aparecer para o usuário.
        exit('Sem conexão com o banco de dados. Tente novamente mais tarde.');
    }
}


// ----------------------------------------------------------------------
// Pequenos ajudantes de exibição
// ----------------------------------------------------------------------

// pg_para_bool(): o PostgreSQL pode devolver "verdadeiro" de formas diferentes
// (true, 't', 'true', 1). Aqui tudo isso vira um true/false confiável.
// (Cuidado: no PHP o texto 'f' conta como VERDADEIRO, por isso este ajudante.)
function pg_para_bool($valor)
{
    return $valor === true || $valor === 't' || $valor === 'true' || $valor === 1 || $valor === '1';
}

// formatar_data(): transforma 2000-12-31 em 31/12/2000 para mostrar na tela.
function formatar_data($dataIso)
{
    // Lê o texto no formato ano-mês-dia.
    $data = DateTime::createFromFormat('Y-m-d', (string) $dataIso);

    // Se a leitura funcionou, devolve no formato brasileiro; senão, texto vazio.
    return $data ? $data->format('d/m/Y') : '';
}
