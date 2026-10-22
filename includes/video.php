<?php
// ======================================================================
// video.php
// Vídeo do jogo por LINK (YouTube), em vez de arquivo enviado.
//
// Por que links?
//   Um vídeo enviado fica dentro da pasta do site (dezenas de MB cada) e
//   deixa o projeto, o servidor e o Git pesados. Com um link, o vídeo
//   fica guardado no YouTube e o site só guarda um texto pequeno.
//
// O que existe aqui:
//   - video_interpretar() descobre se um texto é um link de vídeo válido
//   - video_normalizar()  usado pelo ADMIN ao salvar: valida e limpa o link
//   - video_html()        usado para MOSTRAR o vídeo na tela
//
// Nenhuma mudança no banco é necessária: o link é guardado na mesma coluna
// "video" da tabela "jogo" (VARCHAR 255), onde antes ficava o nome do arquivo.
// ======================================================================


// ----------------------------------------------------------------------
// video_esc(): protege um texto antes de colocá-lo no HTML.
//htmlspecialchars(...): É a função nativa do PHP responsável por "escapar" caracteres especiais. Ela pega caracteres que o navegador interpretaria como código HTML/JavaScript (como <, >, ", ' e &) e os transforma em entidades HTML seguras (por exemplo, o < vira &lt;).

//(string) $texto: Garante que tudo o que for passado para dentro da função seja forçado a ser tratado como um texto puro, evitando erros de tipo no PHP.

//ENT_QUOTES: Diz para a função converter tanto as aspas duplas quanto as aspas simples, o que aumenta drasticamente a segurança contra injeções de código em atributos HTML.
//'UTF-8': Define o padrão de codificação de caracteres, garantindo que acentos e símbolos da língua portuguesa não sejam corrompidos no processo.
// (É igual ao esc_html, mas fica aqui para este arquivo não depender de outro.)
// ----------------------------------------------------------------------
function video_esc($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}


// ----------------------------------------------------------------------
// video_interpretar(): analisa um texto e diz se é um link de vídeo aceito.
// ----------------------------------------------------------------------
// Devolve:
//   ['tipo' => 'youtube', 'id' => 'dQw4w9WgXcQ', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
//   ['tipo' => 'externo', 'url' => 'https://outro-site.com/video']
//   null  (se o texto não é um link aceito; esta função NUNCA lança erro)
function video_interpretar($texto)
{
    // Garante que é texto e tira os espaços das pontas.
    $texto = trim((string) $texto);

    // Texto vazio não é link.
    if ($texto === '') {
        return null;
    }

    // Recusa endereços com "esquema" perigoso ou estranho, como
    // javascript:..., data:..., ftp://... (só http e https são aceitos).
    // A expressão procura letras seguidas de ":" no começo do texto.
    if (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $texto) && !preg_match('#^https?://#i', $texto)) {
        return null;
    }

    // Se a pessoa colou o link sem "https://" (ex.: youtube.com/watch?v=...),
    // acrescentamos. Mas só se houver uma "/" no texto:não transforma isso impede que um
    // nome de arquivo antigo (como abc123.mp4) seja confundido com um site.
    if (!preg_match('#^https?://#i', $texto)) {

        if (strpos($texto, '/') === false) {
            return null;
        }

        $texto = 'https://' . $texto;
    }

    // Separa o endereço em partes (esquema, site, caminho, parâmetros).
    $partes = parse_url($texto);

    // Se não deu para entender o endereço, ou ele não tem site, recusa.
    if ($partes === false || empty($partes['host'])) {
        return null;
    }

    // Nome do site em minúsculas e sem "www." ou "m." no começo.
    $host = preg_replace('/^(www\.|m\.)/', '', strtolower($partes['host']));

    // Caminho do endereço (a parte depois do site), ex.: /watch
    $caminho = $partes['path'] ?? '';

    // Aqui guardaremos o código do vídeo do YouTube, se acharmos um.
    $id = null;

    if ($host === 'youtu.be') {

        // Formato curto: https://youtu.be/CODIGO
        // trim tira as "/" das pontas; explode corta nas "/" e [0] pega o primeiro pedaço.
        $id = explode('/', trim($caminho, '/'))[0];

    } elseif ($host === 'youtube.com' || $host === 'youtube-nocookie.com') {

        if ($caminho === '/watch') {

            // Formato comum: https://www.youtube.com/watch?v=CODIGO
            // parse_str transforma "v=CODIGO&t=10" em uma lista ['v' => 'CODIGO', 't' => '10'].
            parse_str($partes['query'] ?? '', $consulta);
            $id = $consulta['v'] ?? null;

        } elseif (preg_match('#^/(shorts|embed|live|v)/([^/?]+)#', $caminho, $achado)) {

            // Outros formatos: /shorts/CODIGO, /embed/CODIGO, /live/CODIGO
            $id = $achado[2];
        }
    }

    // Se encontramos algo parecido com um código do YouTube...
    if ($id !== null) {

        // O código do YouTube tem sempre 11 caracteres (letras, números, _ e -).
        // Se vier uma lista (v[]=...) ou outro formato, recusa.
        if (!is_string($id) || !preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
            return null;
        }

        // Devolve o link na forma padrão, sempre igual, não importa como foi colado.
        return [
            'tipo' => 'youtube',
            'id'   => $id,
            'url'  => 'https://www.youtube.com/watch?v=' . $id
        ];
    }

    // Link de outro site: só aceitamos https (conexão segura).
    if (strtolower($partes['scheme'] ?? '') !== 'https') {
        return null;
    }

    // O site precisa ter um ponto (algo.com); impede "https://localhost" e similares.
    if (strpos($host, '.') === false) {
        return null;
    }

    // Recusa endereços com usuário e senha (https://usuario:senha@site.com),
    // truque usado para esconder o destino verdadeiro de um link.
    if (isset($partes['user']) || isset($partes['pass'])) {
        return null;
    }

    // Confere o formato geral de URL.
    if (!filter_var($texto, FILTER_VALIDATE_URL)) {
        return null;
    }

    // Link externo aceito (será mostrado como um botão "Assistir vídeo").
    return ['tipo' => 'externo', 'url' => $texto];
}


// ----------------------------------------------------------------------
// video_normalizar(): usada pelo ADMIN ao salvar o jogo.
// ----------------------------------------------------------------------
// Devolve:
//   null            -> campo vazio (o jogo fica sem vídeo)
//   o link limpo    -> pronto para gravar no banco
// Se o link for inválido, lança uma Exception com mensagem para o admin.
function video_normalizar($texto)
{
    // Tira os espaços das pontas.
    $texto = trim((string) $texto);

    // Vazio é permitido: significa "sem vídeo".
    if ($texto === '') {
        return null;
    }

    // Limite para evitar textos gigantes.
    if (mb_strlen($texto) > 500) {
        throw new Exception('O link do vídeo é grande demais.');
    }

    // Analisa o link.
    $video = video_interpretar($texto);

    // Não é um link aceito.
    if ($video === null) {
        throw new Exception(
            'Link de vídeo inválido. Use um link do YouTube '
            . '(ex.: https://www.youtube.com/watch?v=...) ou outro link https.'
        );
    }

    // A coluna do banco guarda no máximo 255 caracteres.
    if (mb_strlen($video['url']) > 255) {
        throw new Exception('O link do vídeo deve ter no máximo 255 caracteres.');
    }

    // Devolve o link já na forma padrão.
    return $video['url'];
}


// ----------------------------------------------------------------------
// video_html(): devolve o HTML para MOSTRAR o vídeo (ou "" se não houver).
// ----------------------------------------------------------------------
// YouTube: aparece só uma MINIATURA com um botão de play (leve!). O player
// de verdade só é carregado quando a pessoa CLICA, por um pequeno script.
// Outros links: aparece um botão "Assistir vídeo" que abre em outra aba.
function video_html($valor)
{
    // static: a variável lembra o valor entre uma chamada e outra. Assim o
    // script abaixo é escrito UMA vez só na página, mesmo com vários vídeos.
    static $scriptImpresso = false;

    // Analisa o valor guardado no banco (confere de novo, por segurança).
    $video = video_interpretar($valor);

    // Valor vazio, inválido ou nome de arquivo antigo: não mostra nada.
    if ($video === null) {
        return '';
    }

    // ---- Link de outro site: um botão que abre em outra aba.
    // rel="noopener noreferrer" impede o outro site de controlar esta página.
    if ($video['tipo'] === 'externo') {
        return '<a class="btn-admin video-externo" href="' . video_esc($video['url']) . '"'
             . ' target="_blank" rel="noopener noreferrer">&#9654; Assistir vídeo</a>';
    }

    // ---- YouTube: miniatura + botão de play.
    // Sem JavaScript, o clique abre o vídeo no YouTube (em outra aba).
    // Com JavaScript, o clique troca a miniatura pelo player, na própria página.
    $id = video_esc($video['id']);

    $html = '<div class="video-box">'
          . '<a class="video-lazy" href="' . video_esc($video['url']) . '"'
          . ' target="_blank" rel="noopener noreferrer"'
          // data-embed: endereço do player (youtube-nocookie = versão que guarda menos dados).
          . ' data-embed="https://www.youtube-nocookie.com/embed/' . $id . '?autoplay=1&amp;rel=0"'
          . ' aria-label="Assistir ao vídeo">'
          // Miniatura oficial do vídeo; loading="lazy" só baixa quando está perto de aparecer.
          . '<img src="https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg" alt="Miniatura do vídeo" loading="lazy">'
          . '<span class="video-play" aria-hidden="true">&#9654;</span>'
          . '</a>'
          . '</div>';

    // O script que troca a miniatura pelo player só é escrito na primeira vez.
    if (!$scriptImpresso) {

        $scriptImpresso = true;

        $html .= <<<'HTML'
<script>
(function () {
    // Escuta TODOS os cliques da página e age só nos cliques em uma miniatura de vídeo.
    document.addEventListener('click', function (evento) {

        // closest procura, a partir do local clicado, o elemento ".video-lazy".
        var link = evento.target.closest ? evento.target.closest('.video-lazy') : null;

        // Clicou em outra coisa: não faz nada.
        if (!link) { return; }

        // Impede o link de abrir em outra aba (o vídeo vai tocar aqui mesmo).
        evento.preventDefault();

        // Cria o player do YouTube (um iframe) com reprodução automática.
        var player = document.createElement('iframe');
        player.src = link.getAttribute('data-embed');
        player.title = 'Vídeo do jogo';
        player.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
        player.allowFullscreen = true;

        // Troca a miniatura pelo player.
        link.parentNode.replaceChild(player, link);
    });
})();
</script>
HTML;
    }

    return $html;
}
