<?php
// ======================================================================
// logout.php  |  SAIR DO SITE

// Configuração geral: define BASE_URL e inicia a sessão se ainda não estiver iniciada.
require_once __DIR__ . '/../includes/config.php';

// Esvazia todos os dados guardados na sessão (id, nome, admin, token...).
$_SESSION = [];

// Apaga o cookie da sessão no navegador (se o site usa cookies de sessão).
if (ini_get('session.use_cookies')) {

    // Pega as configurações do cookie (caminho, domínio...) para apagar o certo.
    $cookie = session_get_cookie_params();

    // Reenvia o cookie com validade no passado: o navegador o descarta.
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $cookie['path'],
        $cookie['domain'],
        $cookie['secure'],
        $cookie['httponly']
    );
}

// Destrói a sessão no servidor (só se ela estiver ativa).
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Volta para a página inicial. BASE_URL faz o endereço funcionar em qualquer pasta.
header("Location: " . BASE_URL . "/index.php");

// Para o script aqui: sem exit, o PHP continuaria executando.
exit();
