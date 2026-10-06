<?php

// ---------- Configurações de upload ----------
const IMAGEM_TIPOS  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
const VIDEO_TIPOS   = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
const IMAGEM_MAX    = 5 * 1024 * 1024;     // 5 MB
const VIDEO_MAX     = 100 * 1024 * 1024;   // 100 MB
const PASTA_UPLOADS = __DIR__ . '/../uploads/jogos/';
const URL_UPLOADS   = '/game_erah/uploads/jogos/';

// Décadas que existem no site (ano inicial => texto do botão)
function decadas_site()
{
    return [
        1960 => 'Anos 60',
        1970 => 'Anos 70',
        1980 => 'Anos 80',
        1990 => 'Anos 90',
        2000 => 'Anos 2000',
        2010 => 'Anos 2010',
        2020 => 'Anos 2020',
    ];
}

function esc_html($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

// ---------- Upload ----------

// Devolve o nome do arquivo salvo, ou null se nenhum arquivo foi enviado.
function salvar_upload($arquivo, $tiposPermitidos, $tamanhoMax)
{
    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE || $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new Exception("Arquivo maior que o limite do servidor (ajuste upload_max_filesize no php.ini).");
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erro no upload (código " . $arquivo['error'] . ").");
    }
    if ($arquivo['size'] > $tamanhoMax) {
        throw new Exception("Arquivo muito grande (máximo " . round($tamanhoMax / 1048576) . " MB).");
    }

    // Confere o tipo real do arquivo, não só a extensão
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    if (!isset($tiposPermitidos[$mime])) {
        throw new Exception("Tipo de arquivo não permitido. Aceitos: " . implode(', ', array_values($tiposPermitidos)) . ".");
    }

    if (!is_dir(PASTA_UPLOADS)) {
        mkdir(PASTA_UPLOADS, 0755, true);
    }

    $nome = bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$mime];

    if (!move_uploaded_file($arquivo['tmp_name'], PASTA_UPLOADS . $nome)) {
        throw new Exception("Não foi possível salvar o arquivo no servidor.");
    }
    return $nome;
}

function apagar_arquivo($nome)
{
    if ($nome) {
        $caminho = PASTA_UPLOADS . basename($nome);
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }
}

// Se o arquivo enviado passar do post_max_size, o PHP zera $_POST e $_FILES
function post_excedeu_limite()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST'
        && empty($_POST)
        && empty($_FILES)
        && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}

// ---------- Validação ----------

function validar_jogo($d)
{
    if ($d['titulo'] === '' || mb_strlen($d['titulo']) > 255) {
        throw new Exception("Informe o nome do jogo (até 255 caracteres).");
    }
    if ($d['desenvolvedora'] === '' || mb_strlen($d['desenvolvedora']) > 255) {
        throw new Exception("Informe quem criou o jogo (desenvolvedora, até 255 caracteres).");
    }
    $ano = filter_var($d['ano_lancamento'], FILTER_VALIDATE_INT);
    if ($ano === false || $ano < 1950 || $ano > (int) date('Y') + 2) {
        throw new Exception("Informe um ano de lançamento válido.");
    }
    if ($d['descricao'] === '') {
        throw new Exception("Informe a descrição do jogo.");
    }
}

// ---------- Banco de dados ----------

// A década (decada_id) é descoberta pelo ano: 1994 -> década que começa em 1990
function decada_do_ano($ano)
{
    return intdiv((int) $ano, 10) * 10;
}

function jogo_criar($c, $titulo, $ano, $desenvolvedora, $descricao, $imagem, $video)
{
    $s = $c->prepare("INSERT INTO jogo (titulo, ano_lancamento, desenvolvedora, descricao, imagem, video, decada_id)
                      VALUES (?, ?, ?, ?, ?, ?, (SELECT id FROM decada WHERE ano_inicio = ? LIMIT 1))");
    $s->execute([$titulo, (int) $ano, $desenvolvedora, $descricao, $imagem, $video, decada_do_ano($ano)]);
}

function jogo_buscar($c, $id)
{
    $s = $c->prepare("SELECT *, (ano_lancamento / 10) * 10 AS decada FROM jogo WHERE id = ?");
    $s->execute([(int) $id]);
    return $s->fetch(PDO::FETCH_ASSOC);
}

function jogos_listar($c, $decada = null)
{
    $sql = "SELECT *, (ano_lancamento / 10) * 10 AS decada FROM jogo";
    $params = [];
    if ($decada !== null) {
        $sql .= " WHERE (ano_lancamento / 10) * 10 = ?";
        $params[] = (int) $decada;
    }
    $sql .= " ORDER BY ano_lancamento, titulo";
    $s = $c->prepare($sql);
    $s->execute($params);
    return $s->fetchAll(PDO::FETCH_ASSOC);
}

// $imagem / $video = null mantém o arquivo atual
function jogo_atualizar($c, $id, $titulo, $ano, $desenvolvedora, $descricao, $imagem, $video)
{
    $s = $c->prepare("UPDATE jogo
                      SET titulo = ?, ano_lancamento = ?, desenvolvedora = ?, descricao = ?,
                          imagem = COALESCE(?, imagem), video = COALESCE(?, video),
                          decada_id = (SELECT id FROM decada WHERE ano_inicio = ? LIMIT 1)
                      WHERE id = ?");
    $s->execute([$titulo, (int) $ano, $desenvolvedora, $descricao, $imagem, $video, decada_do_ano($ano), (int) $id]);
}

function jogo_apagar($c, $id)
{
    $jogo = jogo_buscar($c, $id);
    if (!$jogo) {
        return false;
    }

    // Favoritos, comentários e categorias apontam para o jogo: saem junto com ele
    $c->beginTransaction();
    try {
        foreach (['favorito', 'comentario', 'jogo_categoria'] as $tabela) {
            $c->prepare("DELETE FROM $tabela WHERE jogo_id = ?")->execute([(int) $id]);
        }
        $c->prepare("DELETE FROM jogo WHERE id = ?")->execute([(int) $id]);
        $c->commit();
    } catch (Exception $e) {
        $c->rollBack();
        throw $e;
    }

    apagar_arquivo($jogo['imagem']);
    apagar_arquivo($jogo['video']);
    return true;
}

function jogos_total($c)
{
    return (int) $c->query("SELECT COUNT(*) FROM jogo")->fetchColumn();
}

function jogos_por_decada($c)
{
    return $c->query("SELECT (ano_lancamento / 10) * 10 AS decada, COUNT(*) AS total
                      FROM jogo GROUP BY 1 ORDER BY 1")->fetchAll(PDO::FETCH_ASSOC);
}