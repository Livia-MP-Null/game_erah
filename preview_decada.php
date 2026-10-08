<?php
// PRÉVIA DO VISUAL, SEM BANCO DE DADOS.
// Usa dados de mentirinha só para você ver (e ajustar) o layout.
//
//   preview_decada.php              -> como visitante (não logado)
//   preview_decada.php?logado=1     -> como usuário logado (mostra formulário de comentário)
//
// Esta página NÃO mexe na sessão: o topo do site continua mostrando o seu estado real.
// APAGUE ESTE ARQUIVO antes de publicar o site.

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions_jogos.php';

$decadas = decadas_site();
$d = 1960;

$logado       = isset($_GET['logado']);
$usuarioAtual = 99;
$ehAdmin      = false;

$jogos = [
    [
        'id' => 1, 'titulo' => 'Spacewar!', 'ano_lancamento' => 1962,
        'desenvolvedora' => 'MIT', 'criado_por' => 'Steve Russell, Martin Graetz e Wayne Wiitanen',
        'descricao' => "Um dos primeiros jogos de computador e de simulação de combate espacial da história.\n\nDois jogadores controlam naves em um campo gravitacional, com combustível e torpedos limitados.",
        'imagem' => null, 'video' => null,
    ],
    [
        'id' => 2, 'titulo' => 'Jogo de exemplo', 'ano_lancamento' => 1967,
        'desenvolvedora' => 'Estúdio Exemplo', 'criado_por' => 'Fulano de Tal',
        'descricao' => 'Só para você ver como fica quando existe mais de um jogo na mesma década: uma linha separa um do outro.',
        'imagem' => null, 'video' => null,
    ],
];

$classif = [
    1 => [
        ['categoria' => 'Mais jogados', 'posicao' => 1],
        ['categoria' => 'Melhor qualidade gráfica', 'posicao' => 1],
    ],
];

$interacoes = [
    1 => ['curtidas' => 12, 'favoritos' => 5, 'curtiu' => true, 'favoritou' => false],
];

$comentarios = [
    1 => [
        ['id' => 1, 'usuario_id' => 99, 'nome' => 'Você (exemplo)', 'texto' => 'Que clássico!', 'criado_em' => '2026-10-07 15:30:00'],
        ['id' => 2, 'usuario_id' => 7,  'nome' => 'Maria',          'texto' => 'Joguei na faculdade.', 'criado_em' => '2026-10-06 09:10:00'],
    ],
];

$anterior = null;
$proxima  = 1970;
$erroInteracao = '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/jogos.css">
    <title>Prévia | game.erah</title>
</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <?php include __DIR__ . '/includes/view_decada.php'; ?>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>
