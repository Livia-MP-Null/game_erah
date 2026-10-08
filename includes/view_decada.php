<?php
// ============================================================
// VISUAL DA PÁGINA DE UMA DÉCADA
// ============================================================
// Este arquivo só DESENHA a página. Quem entrega os dados é o decada.php
// (dados reais do banco) ou o preview_decada.php (dados de mentirinha).
//
// Variáveis esperadas:
//   $d               década (ex.: 1960)
//   $decadas         lista de décadas do site
//   $jogos           jogos da década
//   $classif         classificações  [jogo_id => [categoria, posicao]]
//   $interacoes      curtidas/favoritos  [jogo_id => [...]]
//   $comentarios     comentários  [jogo_id => [...]]
//   $logado, $usuarioAtual, $ehAdmin
//   $anterior, $proxima   décadas vizinhas (ou null)
//   $erroInteracao   mensagem de erro (ou '')


// Desenha um botão de favoritar/curtir.
// Logado: é um formulário que envia para interagir.php.
// Não logado: é um link que leva ao login.
$botaoInteracao = function ($acao, $classe, $icone, $texto, $ativo, $total, $jogoId) use ($logado, $d) {

    $rotulo  = '<span class="icone">' . $icone . '</span> ' . esc_html($texto)
             . ' <small>(' . (int) $total . ')</small>';
    $classes = 'btn-icone ' . $classe . ($ativo ? ' ativo' : '');

    if (!$logado) {
        echo '<a class="' . $classes . '" href="' . BASE_URL . '/login/login.php"'
           . ' title="Entre para usar este botão">' . $rotulo . '</a>';
        return;
    }

    echo '<form method="post" action="' . BASE_URL . '/interagir.php">'
       . '<input type="hidden" name="acao" value="' . esc_html($acao) . '">'
       . '<input type="hidden" name="jogo_id" value="' . (int) $jogoId . '">'
       . '<input type="hidden" name="decada" value="' . (int) $d . '">'
       . '<button type="submit" class="' . $classes . '">' . $rotulo . '</button>'
       . '</form>';
};

$padraoInteracao = ['curtidas' => 0, 'favoritos' => 0, 'curtiu' => false, 'favoritou' => false];
?>

<main class="pagina-decada">

    <!-- Botão redondo de voltar (como no protótipo) -->
    <a class="voltar-circulo" href="<?= BASE_URL ?>/jogos.php"
       aria-label="Voltar às décadas" title="Voltar às décadas">←</a>

    <div class="decada-topo">
        <span class="btn-decada grande d-<?= (int) $d ?>"><?= esc_html($decadas[$d]) ?></span>
        <p class="sub">
            <?= count($jogos) ?> <?= count($jogos) === 1 ? 'jogo cadastrado' : 'jogos cadastrados' ?>
        </p>
    </div>

    <?php if ($erroInteracao): ?>
        <p class="msg-erro"><?= esc_html($erroInteracao) ?></p>
    <?php endif; ?>

    <?php if (!$jogos): ?>
        <p class="vazio">Ainda não há jogos desta década. Volte em breve!</p>
    <?php endif; ?>


    <?php foreach ($jogos as $indice => $j): ?>

        <?php
        $id    = (int) $j['id'];
        $inter = $interacoes[$id] ?? $padraoInteracao;
        $lista = $comentarios[$id] ?? [];
        ?>

        <?php if ($indice > 0): ?>
            <hr class="separador">
        <?php endif; ?>

        <!-- id="jogo-N" permite voltar exatamente neste jogo depois de curtir/comentar -->
        <article class="jogo-secao" id="jogo-<?= $id ?>">

            <!-- Destaques da década (a classificação) + nome do jogo -->
            <div class="jogo-cabecalho">

                <?php foreach ($classif[$id] ?? [] as $cl): ?>
                    <span class="selo">
                        🏆 <?= esc_html($cl['categoria']) ?> · <?= (int) $cl['posicao'] ?>º lugar
                    </span>
                <?php endforeach; ?>

                <h2 class="jogo-titulo"><?= esc_html($j['titulo']) ?></h2>

            </div>

            <div class="jogo-corpo">

                <div class="jogo-texto">
                    <p class="jogo-meta">
                        <strong>Lançamento:</strong> <?= (int) $j['ano_lancamento'] ?><br>
                        <strong>Desenvolvedora:</strong> <?= esc_html($j['desenvolvedora']) ?><br>
                        <strong>Feito por:</strong> <?= esc_html($j['criado_por'] ?? '') ?>
                    </p>
                    <p class="jogo-descricao"><?= nl2br(esc_html($j['descricao'])) ?></p>
                </div>

                <?php if (!empty($j['imagem']) || !empty($j['video'])): ?>
                    <div class="jogo-midia">

                        <?php if (!empty($j['imagem'])): ?>
                            <img src="<?= URL_UPLOADS . esc_html($j['imagem']) ?>"
                                 alt="<?= esc_html($j['titulo']) ?>">
                        <?php endif; ?>

                        <?php if (!empty($j['video'])): ?>
                            <video src="<?= URL_UPLOADS . esc_html($j['video']) ?>"
                                   controls preload="metadata"></video>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>

            <!-- Favoritar e curtir -->
            <div class="acoes-jogo">
                <?php
                $botaoInteracao('favoritar', 'favorito', '★',
                    $inter['favoritou'] ? 'favoritado' : 'favoritar',
                    $inter['favoritou'], $inter['favoritos'], $id);

                $botaoInteracao('curtir', 'curtir', '♥',
                    $inter['curtiu'] ? 'curtido' : 'curtir',
                    $inter['curtiu'], $inter['curtidas'], $id);
                ?>
            </div>

            <!-- Comentários -->
            <div class="comentarios">

                <h3>Comentários (<?= count($lista) ?>)</h3>

                <?php if ($logado): ?>

                    <form method="post" action="<?= BASE_URL ?>/interagir.php" class="comentario-form">
                        <input type="hidden" name="acao" value="comentar">
                        <input type="hidden" name="jogo_id" value="<?= $id ?>">
                        <input type="hidden" name="decada" value="<?= (int) $d ?>">

                        <label for="comentario-<?= $id ?>">Comentário:</label>
                        <textarea name="texto" id="comentario-<?= $id ?>" rows="3"
                                  maxlength="1000" required
                                  placeholder="O que você achou desse jogo?"></textarea>

                        <button type="submit" class="btn-admin">Comentar</button>
                    </form>

                <?php else: ?>

                    <p><a href="<?= BASE_URL ?>/login/login.php">Entre</a> para comentar.</p>

                <?php endif; ?>

                <?php foreach ($lista as $cm): ?>

                    <div class="comentario">

                        <strong><?= esc_html($cm['nome'] ?? 'Usuário removido') ?></strong>
                        <time><?= esc_html(date('d/m/Y H:i', strtotime($cm['criado_em']))) ?></time>

                        <p><?= nl2br(esc_html($cm['texto'])) ?></p>

                        <?php if ($logado && ($ehAdmin || (int) $cm['usuario_id'] === (int) $usuarioAtual)): ?>
                            <form method="post" action="<?= BASE_URL ?>/interagir.php"
                                  onsubmit="return confirm('Apagar este comentário?');">
                                <input type="hidden" name="acao" value="apagar_comentario">
                                <input type="hidden" name="comentario_id" value="<?= (int) $cm['id'] ?>">
                                <input type="hidden" name="jogo_id" value="<?= $id ?>">
                                <input type="hidden" name="decada" value="<?= (int) $d ?>">
                                <button type="submit" class="btn-perigo">Apagar</button>
                            </form>
                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        </article>

    <?php endforeach; ?>


    <!-- Década anterior / próxima -->
    <nav class="decada-nav">

        <?php if ($anterior): ?>
            <a class="btn-decada d-<?= (int) $anterior ?>"
               href="<?= BASE_URL ?>/decada.php?d=<?= (int) $anterior ?>">← <?= esc_html($decadas[$anterior]) ?></a>
        <?php else: ?><span></span><?php endif; ?>

        <?php if ($proxima): ?>
            <a class="btn-decada d-<?= (int) $proxima ?>"
               href="<?= BASE_URL ?>/decada.php?d=<?= (int) $proxima ?>"><?= esc_html($decadas[$proxima]) ?> →</a>
        <?php else: ?><span></span><?php endif; ?>

    </nav>

</main>
