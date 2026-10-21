<?php
// ============================================================
// CONFIGURAÇÃO GERAL
// ============================================================

if (session_status() === PHP_SESSION_NONE) {

    // Detecta se a conexão é realmente HTTPS
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    // Configura os cookies da sessão antes de iniciar
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',       // Vazio para aceitar automaticamente qualquer IP da rede ou localhost
        'secure'   => $isHttps, // Falso em conexões HTTP locais (não descarta o cookie)
        'httponly' => true,
        'samesite' => 'Lax'     // Permite navegação normal entre páginas na rede local
    ]);

    if (!headers_sent()) {
        session_start();
    }
}

// Descobre o endereço base do projeto (BASE_URL)
if (!defined('BASE_URL')) {

    $docRaiz = $_SERVER['DOCUMENT_ROOT'] ?? '';

    $raizProjeto  = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
    $raizServidor = $docRaiz !== '' ? str_replace('\\', '/', (string) realpath($docRaiz)) : '';

    if ($raizServidor !== '' && stripos($raizProjeto, $raizServidor) === 0) {
        $base = (string) substr($raizProjeto, strlen($raizServidor));
    } else {
        $base = '/game_erah'; // plano B
    }

    define('BASE_URL', rtrim($base, '/'));
}