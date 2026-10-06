<?php

require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';

$jogos     = jogos_listar($conexao);
$total     = jogos_total($conexao);
$porDecada = jogos_por_decada($conexao);

$mensagens = [
    'criado'     => 'Jogo cadastrado com sucesso.',
    'atualizado' => 'Jogo atualizado com sucesso.',
    'apagado'    => 'Jogo apagado.',
];
$ok = $mensagens[$_GET['ok'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/jogos.css">
    <title>Relatório de jogos</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>
        <h1>Relatório de jogos:</h1>

        <?php if ($ok): ?>
            <p class="msg-ok"><?= esc_html($ok) ?></p>
        <?php endif; ?>

        <div class="resumo">
            <div class="resumo-item">
                <strong><?= $total ?></strong>
                <span>jogos no total</span>
            </div>
            <?php foreach ($porDecada as $linha): ?>
                <div class="resumo-item">
                    <strong><?= (int) $linha['total'] ?></strong>
                    <span>anos <?= (int) $linha['decada'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <p><a class="btn-admin" href="jogos_create.php">+ Novo jogo</a>
           <a href="painel.php">Voltar ao painel</a></p>

        <?php if (!$jogos): ?>
            <p>Nenhum jogo cadastrado ainda.</p>
        <?php else: ?>
            <div class="tabela-rolagem">
                <table class="tabela-jogos">
                    <thead>
                        <tr>
                            <th>ID</th><th>Imagem</th><th>Nome</th><th>Desenvolvedora</th><th>Ano</th><th>Década</th><th>Vídeo</th><th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jogos as $j): ?>
                            <tr>
                                <td><?= (int) $j['id'] ?></td>
                                <td>
                                    <?php if ($j['imagem']): ?>
                                        <img class="miniatura" src="<?= URL_UPLOADS . esc_html($j['imagem']) ?>" alt="">
                                    <?php else: ?>—<?php endif; ?>
                                </td>
                                <td><?= esc_html($j['titulo']) ?></td>
                                <td><?= esc_html($j['desenvolvedora']) ?></td>
                                <td><?= (int) $j['ano_lancamento'] ?></td>
                                <td><?= (int) $j['decada'] ?>s</td>
                                <td><?= $j['video'] ? 'Sim' : 'Não' ?></td>
                                <td class="acoes">
                                    <a href="jogos_update.php?id=<?= (int) $j['id'] ?>">Editar</a>
                                    <form action="jogos_delete.php" method="post"
                                          onsubmit="return confirm('Apagar este jogo? Isso não pode ser desfeito.');">
                                        <input type="hidden" name="id" value="<?= (int) $j['id'] ?>">
                                        <button type="submit" class="btn-perigo">Apagar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
