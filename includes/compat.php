<?php
// 
// compat.php Formatação caso não exista
//
// Alguns servidores (como o PHP do Linux em 192.168.10.45) vêm SEM a
// extensão mbstring. Sem ela, funções como mb_strlen() e mb_strtolower()
// não existem, e a página quebra com "Call to undefined function" (HTTP 500). foi o que aconteceu na quarta feira
//
// Este arquivo cria versões de reserva dessas funções, mas SÓ quando elas
// não existem. Em servidores que já têm a mbstring, nada aqui é usado.
//
// Vantagem: o resto do código continua contando LETRAS (e não bytes),
// então "José" tem 4 letras mesmo com o acento.
// 

// ----------------------------------------------------------------------
// mb_strlen(): conta quantas LETRAS tem um texto.
// ----------------------------------------------------------------------
//O PHP verifica se a função padrão mb_strlen (usada para contar caracteres de textos acentuados corretamente) já existe no servidor. Se ela não existir, o código cria uma versão própria logo abaixo para que o sistema não quebre.
if (!function_exists('mb_strlen')) {

    function mb_strlen($texto, $codificacao = null)
    {
        // Garante que é texto.
        $texto = (string) $texto;

        // 1ª tentativa: iconv_strlen conta letras em UTF-8 (se a extensão iconv(A extensão iconv é um módulo  que permite converter textos e strings de uma codificação de caracteres) existir).
        if (function_exists('iconv_strlen')) {

            // O @ esconde o aviso que o iconv gera quando o texto tem bytes inválidos.
            $quantidade = @iconv_strlen($texto, 'UTF-8');

            if ($quantidade !== false) {
                return $quantidade;
            }
        }

        // 2ª tentativa: a expressão regular com /u lê o texto como UTF-8
        // e conta cada letra encontrada (/s faz o ponto aceitar quebras de linha).
        $quantidade = @preg_match_all('/./su', $texto, $achados);

        if ($quantidade !== false) {
            return $quantidade;
        }

        // Último recurso: conta bytes.
        return strlen($texto);
    }
}


// ----------------------------------------------------------------------
// mb_strtolower(): põe um texto em minúsculas (incluindo letras acentuadas).
// ----------------------------------------------------------------------
if (!function_exists('mb_strtolower')) {

    function mb_strtolower($texto, $codificacao = null)
    {
        // strtolower cuida das letras sem acento (A-Z).
        $texto = strtolower((string) $texto);

        // strtr troca cada letra acentuada maiúscula pela minúscula correspondente.
        return strtr($texto, [
            'Á' => 'á', 'À' => 'à', 'Â' => 'â', 'Ã' => 'ã', 'Ä' => 'ä',
            'É' => 'é', 'È' => 'è', 'Ê' => 'ê', 'Ë' => 'ë',
            'Í' => 'í', 'Ì' => 'ì', 'Î' => 'î', 'Ï' => 'ï',
            'Ó' => 'ó', 'Ò' => 'ò', 'Ô' => 'ô', 'Õ' => 'õ', 'Ö' => 'ö',
            'Ú' => 'ú', 'Ù' => 'ù', 'Û' => 'û', 'Ü' => 'ü',
            'Ç' => 'ç', 'Ñ' => 'ñ',
        ]);
    }
}
//A função mb_strlen (Contar letras com precisão)
////Se mb_strlen não existir, o script cria uma própria usando três tentativas em cascata:

//1ª tentativa (iconv_strlen): Tenta usar a extensão iconv (muito comum em sistemas Unix) para contar os caracteres em UTF-8 de forma segura.

//2ª tentativa (Expressão Regular): Se o iconv não estiver disponível, usa preg_match_all('/./su', $texto) com o modificador /u, que força o PHP a ler o texto como UTF-8 e contar cada caractere (em vez de contar bytes crus).

//3ª tentativa (Fallback final): Se tudo mais falhar, recorre ao strlen() tradicional (que conta bytes, podendo dar diferença em caracteres acentuados, mas evita que o sistema quebre).

//2. A função mb_strtolower (Letras minúsculas com acento)
//O strtolower() padrão do PHP não sabe lidar com letras acentuadas (ele altera apenas de A a Z). Para resolver isso, a função personalizada:

//Converte o texto básico com o strtolower() comum.

//Usa a função strtr() para substituir manualmente cada letra maiúscula acentuada (Á, É, Ç, etc.) pela sua respectiva versão minúscula (á, é, ç).
