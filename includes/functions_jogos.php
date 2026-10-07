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
    return htmlspecialchars(
        (string) $texto,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ============================================================
// UPLOAD
// ============================================================

function salvar_upload(
    $arquivo,
    $tiposPermitidos,
    $tamanhoMax
) {
    // Nenhum arquivo foi enviado
    if (
        !$arquivo ||
        $arquivo['error'] === UPLOAD_ERR_NO_FILE
    ) {
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
        throw new Exception(
            "Erro no upload (código "
            . $arquivo['error']
            . ")."
        );
    }

    // Verifica tamanho
    if ($arquivo['size'] > $tamanhoMax) {
        throw new Exception(
            "Arquivo muito grande. Máximo: "
            . round($tamanhoMax / 1048576)
            . " MB."
        );
    }

    // Descobre o tipo real do arquivo
    $mime = (
        new finfo(FILEINFO_MIME_TYPE)
    )->file(
        $arquivo['tmp_name']
    );

    // Verifica se o tipo é permitido
    if (!isset($tiposPermitidos[$mime])) {
        throw new Exception(
            "Tipo de arquivo não permitido. "
            . "Aceitos: "
            . implode(
                ', ',
                array_values($tiposPermitidos)
            )
            . "."
        );
    }

    // Cria a pasta se ela não existir
    if (!is_dir(PASTA_UPLOADS)) {
        mkdir(
            PASTA_UPLOADS,
            0755,
            true
        );
    }

    // Cria um nome aleatório para o arquivo
    $nome =
        bin2hex(random_bytes(16))
        . '.'
        . $tiposPermitidos[$mime];

    // Move o arquivo para a pasta uploads
    if (
        !move_uploaded_file(
            $arquivo['tmp_name'],
            PASTA_UPLOADS . $nome
        )
    ) {
        throw new Exception(
            "Não foi possível salvar o arquivo no servidor."
        );
    }

    return $nome;
}


// ============================================================
// APAGAR ARQUIVO
// ============================================================

function apagar_arquivo($nome)
{
    if (!$nome) {
        return;
    }

    $caminho =
        PASTA_UPLOADS
        . basename($nome);

    if (is_file($caminho)) {
        unlink($caminho);
    }
}


// ============================================================
// VERIFICAR POST EXCEDENDO LIMITE
// ============================================================

function post_excedeu_limite()
{
    return
        $_SERVER['REQUEST_METHOD'] === 'POST'
        &&
        empty($_POST)
        &&
        empty($_FILES)
        &&
        (int) (
            $_SERVER['CONTENT_LENGTH'] ?? 0
        ) > 0;
}


// ============================================================
// VALIDAR JOGO
// ============================================================

function validar_jogo($d)
{
    // Nome
    if (
        trim($d['titulo'] ?? '') === ''
        ||
        mb_strlen(
            $d['titulo'] ?? ''
        ) > 255
    ) {
        throw new Exception(
            "Informe o nome do jogo (até 255 caracteres)."
        );
    }

    // Desenvolvedora
    if (
        trim($d['desenvolvedora'] ?? '') === ''
        ||
        mb_strlen(
            $d['desenvolvedora'] ?? ''
        ) > 255
    ) {
        throw new Exception(
            "Informe a desenvolvedora (até 255 caracteres)."
        );
    }

    // Criado por
    if (
        trim($d['criado_por'] ?? '') === ''
        ||
        mb_strlen(
            $d['criado_por'] ?? ''
        ) > 255
    ) {
        throw new Exception(
            "Informe quem fez o jogo (até 255 caracteres)."
        );
    }

    // Ano
    $ano = filter_var(
        $d['ano_lancamento'] ?? '',
        FILTER_VALIDATE_INT
    );

    if (
        $ano === false
        ||
        $ano < 1950
        ||
        $ano > (int) date('Y') + 2
    ) {
        throw new Exception(
            "Informe um ano de lançamento válido."
        );
    }

    // Descrição
    if (
        trim($d['descricao'] ?? '') === ''
    ) {
        throw new Exception(
            "Informe a descrição do jogo."
        );
    }
}


// ============================================================
// DESCOBRIR DÉCADA PELO ANO
// ============================================================

function decada_do_ano($ano)
{
    return intdiv(
        (int) $ano,
        10
    ) * 10;
}


// ============================================================
// CRIAR JOGO
// ============================================================

function jogo_criar(
    $c,
    $titulo,
    $ano,
    $desenvolvedora,
    $criado_por,
    $descricao,
    $imagem,
    $video
) {
    $sql = "
        INSERT INTO jogo (
            titulo,
            ano_lancamento,
            desenvolvedora,
            criado_por,
            descricao,
            imagem,
            video,
            decada_id
        )
        VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            (
                SELECT id
                FROM decada
                WHERE ano_inicio = ?
                LIMIT 1
            )
        )
    ";

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
}


// ============================================================
// BUSCAR UM JOGO
// ============================================================

function jogo_buscar($c, $id)
{
    $sql = "
        SELECT
            *,
            FLOOR(
                ano_lancamento / 10
            ) * 10 AS decada
        FROM jogo
        WHERE id = ?
    ";

    $s = $c->prepare($sql);

    $s->execute([
        (int) $id
    ]);

    return $s->fetch(
        PDO::FETCH_ASSOC
    );
}


// ============================================================
// LISTAR JOGOS
// ============================================================

function jogos_listar(
    $c,
    $decada = null
) {
    $sql = "
        SELECT
            *,
            FLOOR(
                ano_lancamento / 10
            ) * 10 AS decada
        FROM jogo
    ";

    $params = [];

    // Filtrar por década
    if ($decada !== null) {

        $sql .= "
            WHERE FLOOR(
                ano_lancamento / 10
            ) * 10 = ?
        ";

        $params[] = (int) $decada;
    }

    $sql .= "
        ORDER BY
            ano_lancamento,
            titulo
    ";

    $s = $c->prepare($sql);

    $s->execute($params);

    return $s->fetchAll(
        PDO::FETCH_ASSOC
    );
}


// ============================================================
// ATUALIZAR JOGO
// ============================================================

function jogo_atualizar(
    $c,
    $id,
    $titulo,
    $ano,
    $desenvolvedora,
    $criado_por,
    $descricao,
    $imagem,
    $video
) {
    $sql = "
        UPDATE jogo
        SET
            titulo = ?,
            ano_lancamento = ?,
            desenvolvedora = ?,
            criado_por = ?,
            descricao = ?,
            imagem = COALESCE(?, imagem),
            video = COALESCE(?, video),
            decada_id = (
                SELECT id
                FROM decada
                WHERE ano_inicio = ?
                LIMIT 1
            )
        WHERE id = ?
    ";

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
}


// ============================================================
// APAGAR JOGO
// ============================================================

function jogo_apagar($c, $id)
{
    // Busca o jogo antes de apagar
    $jogo = jogo_buscar(
        $c,
        $id
    );

    if (!$jogo) {
        return false;
    }

    // Começa uma transação
    $c->beginTransaction();

    try {

        // Apaga registros relacionados
        $tabelas = [
            'favorito',
            'comentario',
            'jogo_categoria'
        ];

        foreach ($tabelas as $tabela) {

            $c->prepare(
                "DELETE FROM $tabela WHERE jogo_id = ?"
            )->execute([
                (int) $id
            ]);
        }

        // Apaga o jogo
        $c->prepare(
            "DELETE FROM jogo WHERE id = ?"
        )->execute([
            (int) $id
        ]);

        // Confirma
        $c->commit();

    } catch (Exception $e) {

        // Desfaz se houver erro
        if ($c->inTransaction()) {
            $c->rollBack();
        }

        throw $e;
    }

    // Apaga imagem
    apagar_arquivo(
        $jogo['imagem'] ?? null
    );

    // Apaga vídeo
    apagar_arquivo(
        $jogo['video'] ?? null
    );

    return true;
}


// ============================================================
// TOTAL DE JOGOS
// ============================================================

function jogos_total($c)
{
    $sql = "
        SELECT COUNT(*)
        FROM jogo
    ";

    return (int) $c
        ->query($sql)
        ->fetchColumn();
}


// ============================================================
// JOGOS POR DÉCADA
// ============================================================

function jogos_por_decada($c)
{
    $sql = "
        SELECT
            FLOOR(
                ano_lancamento / 10
            ) * 10 AS decada,
            COUNT(*) AS total
        FROM jogo
        GROUP BY
            FLOOR(
                ano_lancamento / 10
            ) * 10
        ORDER BY
            FLOOR(
                ano_lancamento / 10
            ) * 10
    ";

    $s = $c->prepare($sql);

    $s->execute();

    return $s->fetchAll(
        PDO::FETCH_ASSOC
    );
}
