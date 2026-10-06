<?php

// ---------- Configurações de upload ----------
const IMAGEM_TIPOS  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
const VIDEO_TIPOS   = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
const IMAGEM_MAX    = 5 * 1024 * 1024;     // 5 MB
const VIDEO_MAX     = 100 * 1024 * 1024;   // 100 MB
const PASTA_UPLOADS = __DIR__ . '/../uploads/jogos/';
const URL_UPLOADS   = '/game_erah/uploads/jogos/';

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
    $ano = filter_var($d['ano_lancamento'], FILTER_VALIDATE_INT);
    if ($ano === false || $ano < 1950 || $ano > (int) date('Y') + 2) {
        throw new Exception("Informe um ano de lançamento válido.");
    }
    if ($d['descricao'] === '') {
        throw new Exception("Informe a descrição do jogo.");
    }
}

// ---------- Banco de dados ----------

function jogo_criar($c, $titulo, $ano, $descricao, $imagem, $video)
{
    $s = $c->prepare("INSERT INTO jogos (titulo, ano_lancamento, descricao, imagem, video) VALUES (?, ?, ?, ?, ?)");
    $s->execute([$titulo, (int) $ano, $descricao, $imagem, $video]);
}

function jogo_buscar($c, $id)
{
    $s = $c->prepare("SELECT *, (ano_lancamento / 10) * 10 AS decada FROM jogos WHERE id = ?");
    $s->execute([(int) $id]);
    return $s->fetch(PDO::FETCH_ASSOC);
}

function jogos_listar($c, $decada = null)
{
    $sql = "SELECT *, (ano_lancamento / 10) * 10 AS decada FROM jogos";
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
function jogo_atualizar($c, $id, $titulo, $ano, $descricao, $imagem, $video)
{
    $s = $c->prepare("UPDATE jogos
                      SET titulo = ?, ano_lancamento = ?, descricao = ?,
                          imagem = COALESCE(?, imagem), video = COALESCE(?, video)
                      WHERE id = ?");
    $s->execute([$titulo, (int) $ano, $descricao, $imagem, $video, (int) $id]);
}

function jogo_apagar($c, $id)
{
    $jogo = jogo_buscar($c, $id);
    if (!$jogo) {
        return false;
    }
    $s = $c->prepare("DELETE FROM jogos WHERE id = ?");
    $s->execute([(int) $id]);
    apagar_arquivo($jogo['imagem']);
    apagar_arquivo($jogo['video']);
    return true;
}

function jogos_total($c)
{
    return (int) $c->query("SELECT COUNT(*) FROM jogos")->fetchColumn();
}

function jogos_por_decada($c)
{
    return $c->query("SELECT (ano_lancamento / 10) * 10 AS decada, COUNT(*) AS total
                      FROM jogos GROUP BY 1 ORDER BY 1")->fetchAll(PDO::FETCH_ASSOC);
}
