<?php

// ============================================================
// CONFIGURAÇÕES DE UPLOAD
// ============================================================

const IMAGEM_TIPOS = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

const VIDEO_TIPOS = [
    'video/mp4'  => 'mp4',
    'video/webm' => 'webm'
];

const IMAGEM_MAX = 5 * 1024 * 1024;      // 5 MB
const VIDEO_MAX  = 100 * 1024 * 1024;    // 100 MB

const PASTA_UPLOADS = __DIR__ . '/../uploads/jogos/';
const URL_UPLOADS   = '/game_erah/uploads/jogos/';


// ============================================================
// DÉCADAS DISPONÍVEIS NO SITE
// ============================================================

function decadas_site()
{
    return [
        1960 => 'Anos 60',
        1970 => 'Anos 70',
        1980 => 'Anos 80',
        1990 => 'Anos 90',
        2000 => 'Anos 2000',
        2010 => 'Anos 2010',
        2020 => 'Anos 2020'
    ];
}


// ============================================================
// ESCAPAR TEXTO PARA HTML
// ============================================================

function esc_html($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}


// ============================================================
// UPLOAD
// ============================================================

// Devolve o nome do arquivo salvo, ou null se nada foi enviado.
function salvar_upload($arquivo, $tiposPermitidos, $tamanhoMax)
{
    // Nenhum arquivo foi enviado
    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Arquivo ultrapassou upload_max_filesize
    if (
        $arquivo['error'] === UPLOAD_ERR_INI_SIZE ||
        $arquivo['error'] === UPLOAD_ERR_FORM_SIZE
    ) {
        throw new Exception(
            "Arquivo maior que o limite do servidor. "
            . "Ajuste upload_max_filesize no php.ini."
        );
    }

    // Outro erro no upload
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erro no upload (código " . $arquivo['error'] . ").");
    }

    // Verifica o tamanho
    if ($arquivo['size'] > $tamanhoMax) {
        throw new Exception(
            "Arquivo muito grande. Máximo: " . round($tamanhoMax / 1048576) . " MB."
        );
    }

    // Descobre o tipo REAL do arquivo (não confia na extensão)
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);

    if (!isset($tiposPermitidos[$mime])) {
        throw new Exception(
            "Tipo de arquivo não permitido. Aceitos: "
            . implode(', ', array_values($tiposPermitidos)) . "."
        );
    }

    // Cria a pasta se ela não existir
    if (!is_dir(PASTA_UPLOADS)) {
        mkdir(PASTA_UPLOADS, 0755, true);
    }

    // Nome aleatório, para nunca repetir nem ser malicioso
    $nome = bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$mime];

    if (!move_uploaded_file($arquivo['tmp_name'], PASTA_UPLOADS . $nome)) {
        throw new Exception("Não foi possível salvar o arquivo no servidor.");
    }

    return $nome;
}


// ============================================================
// APAGAR ARQUIVO DA PASTA DE UPLOADS
// ============================================================

function apagar_arquivo($nome)
{
    if (!$nome) {
        return;
    }

    $caminho = PASTA_UPLOADS . basename($nome);

    if (is_file($caminho)) {
        unlink($caminho);
    }
}


// ============================================================
// VERIFICAR SE O ENVIO PASSOU DO post_max_size
// ============================================================

function post_excedeu_limite()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST'
        && empty($_POST)
        && empty($_FILES)
        && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}


// ============================================================
// VALIDAR OS DADOS DO JOGO
// ============================================================

function validar_jogo($d)
{
    // Nome
    if (trim($d['titulo'] ?? '') === '' || mb_strlen($d['titulo'] ?? '') > 255) {
        throw new Exception("Informe o nome do jogo (até 255 caracteres).");
    }

    // Desenvolvedora
    if (trim($d['desenvolvedora'] ?? '') === '' || mb_strlen($d['desenvolvedora'] ?? '') > 255) {
        throw new Exception("Informe a desenvolvedora (até 255 caracteres).");
    }

    // Feito por
    if (trim($d['criado_por'] ?? '') === '' || mb_strlen($d['criado_por'] ?? '') > 255) {
        throw new Exception("Informe quem fez o jogo (até 255 caracteres).");
    }

    // Ano
    $ano = filter_var($d['ano_lancamento'] ?? '', FILTER_VALIDATE_INT);

    if ($ano === false || $ano < 1950 || $ano > (int) date('Y') + 2) {
        throw new Exception("Informe um ano de lançamento válido.");
    }

    // Descrição
    if (trim($d['descricao'] ?? '') === '') {
        throw new Exception("Informe a descrição do jogo.");
    }
}


// ============================================================
// CLASSIFICAÇÃO NA DÉCADA (posição em cada categoria)
// ============================================================

// Lê os campos posicao[ID_DA_CATEGORIA] do formulário.
// Devolve algo como [1 => '3', 2 => '', 3 => '1'].
function ler_posicoes()
{
    $posicoes = [];

    if (isset($_POST['posicao']) && is_array($_POST['posicao'])) {
        foreach ($_POST['posicao'] as $categoriaId => $valor) {
            $posicoes[(int) $categoriaId] = is_scalar($valor) ? trim((string) $valor) : '';
        }
    }

    return $posicoes;
}

// Cada posição preenchida precisa ser um número inteiro de 1 a 100.
function validar_posicoes($posicoes)
{
    foreach ($posicoes as $valor) {

        if ($valor === '') {
            continue; // em branco = o jogo não entra nessa classificação
        }

        $n = filter_var($valor, FILTER_VALIDATE_INT);

        if ($n === false || $n < 1 || $n > 100) {
            throw new Exception("A posição na classificação deve ser um número de 1 a 100.");
        }
    }
}

// Lista as categorias (Mais jogados, Melhor qualidade gráfica...).
function categorias_listar($c)
{
    return $c->query("SELECT id, nome FROM categoria ORDER BY id")
             ->fetchAll(PDO::FETCH_ASSOC);
}

// Classificações de UM jogo: [categoria_id => posicao]
function classificacoes_do_jogo($c, $jogoId)
{
    $s = $c->prepare(
        "SELECT categoria_id, posicao
         FROM jogo_categoria
         WHERE jogo_id = ? AND posicao IS NOT NULL"
    );
    $s->execute([(int) $jogoId]);

    $resultado = [];

    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $linha) {
        $resultado[(int) $linha['categoria_id']] = (int) $linha['posicao'];
    }

    return $resultado;
}

// Classificações de TODOS os jogos, agrupadas por jogo:
// [jogo_id => [ ['categoria' => 'Mais jogados', 'posicao' => 3], ... ]]
function classificacoes_todas($c)
{
    $sql = "
        SELECT jc.jogo_id, ca.nome, jc.posicao
        FROM jogo_categoria jc
        JOIN categoria ca ON ca.id = jc.categoria_id
        WHERE jc.posicao IS NOT NULL
        ORDER BY jc.posicao, ca.nome
    ";

    $resultado = [];

    foreach ($c->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $linha) {
        $resultado[(int) $linha['jogo_id']][] = [
            'categoria' => $linha['nome'],
            'posicao'   => (int) $linha['posicao']
        ];
    }

    return $resultado;
}

// Regrava as classificações de um jogo.
// IMPORTANTE: só é chamada de dentro de jogo_criar / jogo_atualizar,
// que já abrem a transação.
function jogo_salvar_classificacoes($c, $jogoId, $posicoes)
{
    $c->prepare("DELETE FROM jogo_categoria WHERE jogo_id = ?")
      ->execute([(int) $jogoId]);

    $inserir = $c->prepare(
        "INSERT INTO jogo_categoria (jogo_id, categoria_id, posicao) VALUES (?, ?, ?)"
    );

    foreach ($posicoes as $categoriaId => $posicao) {

        if ($posicao === '' || $posicao === null) {
            continue;
        }

        $inserir->execute([(int) $jogoId, (int) $categoriaId, (int) $posicao]);
    }
}


// ============================================================
// DESCOBRIR A DÉCADA PELO ANO  (1994 -> 1990)
// ============================================================

function decada_do_ano($ano)
{
    return intdiv((int) $ano, 10) * 10;
}


// ============================================================
// CRIAR JOGO
// ============================================================

// Devolve o ID do jogo criado.
function jogo_criar(
    $c,
    $titulo,
    $ano,
    $desenvolvedora,
    $criado_por,
    $descricao,
    $imagem,
    $video,
    $posicoes = []
) {
    $sql = "
        INSERT INTO jogo (
            titulo, ano_lancamento, desenvolvedora, criado_por,
            descricao, imagem, video, decada_id
        )
        VALUES (
            ?, ?, ?, ?, ?, ?, ?,
            (SELECT id FROM decada WHERE ano_inicio = ? LIMIT 1)
        )
        RETURNING id
    ";

    // Transação: ou salva o jogo E a classificação, ou não salva nada.
    $c->beginTransaction();

    try {

        $s = $c->prepare($sql);

        $s->execute([
            $titulo,
            (int) $ano,
            $desenvolvedora,
            $criado_por,
            $descricao,
            $imagem,
            $video,
            decada_do_ano($ano)
        ]);

        $id = (int) $s->fetchColumn();

        jogo_salvar_classificacoes($c, $id, $posicoes);

        $c->commit();

        return $id;

    } catch (Exception $e) {

        if ($c->inTransaction()) {
            $c->rollBack();
        }

        throw $e;
    }
}


// ============================================================
// BUSCAR UM JOGO
// ============================================================

function jogo_buscar($c, $id)
{
    $sql = "
        SELECT *, FLOOR(ano_lancamento / 10) * 10 AS decada
        FROM jogo
        WHERE id = ?
    ";

    $s = $c->prepare($sql);
    $s->execute([(int) $id]);

    return $s->fetch(PDO::FETCH_ASSOC);
}


// ============================================================
// LISTAR JOGOS
// ============================================================

function jogos_listar($c, $decada = null)
{
    $sql = "
        SELECT *, FLOOR(ano_lancamento / 10) * 10 AS decada
        FROM jogo
    ";

    $params = [];

    // Filtrar por década
    if ($decada !== null) {
        $sql .= " WHERE FLOOR(ano_lancamento / 10) * 10 = ? ";
        $params[] = (int) $decada;
    }

    $sql .= " ORDER BY ano_lancamento, titulo ";

    $s = $c->prepare($sql);
    $s->execute($params);

    return $s->fetchAll(PDO::FETCH_ASSOC);
}


// ============================================================
// ATUALIZAR JOGO
// ============================================================

// $imagem / $video = null mantém o arquivo atual.
// $posicoes = null mantém as classificações atuais.
function jogo_atualizar(
    $c,
    $id,
    $titulo,
    $ano,
    $desenvolvedora,
    $criado_por,
    $descricao,
    $imagem,
    $video,
    $posicoes = null
) {
    $sql = "
        UPDATE jogo
        SET
            titulo         = ?,
            ano_lancamento = ?,
            desenvolvedora = ?,
            criado_por     = ?,
            descricao      = ?,
            imagem         = COALESCE(?, imagem),
            video          = COALESCE(?, video),
            decada_id      = (SELECT id FROM decada WHERE ano_inicio = ? LIMIT 1)
        WHERE id = ?
    ";

    $c->beginTransaction();

    try {

        $s = $c->prepare($sql);

        $s->execute([
            $titulo,
            (int) $ano,
            $desenvolvedora,
            $criado_por,
            $descricao,
            $imagem,
            $video,
            decada_do_ano($ano),
            (int) $id
        ]);

        if ($posicoes !== null) {
            jogo_salvar_classificacoes($c, $id, $posicoes);
        }

        $c->commit();

    } catch (Exception $e) {

        if ($c->inTransaction()) {
            $c->rollBack();
        }

        throw $e;
    }
}


// ============================================================
// APAGAR JOGO
// ============================================================

// Devolve true se apagou, false se o jogo não existe.
// Se o banco recusar, LANÇA uma Exception com o motivo.
function jogo_apagar($c, $id)
{
    $jogo = jogo_buscar($c, $id);

    if (!$jogo) {
        return false;
    }

    // Favoritos, comentários e classificações do jogo são apagados
    // pelo próprio banco (ON DELETE CASCADE, veja correcoes_1.sql).
    $s = $c->prepare("DELETE FROM jogo WHERE id = ?");

    if (!$s->execute([(int) $id])) {
        // Só chega aqui se o PDO estiver em modo "silencioso".
        throw new Exception("O banco recusou apagar o jogo: " . implode(' ', $s->errorInfo()));
    }

    // Só apaga os arquivos DEPOIS de o jogo ter saído do banco.
    apagar_arquivo($jogo['imagem'] ?? null);
    apagar_arquivo($jogo['video'] ?? null);

    return true;
}


// ============================================================
// TOTAL DE JOGOS
// ============================================================

function jogos_total($c)
{
    return (int) $c->query("SELECT COUNT(*) FROM jogo")->fetchColumn();
}


// ============================================================
// JOGOS POR DÉCADA
// ============================================================

function jogos_por_decada($c)
{
    $sql = "
        SELECT FLOOR(ano_lancamento / 10) * 10 AS decada, COUNT(*) AS total
        FROM jogo
        GROUP BY 1
        ORDER BY 1
    ";

    return $c->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
