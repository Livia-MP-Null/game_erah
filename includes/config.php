<?php
// ============================================================
// CONFIGURAÇÃO GERAL  (carregue este arquivo no TOPO de toda página)
// ============================================================
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

if (!defined('BASE_URL')) {

    $docRaiz = $_SERVER['DOCUMENT_ROOT'] ?? '';

    $raizProjeto  = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
    $raizServidor = $docRaiz !== '' ? str_replace('\\', '/', (string) realpath($docRaiz)) : '';

    if ($raizServidor !== '' && stripos($raizProjeto, $raizServidor) === 0) {
        $base = (string) substr($raizProjeto, strlen($raizServidor));
    } else {
        $base = '/game_erah';   // plano B
    }

    define('BASE_URL', rtrim($base, '/'));
}

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
