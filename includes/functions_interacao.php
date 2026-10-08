<?php
// ============================================================
// FAVORITAR, CURTIR E COMENTAR
// ============================================================
// Usa as tabelas: favorito, curtida e comentario.
// Precisa de functions_jogos.php carregado antes (usa jogo_buscar).


// Favoritar / curtir funcionam como um "liga e desliga":
// se já existe, remove; se não existe, cria.
function alternar_marca($c, $tabela, $usuarioId, $jogoId)
{
    // O nome da tabela vem de código nosso, nunca do usuário; mesmo assim
    // só aceitamos estas duas.
    if (!in_array($tabela, ['favorito', 'curtida'], true)) {
        throw new Exception("Ação inválida.");
    }

    if (!jogo_buscar($c, $jogoId)) {
        throw new Exception("Jogo não encontrado.");
    }

    $apagar = $c->prepare("DELETE FROM $tabela WHERE usuario_id = ? AND jogo_id = ?");
    $apagar->execute([(int) $usuarioId, (int) $jogoId]);

    // Nenhuma linha apagada = ainda não estava marcado: marca agora.
    if ($apagar->rowCount() === 0) {
        $inserir = $c->prepare("INSERT INTO $tabela (usuario_id, jogo_id) VALUES (?, ?)");
        $inserir->execute([(int) $usuarioId, (int) $jogoId]);
    }
}


// Para uma lista de jogos, devolve:
// [jogo_id => ['curtidas' => 3, 'favoritos' => 1, 'curtiu' => true, 'favoritou' => false]]
// ($usuarioId = null quando ninguém está logado)
function interacoes_dos_jogos($c, $ids, $usuarioId = null)
{
    $resultado = [];

    foreach ($ids as $id) {
        $resultado[(int) $id] = [
            'curtidas'  => 0,
            'favoritos' => 0,
            'curtiu'    => false,
            'favoritou' => false
        ];
    }

    if (!$resultado) {
        return $resultado;
    }

    $lista      = array_keys($resultado);
    $marcadores = implode(',', array_fill(0, count($lista), '?'));

    $tabelas = [
        'curtida'  => ['curtidas', 'curtiu'],
        'favorito' => ['favoritos', 'favoritou']
    ];

    foreach ($tabelas as $tabela => $campos) {

        list($campoTotal, $campoEu) = $campos;

        // Quantas pessoas marcaram cada jogo
        $s = $c->prepare(
            "SELECT jogo_id, COUNT(*) AS total FROM $tabela
             WHERE jogo_id IN ($marcadores) GROUP BY jogo_id"
        );
        $s->execute($lista);

        foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $resultado[(int) $linha['jogo_id']][$campoTotal] = (int) $linha['total'];
        }

        // Quais jogos a pessoa logada já marcou
        if ($usuarioId) {

            $s = $c->prepare(
                "SELECT jogo_id FROM $tabela
                 WHERE usuario_id = ? AND jogo_id IN ($marcadores)"
            );
            $s->execute(array_merge([(int) $usuarioId], $lista));

            foreach ($s->fetchAll(PDO::FETCH_COLUMN) as $jogoId) {
                $resultado[(int) $jogoId][$campoEu] = true;
            }
        }
    }

    return $resultado;
}


// ============================================================
// COMENTÁRIOS
// ============================================================

function comentario_criar($c, $usuarioId, $jogoId, $texto)
{
    $texto = trim((string) $texto);

    if ($texto === '') {
        throw new Exception("Escreva algo antes de comentar.");
    }

    if (mb_strlen($texto) > 1000) {
        throw new Exception("O comentário pode ter no máximo 1000 caracteres.");
    }

    if (!jogo_buscar($c, $jogoId)) {
        throw new Exception("Jogo não encontrado.");
    }

    $s = $c->prepare("INSERT INTO comentario (usuario_id, jogo_id, texto) VALUES (?, ?, ?)");
    $s->execute([(int) $usuarioId, (int) $jogoId, $texto]);
}


// Só o autor do comentário ou um admin pode apagar.
function comentario_apagar($c, $comentarioId, $usuarioId, $ehAdmin)
{
    $s = $c->prepare("SELECT usuario_id FROM comentario WHERE id = ?");
    $s->execute([(int) $comentarioId]);
    $comentario = $s->fetch(PDO::FETCH_ASSOC);

    if (!$comentario) {
        return; // já não existe
    }

    if (!$ehAdmin && (int) $comentario['usuario_id'] !== (int) $usuarioId) {
        throw new Exception("Você só pode apagar os seus próprios comentários.");
    }

    $c->prepare("DELETE FROM comentario WHERE id = ?")->execute([(int) $comentarioId]);
}


// Comentários de vários jogos, agrupados: [jogo_id => [comentario, ...]]
// Os mais novos aparecem primeiro.
function comentarios_por_jogo($c, $ids)
{
    $resultado = [];
    $ids = array_values(array_map('intval', $ids));

    if (!$ids) {
        return $resultado;
    }

    $marcadores = implode(',', array_fill(0, count($ids), '?'));

    $s = $c->prepare(
        "SELECT cm.id, cm.jogo_id, cm.usuario_id, cm.texto, cm.criado_em, u.nome
         FROM comentario cm
         LEFT JOIN usuarios u ON u.id = cm.usuario_id
         WHERE cm.jogo_id IN ($marcadores)
         ORDER BY cm.criado_em DESC"
    );
    $s->execute($ids);

    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $linha) {
        $resultado[(int) $linha['jogo_id']][] = $linha;
    }

    return $resultado;
}
