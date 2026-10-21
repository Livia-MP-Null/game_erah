<?php

// Importa as funções gerais do sistema.
// Esse arquivo contém funções como esc_html() e também a conexão com o banco.
require_once __DIR__ . '/../includes/functions.php';

// Verifica se o usuário está logado e possui permissão de administrador.
// Se não for administrador, o acesso à página é bloqueado.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Importa as funções específicas relacionadas aos jogos.
// Aqui estão funções como jogos_listar(), jogos_total(), jogos_por_decada()
// e classificacoes_todas().
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// MENSAGEM DE ERRO
// ============================================================

// Tenta pegar uma mensagem de erro que foi enviada pela sessão.
// O operador ?? '' significa:
// se $_SESSION['erro_jogos'] existir, usa seu valor;
// caso contrário, usa uma string vazia.
$erro = $_SESSION['erro_jogos'] ?? '';

// Depois de pegar a mensagem, remove ela da sessão.
// Assim, o erro não aparece novamente quando a página for recarregada.
unset($_SESSION['erro_jogos']);


// ============================================================
// BUSCAR DADOS DOS JOGOS
// ============================================================

// Busca todos os jogos cadastrados no banco de dados.
$jogos = jogos_listar($conexao);

// Conta quantos jogos existem no banco de dados.
$total = jogos_total($conexao);

// Busca a quantidade de jogos agrupada por década.
$porDecada = jogos_por_decada($conexao);

// Busca todas as classificações dos jogos.
// O resultado será utilizado posteriormente na tabela.
$classif = classificacoes_todas($conexao);


// ============================================================
// MENSAGENS DE SUCESSO
// ============================================================

// Cria uma lista de mensagens que podem ser exibidas
// depois de alguma ação realizada no sistema.
$mensagens = [

    // Mensagem exibida depois que um jogo é cadastrado.
    'criado' => 'Jogo cadastrado com sucesso.',

    // Mensagem exibida depois que um jogo é atualizado.
    'atualizado' => 'Jogo atualizado com sucesso.',

    // Mensagem exibida depois que um jogo é apagado.
    'apagado' => 'Jogo apagado.',

    // Mensagem exibida quando o jogo procurado não existe.
    'nao_encontrado' => 'Esse jogo não existe mais (talvez já tenha sido apagado).',

];


// Pega o valor "ok" enviado pela URL.
//
// Exemplo:
// jogos_select.php?ok=criado
//
// Nesse caso, $_GET['ok'] será "criado" e a mensagem
// correspondente será mostrada.
//
// Se não existir nenhum valor, utiliza uma string vazia.
$ok = $mensagens[$_GET['ok'] ?? ''] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <!-- Define a codificação de caracteres utilizada pela página. -->
    <meta charset="UTF-8">

    <!-- Faz a página se adaptar a diferentes tamanhos de tela. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- ========================================================
         ARQUIVOS CSS
         ======================================================== -->

    <!-- CSS principal do site. -->
    <link rel="stylesheet" href="../style/style.css">

    <!-- CSS responsável pelo cabeçalho do site. -->
    <link rel="stylesheet" href="../style/header.css">

    <!-- CSS específico da área administrativa. -->
    <link rel="stylesheet" href="../style/adm_style.css">

    <!-- CSS específico da página de jogos. -->
    <link rel="stylesheet" href="../style/jogos.css">


    <!-- Título que aparece na aba do navegador. -->
    <title>Relatório de jogos</title>

</head>

<body>

    <!-- Inclui o cabeçalho do site. -->
    <?php include __DIR__ . '/../includes/header.php'; ?>


    <!-- ========================================================
         CONTEÚDO PRINCIPAL
         ======================================================== -->

    <main class="admin-conteudo">

        <!-- Título e descrição da página. -->
        <div class="admin-titulo">

            <!-- Título principal. -->
            <h1>Relatório de jogos</h1>

            <!-- Texto explicativo da página. -->
            <p>Gerencie os jogos cadastrados no game.erah.</p>

        </div>


        <!-- ====================================================
             MENSAGEM DE ERRO
             ==================================================== -->

        <?php if ($erro): ?>

            <!--
                Só mostra esta mensagem se $erro possuir algum valor.

                esc_html() protege o conteúdo antes de colocá-lo
                dentro do HTML.
            -->
            <p class="msg-erro">
                <?= esc_html($erro) ?>
            </p>

        <?php endif; ?>


        <!-- ====================================================
             MENSAGEM DE SUCESSO
             ==================================================== -->

        <?php if ($ok): ?>

            <!--
                Só mostra esta mensagem se $ok possuir algum valor.

                Por exemplo:
                "Jogo cadastrado com sucesso."
            -->
            <p class="msg-ok">
                <?= esc_html($ok) ?>
            </p>

        <?php endif; ?>


        <!-- ====================================================
             RESUMO DOS JOGOS
             ==================================================== -->

        <section class="resumo">

            <!-- Mostra a quantidade total de jogos cadastrados. -->
            <div class="resumo-item">

                <!--
                    Exibe o número total de jogos.
                    $total vem da função jogos_total().
                -->
                <strong>
                    <?= $total ?>
                </strong>

                <!-- Texto que acompanha o número. -->
                <span>
                    jogos no total
                </span>

            </div>


            <!--
                Percorre os resultados encontrados em $porDecada.

                Cada $linha representa uma década e a quantidade
                de jogos cadastrados nela.
            -->
            <?php foreach ($porDecada as $linha): ?>

                <div class="resumo-item">

                    <!--
                        Mostra a quantidade de jogos daquela década.

                        (int) transforma o valor em número inteiro.
                    -->
                    <strong>
                        <?= (int) $linha['total'] ?>
                    </strong>

                    <!--
                        Mostra a década.

                        Exemplo:
                        anos 1960
                        anos 1970
                        anos 1980
                    -->
                    <span>
                        anos <?= (int) $linha['decada'] ?>
                    </span>

                </div>

            <?php endforeach; ?>

        </section>


        <!-- ====================================================
             BOTÕES DE AÇÃO
             ==================================================== -->

        <div class="admin-acoes">

            <!--
                Link que leva para a página de cadastro de um novo jogo.
            -->
            <a class="btn-admin" href="jogos_create.php">
                + Novo jogo
            </a>


            <!--
                Link que retorna para o painel administrativo.
            -->
            <a class="btn-secundario" href="painel.php">
                Voltar ao painel
            </a>

        </div>


        <!-- ====================================================
             VERIFICAÇÃO DE JOGOS
             ==================================================== -->

        <!--
            Verifica se não existem jogos cadastrados.

            O ! significa "não".

            Portanto:
            !$jogos = não existem jogos.
        -->
        <?php if (!$jogos): ?>

            <!-- Área exibida quando não existem jogos. -->
            <div class="admin-vazio">

                <!-- Mensagem informando que não há jogos. -->
                <p>
                    Nenhum jogo cadastrado ainda.
                </p>


                <!--
                    Botão para cadastrar o primeiro jogo.
                -->
                <a class="btn-admin" href="jogos_create.php">
                    Cadastrar primeiro jogo
                </a>

            </div>


        <?php else: ?>


            <!-- =================================================
                 TABELA DE JOGOS
                 ================================================= -->

            <!--
                Container utilizado para permitir rolagem horizontal
                caso a tabela seja maior que a tela.
            -->
            <div class="tabela-rolagem">

                <!-- Tabela que apresenta os jogos cadastrados. -->
                <table class="tabela-jogos">


                    <!-- ==========================================
                         CABEÇALHO DA TABELA
                         ========================================== -->

                    <thead>

                        <tr>

                            <!-- ID do jogo. -->
                            <th>ID</th>

                            <!-- Imagem do jogo. -->
                            <th>Imagem</th>

                            <!-- Nome do jogo. -->
                            <th>Nome</th>

                            <!-- Classificações do jogo. -->
                            <th>Classificação</th>

                            <!-- Desenvolvedora do jogo. -->
                            <th>Desenvolvedora</th>

                            <!-- Ano de lançamento. -->
                            <th>Ano</th>

                            <!-- Década calculada a partir do ano. -->
                            <th>Década</th>

                            <!-- Indica se o jogo possui vídeo. -->
                            <th>Vídeo</th>

                            <!-- Botões para editar e apagar. -->
                            <th>Ações</th>

                        </tr>

                    </thead>


                    <!-- ==========================================
                         CORPO DA TABELA
                         ========================================== -->

                    <tbody>

                        <!--
                            Percorre todos os jogos encontrados.

                            A cada repetição:
                            $j representa um jogo diferente.
                        -->
                        <?php foreach ($jogos as $j): ?>

                            <tr>


                                <!-- ==================================
                                     ID
                                     ================================== -->

                                <td>

                                    <!--
                                        Mostra o ID do jogo.

                                        (int) garante que o valor seja
                                        tratado como número inteiro.
                                    -->
                                    <?= (int) $j['id'] ?>

                                </td>


                                <!-- ==================================
                                     IMAGEM
                                     ================================== -->

                                <td>

                                    <!--
                                        Verifica se o jogo possui uma imagem.

                                        Se $j['imagem'] tiver algum valor,
                                        a imagem será exibida.
                                    -->
                                    <?php if ($j['imagem']): ?>

                                        <!--
                                            ATENÇÃO: comentário HTML NUNCA pode ficar
                                            DENTRO de uma tag (entre <img e o >).
                                            Por isso as explicações ficam aqui, antes da tag.

                                            URL_UPLOADS contém o caminho da pasta onde as
                                            imagens estão.

                                            esc_html() protege o nome do arquivo antes de
                                            colocá-lo no HTML.

                                            O atributo alt (texto alternativo) usa o título
                                            do jogo.
                                        -->
                                        <img
                                            class="miniatura"
                                            src="<?= URL_UPLOADS . esc_html($j['imagem']) ?>"
                                            alt="<?= esc_html($j['titulo']) ?>"
                                        >

                                    <?php else: ?>

                                        <!--
                                            Se não houver imagem cadastrada,
                                            mostra um traço.
                                        -->
                                        —

                                    <?php endif; ?>

                                </td>


                                <!-- ==================================
                                     NOME DO JOGO
                                     ================================== -->

                                <td>

                                    <!--
                                        Mostra o título do jogo em destaque.
                                    -->
                                    <strong class="nome-jogo">
                                        <?= esc_html($j['titulo']) ?>
                                    </strong>

                                </td>


                                <!-- ==================================
                                     CLASSIFICAÇÕES
                                     ================================== -->

                                <td>

                                    <!--
                                        Verifica se o jogo possui alguma
                                        classificação.

                                        $j['id'] identifica o jogo.

                                        (int) garante que o ID seja inteiro.
                                    -->
                                    <?php if (!empty($classif[(int) $j['id']])): ?>

                                        <!--
                                            Container que agrupa as
                                            classificações do jogo.
                                        -->
                                        <div class="classificacoes">

                                            <!--
                                                Percorre todas as classificações
                                                daquele jogo.

                                                $cl representa uma classificação.
                                            -->
                                            <?php foreach ($classif[(int) $j['id']] as $cl): ?>

                                                <!--
                                                    Exibe a posição e o nome
                                                    da categoria.

                                                    Exemplo:
                                                    1º · Mais jogados
                                                    5º · Melhor qualidade gráfica
                                                -->
                                                <span class="selo">

                                                    <?= (int) $cl['posicao'] ?>º

                                                    ·

                                                    <?= esc_html($cl['categoria']) ?>

                                                </span>

                                            <?php endforeach; ?>

                                        </div>


                                    <?php else: ?>

                                        <!--
                                            Caso o jogo não possua classificação,
                                            mostra um traço.
                                        -->
                                        <span class="sem-classificacao">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ==================================
                                     DESENVOLVEDORA
                                     ================================== -->

                                <td>

                                    <!--
                                        Mostra o nome da desenvolvedora.

                                        esc_html() protege o texto antes
                                        de colocá-lo na página.
                                    -->
                                    <?= esc_html($j['desenvolvedora']) ?>

                                </td>


                                <!-- ==================================
                                     ANO DE LANÇAMENTO
                                     ================================== -->

                                <td>

                                    <!--
                                        Mostra o ano em que o jogo foi lançado.
                                    -->
                                    <?= (int) $j['ano_lancamento'] ?>

                                </td>


                                <!-- ==================================
                                     DÉCADA
                                     ================================== -->

                                <td>

                                    <!--
                                        Mostra a década do jogo.

                                        Exemplo:
                                        1990s
                                        2000s
                                        2010s

                                        A década é calculada a partir
                                        do ano de lançamento.
                                    -->
                                    <?= (int) $j['decada'] ?>s

                                </td>


                                <!-- ==================================
                                     VÍDEO
                                     ================================== -->

                                <td>

                                    <!--
                                        Verifica se existe um vídeo
                                        cadastrado para o jogo.
                                    -->
                                    <?php if ($j['video']): ?>

                                        <!--
                                            Se existir vídeo, mostra "Sim".
                                        -->
                                        <span class="status-sim">
                                            Sim
                                        </span>

                                    <?php else: ?>

                                        <!--
                                            Se não existir vídeo, mostra "Não".
                                        -->
                                        <span class="status-nao">
                                            Não
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ==================================
                                     AÇÕES
                                     ================================== -->

                                <td class="acoes">


                                    <!-- ==================================
                                         EDITAR
                                         ================================== -->

                                    <!--
                                        Link para editar o jogo.

                                        O ID é enviado pela URL.

                                        Exemplo:
                                        jogos_update.php?id=5

                                        Isso permite que a página de
                                        atualização saiba qual jogo editar.
                                    -->
                                    <a
                                        class="btn-editar"
                                        href="jogos_update.php?id=<?= (int) $j['id'] ?>"
                                    >
                                        Editar
                                    </a>


                                    <!-- ==================================
                                         APAGAR
                                         ================================== -->

                                    <!--
                                        Formulário responsável por apagar
                                        o jogo.

                                        method="post" envia o ID de forma
                                        mais adequada do que colocar o ID
                                        diretamente em um link.

                                        O atributo onsubmit antes de enviar o
                                        formulário pergunta ao administrador se
                                        realmente deseja apagar o jogo.

                                        Se clicar em "Cancelar", confirm()
                                        retorna false e o formulário NÃO é enviado.

                                        ATENÇÃO: este comentário fica FORA da tag
                                        <form>. Dentro da tag (entre <form e o >)
                                        só podem existir atributos, nunca comentários.
                                    -->
                                    <form
                                        action="jogos_delete.php"
                                        method="post"
                                        onsubmit="return confirm('Apagar este jogo? Isso não pode ser desfeito.');"
                                    >

                                        <!--
                                            Campo escondido que envia o ID
                                            do jogo para jogos_delete.php.

                                            O usuário não vê esse campo,
                                            mas seu valor é enviado junto
                                            com o formulário.
                                        -->
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int) $j['id'] ?>"
                                        >


                                        <!--
                                            Botão que envia o formulário
                                            de exclusão.
                                        -->
                                        <button
                                            type="submit"
                                            class="btn-perigo"
                                        >
                                            Apagar
                                        </button>

                                    </form>


                                </td>

                            </tr>

                        <!--
                            Finaliza o foreach.

                            A tabela terá uma linha <tr> para cada jogo
                            encontrado no banco de dados.
                        -->
                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </main>


    <!-- Inclui o rodapé do site. -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>