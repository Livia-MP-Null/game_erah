<?php

// Importa o arquivo com as funções gerais do sistema.
// Ele também disponibiliza funções como esc_html().
require_once __DIR__ . '/../includes/functions.php';

// Verifica se o usuário está logado e se possui permissão de administrador.
// Se não for administrador, o acesso ao painel é bloqueado.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Importa as funções relacionadas aos jogos.
// É utilizado principalmente para contar os jogos cadastrados.
require_once __DIR__ . '/../includes/functions_jogos.php';


// ============================================================
// NOME DO ADMINISTRADOR
// ============================================================

// Pega o nome do usuário que está armazenado na sessão.

// $_SESSION['nome'] contém o nome do usuário que fez login.

// O operador ?? significa:
// se $_SESSION['nome'] existir, utiliza seu valor;
// caso contrário, utiliza "admin".
$nome = $_SESSION['nome'] ?? 'admin';


// ============================================================
// NÚMEROS RÁPIDOS
// ============================================================

// Inicializa as variáveis que armazenarão as quantidades.

// O valor null significa que ainda não existe um número definido.
$totalJogos = null;
$totalUsuarios = null;


try {

    // Conta a quantidade total de jogos cadastrados no banco de dados.
    // A função jogos_total() recebe a conexão com o banco.
    $totalJogos = jogos_total($conexao);


    // Executa uma consulta SQL para contar todos os usuários.
    //
    // SELECT COUNT(*) FROM usuarios
    //
    // COUNT(*) conta quantas linhas existem na tabela usuarios.
    //
    // fetchColumn() pega diretamente o primeiro valor retornado
    // pela consulta, que neste caso é a quantidade de usuários.
    //
    // (int) transforma o resultado em um número inteiro.
    $totalUsuarios = (int) $conexao->query(
        "SELECT COUNT(*) FROM usuarios"
    )->fetchColumn();


} catch (Exception $e) {

    // Se ocorrer algum erro durante a consulta ao banco,
    // o sistema não interrompe completamente a página.
    //
    // error_log() registra a mensagem de erro no log do servidor.
    //
    // Isso é melhor do que mostrar detalhes do erro diretamente
    // para o usuário.
    error_log(
        "Painel: não foi possível contar: " . $e->getMessage()
    );

}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <!--Define a codificação de caracteres utilizada pela página.-->
    <meta charset="UTF-8">

    <!--Faz a página se adaptar a diferentes tamanhos de tela.-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!--Importa o CSS principal do site.-->
    <link rel="stylesheet" href="../style/style.css">

    <!--Importa o CSS responsável pelo cabeçalho.-->
    <link rel="stylesheet" href="../style/header.css">

    <!--Importa o CSS específico da área administrativa.-->
    <link rel="stylesheet" href="../style/adm_style.css">

    <!-- Importa o CSS relacionado aos jogos.-->
    <link rel="stylesheet" href="../style/jogos.css">


    <!--Define o título que aparece na aba do navegador.
    <title>Painel do administrador</title> -->

</head>

<body>

    <!--
        Inclui o cabeçalho do site.

        __DIR__ representa a pasta onde este arquivo está localizado.
        ../ volta uma pasta.
        Depois acessa a pasta includes e o arquivo header.php.
    -->
    <?php include __DIR__ . '/../includes/header.php'; ?>


    <!--
        Área principal do painel administrativo.
    -->
    <main>


        <!-- ====================================================
             SAUDAÇÃO
             ==================================================== -->

        <!--
            Mostra uma mensagem de boas-vindas.

            esc_html() protege o nome antes de colocá-lo no HTML,
            evitando que códigos HTML ou scripts sejam interpretados.
        -->
        <h1>
            Bem-vindo, <?= esc_html($nome) ?>!
        </h1>


        <!--
            Texto explicando ao administrador onde ele está
            e como utilizar o painel.
        -->
        <p class="sub">

            Você está na área do administrador.

            Use a barra no topo da página para ir a qualquer área,
            ou os atalhos abaixo.

        </p>


        <!-- ====================================================
             NÚMEROS RÁPIDOS
             ==================================================== -->

        <!--
            Container que agrupa os números principais
            apresentados no painel.
        -->
        <div class="resumo">


            <!-- ================================================
                 TOTAL DE JOGOS
                 ================================================ -->

            <div class="resumo-item">

                <!--
                    Mostra a quantidade de jogos.

                    $totalJogos ?? '—'

                    significa:
                    se $totalJogos possuir um valor,
                    mostra esse valor.

                    Se estiver null, mostra "—".
                -->
                <strong>
                    <?= $totalJogos ?? '—' ?>
                </strong>


                <!-- Identifica o número apresentado. -->
                <span>
                    jogos
                </span>

            </div>


            <!-- ================================================
                 TOTAL DE USUÁRIOS
                 ================================================ -->

            <div class="resumo-item">

                <!--
                    Mostra a quantidade de usuários cadastrados.

                    Caso a consulta tenha falhado e o valor seja null,
                    será mostrado "—".
                -->
                <strong>
                    <?= $totalUsuarios ?? '—' ?>
                </strong>


                <!-- Identifica o número apresentado. -->
                <span>
                    usuários
                </span>

            </div>

        </div>


        <!-- ====================================================
             ATALHOS DO PAINEL
             ==================================================== -->

        <!--
            Container que organiza os cartões do painel.
        -->
        <div class="painel-grade">


            <!-- =================================================
                 CARTÃO DE USUÁRIOS
                 ================================================= -->

            <section class="painel-cartao">

                <!-- Título do grupo de opções relacionadas aos usuários. -->
                <h2>Usuários</h2>


                <!--
                    Leva para a página de cadastro de um novo usuário.
                -->
                <a href="create.php">
                    Cadastrar usuário
                </a>


                <!--
                    Leva para o relatório/lista de usuários cadastrados.
                -->
                <a href="select.php">
                    Relatório de usuários
                </a>


                <!--
                    Leva para a página que permite consultar
                    um usuário específico.
                -->
                <a href="select_w.php">
                    Consultar usuário
                </a>


                <!--
                    Leva para a página de atualização de usuário.
                -->
                <a href="update.php">
                    Atualizar usuário
                </a>


                <!--
                    Leva para a página de exclusão de usuário.
                -->
                <a href="delete.php">
                    Excluir usuário
                </a>

            </section>


            <!-- =================================================
                 CARTÃO DE JOGOS
                 ================================================= -->

            <section class="painel-cartao">

                <!-- Título do grupo de opções relacionadas aos jogos. -->
                <h2>Jogos</h2>


                <!--
                    Leva para a página de cadastro de um novo jogo.
                -->
                <a href="jogos_create.php">
                    Cadastrar jogo
                </a>


                <!--
                    Leva para a página que mostra o relatório
                    e a lista dos jogos cadastrados.
                -->
                <a href="jogos_select.php">
                    Relatório e lista de jogos
                </a>

            </section>


            <!-- =================================================
                 CARTÃO DO SITE
                 ================================================= -->

            <section class="painel-cartao">

                <!-- Título do grupo de opções relacionadas ao site. -->
                <h2>Site</h2>


                <!--
                    Volta para a página inicial pública do site.

                    ../ significa voltar uma pasta,
                    saindo da pasta "app".
                -->
                <a href="../index.php">
                    Página inicial
                </a>


                <!--
                    Leva para a página pública que apresenta
                    os jogos organizados por década.
                -->
                <a href="../jogos.php">
                    Jogos por década
                </a>

            </section>

        </div>

    </main>


    <!--
        Inclui o rodapé do site.

        O arquivo footer.php fica na pasta includes.
    -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>