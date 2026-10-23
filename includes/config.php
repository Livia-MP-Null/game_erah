<?php
// ============================================================
// CONFIGURAÇÃO GERAL  (carregue este arquivo no TOPO de toda página)
// ============================================================
////Ele descobre dinamicamente o endereço raiz do site (BASE_URL) para que nenhum link quebre,
// 
//
// 1) Descobre o endereço da pasta do projeto (BASE_URL), seja qual for a
//    forma de rodar o site:
//       php -S dentro de game_erah   -> BASE_URL = ""            (site em /)
//       php -S uma pasta acima       -> BASE_URL = "/game_erah"
//       XAMPP (htdocs/game_erah)     -> BASE_URL = "/game_erah"
//    Assim os links e as imagens nunca "quebram" por causa do endereço.
//
// 2) Inicia a sessão ANTES de qualquer HTML. Se ela só for iniciada depois
//    do HTML, o PHP não consegue ler quem está logado.

// Funções de reserva para servidores SEM a extensão mbstring (mb_strlen, mb_strtolower).
// Como o config.php é carregado por todas as páginas, isto vale para o site inteiro.
require_once __DIR__ . '/compat.php';
//garantedo que o sistema tenha todas as ferramentas de texto necessárias independentemente do servidor.

if (!defined('BASE_URL')) {
//Verifica se a constante BASE_URL ainda não foi criada
    $docRaiz = $_SERVER['DOCUMENT_ROOT'] ?? '';
//Pega o caminho físico principal da raiz do servidor web
    $raizProjeto  = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
    //Descobre o caminho absoluto onde o projeto está guardado no computador/servidor e padroniza todas as barras invertidas (\) para barras normais (/).
    $raizServidor = $docRaiz !== '' ? str_replace('\\', '/', (string) realpath($docRaiz)) : '';
//Padroniza também as barras do caminho raiz do servidor web para o mesmo formato.
    if ($raizServidor !== '' && stripos($raizProjeto, $raizServidor) === 0) {
        //Compara os dois caminhos para ver se o projeto está devidamente guardado dentro da pasta raiz do servidor.
        //calcula dinamicamente o caminho base (a pasta relativa) onde o seu projeto está instalado dentro do servidor web.
        $base = (string) substr($raizProjeto, strlen($raizServidor));
        //Pega o caminho completo da pasta onde o projeto está salvo e corta fora o pedaço inicial correspondente ao servidor,
        //substr é a abreviação de substring (que significa "subtexto" ou "pedaço de texto"
        
    } else {
        $base = '/game_erah';   // plano B
    }

    define('BASE_URL', rtrim($base, '/'));
}
//Ele normaliza as barras de diretório (trocando contrabarras \ do Windows por barras normais /), compara o caminho físico do projeto com a raiz do servidor web, e cria uma constante chamada BASE_URL. Assim, quando você chama arquivos CSS, imagens ou links (<?= BASE_URL 
// ?/style/style.css), o caminho nunca quebra, seja no seu computador local ou em um servidor de hospedagem na nuvem. Se algo falhar, ele ainda tem um "Plano B" apontando para /game_erah.

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
    //Ele faz duas checagens antes de iniciar:

//Verifica se já existe uma sessão ativa (PHP_SESSION_NONE).

//Verifica se o cabeçalho HTTP ainda não foi enviado ao navegador (!headers_sent()).
}
