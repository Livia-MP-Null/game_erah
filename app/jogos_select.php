<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';
require_once __DIR__ . '/../includes/functions_jogos.php';

// Erro vindo de outra página
$erro = $_SESSION['erro_jogos'] ?? '';
unset($_SESSION['erro_jogos']);

$jogos = jogos_listar($conexao);
$total = jogos_total($conexao);
$porDecada = jogos_por_decada($conexao);
$classif = classificacoes_todas($conexao);

$mensagens = [
    'criado'         => 'Jogo cadastrado com sucesso.',
    'atualizado'     => 'Jogo atualizado com sucesso.',
    'apagado'        => 'Jogo apagado.',
    'nao_encontrado' => 'Esse jogo não existe mais (talvez já tenha sido apagado).',
];

$ok = $mensagens[$_GET['ok'] ?? ''] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS principal -->
    <link rel="stylesheet" href="../style/style.css">

    <!-- CSS do cabeçalho -->
    <link rel="stylesheet" href="../style/header.css">

    <!-- CSS da área administrativa -->
    <link rel="stylesheet" href="../style/adm_style.css">

    <!-- CSS específico dos jogos -->
    <link rel="stylesheet" href="../style/jogos.css">

    <title>Relatório de jogos</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-conteudo">

        <div class="admin-titulo">
            <h1>Relatório de jogos</h1>
            <p>Gerencie os jogos cadastrados no game.erah.</p>
        </div>

        <?php if ($erro): ?>
            <p class="msg-erro">
                <?= esc_html($erro) ?>
            </p>
        <?php endif; ?>

        <?php if ($ok): ?>
            <p class="msg-ok">
                <?= esc_html($ok) ?>
            </p>
        <?php endif; ?>


        <!-- RESUMO -->

        <section class="resumo">

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

        </section>


        <!-- BOTÕES -->

        <div class="admin-acoes">

            <a class="btn-admin" href="jogos_create.php">
                + Novo jogo
            </a>

            <a class="btn-secundario" href="painel.php">
                Voltar ao painel
            </a>

        </div>


        <?php if (!$jogos): ?>

            <div class="admin-vazio">
                <p>Nenhum jogo cadastrado ainda.</p>

                <a class="btn-admin" href="jogos_create.php">
                    Cadastrar primeiro jogo
                </a>
            </div>

        <?php else: ?>

            <!-- TABELA -->

            <div class="tabela-rolagem">

                <table class="tabela-jogos">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Classificação</th>
                            <th>Desenvolvedora</th>
                            <th>Ano</th>
                            <th>Década</th>
                            <th>Vídeo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($jogos as $j): ?>

                            <tr>

                                <td>
                                    <?= (int) $j['id'] ?>
                                </td>


                                <td>

                                    <?php if ($j['imagem']): ?>

                                        <img
                                            class="miniatura"
                                            src="<?= URL_UPLOADS . esc_html($j['imagem']) ?>"
                                            alt="<?= esc_html($j['titulo']) ?>"
                                        >

                                    <?php else: ?>

                                        —

                                    <?php endif; ?>

                                </td>


                                <td>
                                    <strong class="nome-jogo">
                                        <?= esc_html($j['titulo']) ?>
                                    </strong>
                                </td>


                                <!-- CLASSIFICAÇÃO -->

                                <td>

                                    <?php if (!empty($classif[(int) $j['id']])): ?>

                                        <div class="classificacoes">

                                            <?php foreach ($classif[(int) $j['id']] as $cl): ?>

                                                <span class="selo">
                                                    <?= (int) $cl['posicao'] ?>º
                                                    ·
                                                    <?= esc_html($cl['categoria']) ?>
                                                </span>

                                            <?php endforeach; ?>

                                        </div>

                                    <?php else: ?>

                                        <span class="sem-classificacao">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>
                                    <?= esc_html($j['desenvolvedora']) ?>
                                </td>


                                <td>
                                    <?= (int) $j['ano_lancamento'] ?>
                                </td>


                                <td>
                                    <?= (int) $j['decada'] ?>s
                                </td>


                                <td>

                                    <?php if ($j['video']): ?>

                                        <span class="status-sim">
                                            Sim
                                        </span>

                                    <?php else: ?>

                                        <span class="status-nao">
                                            Não
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- AÇÕES -->

                                <td class="acoes">

                                    <a
                                        class="btn-editar"
                                        href="jogos_update.php?id=<?= (int) $j['id'] ?>"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="jogos_delete.php"
                                        method="post"
                                        onsubmit="return confirm('Apagar este jogo? Isso não pode ser desfeito.');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $j['id'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn-perigo"
                                        >
                                            Apagar
                                        </button>

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