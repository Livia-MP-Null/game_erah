<?php

// ======================================================================
// select_w.php | CONSULTAR USUÁRIO POR ID (somente administrador)
//
// Este arquivo permite consultar um usuário específico pelo seu ID.
//
// Segurança:
// - Verifica se o usuário possui permissão de administrador.
// - Valida o ID recebido pela URL.
// - Mostra uma mensagem caso o usuário não exista.
// - Nunca exibe a senha do usuário.
// - Utiliza GET porque a consulta apenas lê dados e não altera o banco.
// ======================================================================


// Importa o arquivo que verifica o login e a permissão de administrador.
// Também disponibiliza a conexão com o banco de dados em $conexao.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Importa funções de segurança, como validação de IDs,
// leitura de parâmetros e proteção dos cabeçalhos HTTP.
require_once __DIR__ . '/../includes/seguranca.php';

// Importa as funções responsáveis pelas operações relacionadas aos usuários,
// incluindo a função usuario_buscar().
require_once __DIR__ . '/../includes/functions_usuarios.php';


// Configura cabeçalhos HTTP de segurança para a resposta da página.
cabecalhos_seguranca();

// Verifica se a conexão com o banco de dados está disponível.
// Se não estiver, a função interrompe o processamento conforme sua implementação.
exigir_conexao($conexao ?? null);


// Armazena a mensagem de erro que poderá ser exibida na página.
// Começa como uma string vazia, indicando que ainda não ocorreu erro.
$erro = '';


// Armazena os dados do usuário encontrado.
// null significa que nenhum usuário foi encontrado até o momento.
$usuario = null;


// Guarda o ID recebido pela URL para que ele continue aparecendo no formulário.
// Começa vazio porque ainda não foi informado nenhum ID.
$idDigitado = '';


// Verifica se o parâmetro "id" existe na URL.
//
// Exemplo:
// select_w.php?id=5
//
// Nesse exemplo, $_GET['id'] contém o valor 5.
if (isset($_GET['id'])) {

    // Lê o valor do parâmetro "id" utilizando a função de segurança ler_get().
    // O valor será tratado como texto e protegido quando for exibido no HTML.
    $idDigitado = ler_get('id');


    // Valida o ID recebido.
    // normalizar_id() deve devolver um número inteiro válido
    // ou null caso o valor seja inválido.
    $id = normalizar_id($idDigitado);


    // Verifica se o ID não passou pela validação.
    // === compara o valor e o tipo; aqui, verifica se o resultado é null.
    if ($id === null) {

        // Informa que o ID precisa ser um inteiro maior que zero.
        // Isso evita que valores inválidos sejam usados na consulta.
        $erro = 'Informe um ID válido (número inteiro maior que zero).';

    } else {

        // Tenta executar a consulta ao banco de dados.
        // O try permite tratar possíveis erros sem exibir detalhes técnicos ao usuário.
        try {

            // Procura no banco de dados o usuário que possui o ID informado.
            // O resultado é armazenado na variável $usuario.
            $usuario = usuario_buscar($conexao, $id);


            // Verifica se a função não encontrou nenhum usuário.
            // Se não houver resultado, $usuario será um valor considerado falso.
            if (!$usuario) {

                // Informa que não existe usuário com o ID consultado.
                $erro = 'Não existe usuário com o ID ' . $id . '.';

            }

        } catch (Throwable $e) {

            // Captura erros e exceções que ocorrerem durante a consulta.
            // Throwable abrange exceções e erros do PHP.
            //
            // error_log() registra os detalhes técnicos no log do servidor,
            // em vez de mostrá-los diretamente na página.
            error_log('select_w.php: ' . $e->getMessage());


            // Mostra uma mensagem genérica para o usuário.
            // Isso evita expor informações internas do sistema.
            $erro = 'Não foi possível consultar agora. Tente novamente.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <!-- Define a codificação de caracteres da página. -->
    <meta charset="UTF-8">

    <!-- Permite que a página se adapte a diferentes tamanhos de tela. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- Importa o CSS principal do site. -->
    <link rel="stylesheet" href="../style/style.css">

    <!-- Importa o CSS responsável pelo cabeçalho. -->
    <link rel="stylesheet" href="../style/header.css">

    <!-- Importa o CSS da área administrativa. -->
    <link rel="stylesheet" href="../style/adm_style.css">


    <!-- Define o título exibido na aba do navegador. -->
    <title>Consulta usuário</title>

</head>

<body>


    <!-- ==========================================================
         CABEÇALHO
         ========================================================== -->

    <!--
        Inclui o arquivo header.php nesta página.

        __DIR__ representa o diretório atual do arquivo.
        ../ volta um diretório.
        Depois, o caminho acessa includes/header.php.
    -->
    <?php include __DIR__ . '/../includes/header.php'; ?>


    <!--
        <main> representa o conteúdo principal da página.
        Ajuda a organizar semanticamente o HTML.
    -->
    <main>


        <!-- Título principal da página de consulta. -->
        <h1>Consulta usuário:</h1>


        <!-- ======================================================
             MENSAGEM DE ERRO
             ====================================================== -->

        <!--
            Verifica se a variável $erro contém uma mensagem.

            Se houver um erro, o bloco será exibido.
            Se estiver vazia, ele será ignorado.
        -->
        <?php if ($erro): ?>

            <!--
                Exibe a mensagem de erro.

                class="msg-erro" permite aplicar o estilo definido no CSS.

                escapar() protege o texto antes de inseri-lo no HTML,
                evitando que caracteres especiais sejam interpretados como código.
            -->
            <p class="msg-erro">
                <?= escapar($erro) ?>
            </p>

        <?php endif; ?>


        <!-- ======================================================
             FORMULÁRIO DE CONSULTA
             ====================================================== -->

        <!--
            <form> cria um formulário HTML.

            action="" envia os dados para a própria página.

            method="get" envia os dados pela URL.

            Exemplo:
            select_w.php?id=5

            GET é apropriado aqui porque a consulta apenas busca dados
            e não altera o banco de dados.
        -->
        <form action="" method="get">


            <!--
                <label> identifica o campo de entrada.

                for="id" associa o texto ao campo cujo id é "id".
                Ao clicar no texto, o campo correspondente pode receber foco.
            -->
            <label for="id">ID do usuário:</label>


            <!--
                Campo para informar o ID do usuário.

                type="number" apresenta um campo numérico.
                name="id" define o nome enviado pela URL.
                id="id" identifica o campo no HTML.
                required exige que o campo seja preenchido.
                min="1" indica que o valor mínimo permitido é 1.
                step="1" indica incrementos de números inteiros.
                placeholder mostra uma orientação enquanto o campo está vazio.

                value recupera o ID anteriormente digitado,
                para que ele continue aparecendo após a consulta.

                escapar() protege o valor antes de exibi-lo.
                A validação real também é feita no PHP,
                pois os atributos HTML podem ser contornados.
            -->
            <input
                type="number"
                name="id"
                id="id"
                required
                min="1"
                step="1"
                placeholder="Insira o ID para consultá-lo"
                value="<?= escapar($idDigitado) ?>"
            >


            <!--
                Botão que envia o formulário.

                type="submit" faz o navegador enviar o valor do campo
                para a página usando o método GET.
            -->
            <input type="submit" value="Consultar">


            <!--
                Link para a página que apresenta o relatório completo
                dos usuários cadastrados.
            -->
            <a href="select.php">
                Ver o relatório completo
            </a>

        </form>


        <!-- ======================================================
             RESULTADO DA CONSULTA
             ====================================================== -->

        <!--
            Verifica se um usuário foi encontrado.

            Se $usuario possuir os dados retornados pelo banco,
            a tabela será exibida.

            Se for null ou não contiver um resultado válido,
            a tabela não será mostrada.
        -->
        <?php if ($usuario): ?>


            <!--
                Container que pode permitir rolagem horizontal
                caso a tabela seja maior que a tela.
            -->
            <div class="tabela-rolagem">


                <!--
                    <table> cria uma tabela HTML,
                    organizando as informações em linhas e colunas.
                -->
                <table>


                    <!--
                        <thead> significa "table head".

                        É o cabeçalho da tabela, onde ficam
                        os títulos das colunas.
                    -->
                    <thead>


                        <!--
                            <tr> significa "table row".

                            Cria uma linha da tabela.
                            Aqui, a linha contém os títulos das colunas.
                        -->
                        <tr>

                            <!--
                                <th> significa "table header".

                                Cria uma célula de cabeçalho,
                                utilizada para identificar uma coluna.
                            -->
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Nascimento</th>
                            <th>Ativo</th>
                            <th>Tipo</th>

                        </tr>

                    </thead>


                    <!--
                        <tbody> significa "table body".

                        É o corpo da tabela, onde ficam os dados
                        correspondentes aos títulos do cabeçalho.
                    -->
                    <tbody>


                        <!--
                            Cria uma linha de dados.

                            Como esta página consulta apenas um usuário,
                            será exibida uma única linha com seus dados.
                        -->
                        <tr>


                            <!--
                                <td> significa "table data".

                                Cria uma célula que contém um dado da tabela.

                                (int) converte o ID para um número inteiro.
                                $usuario['id'] acessa o ID retornado pelo banco.
                            -->
                            <td>
                                <?= (int) $usuario['id'] ?>
                            </td>


                            <!--
                                Exibe o nome do usuário.

                                $usuario['nome'] acessa o campo nome
                                do resultado da consulta.

                                escapar() protege o texto antes de exibi-lo.
                            -->
                            <td>
                                <?= escapar($usuario['nome']) ?>
                            </td>


                            <!--
                                Exibe o e-mail do usuário.
                                escapar() protege o conteúdo inserido no HTML.
                            -->
                            <td>
                                <?= escapar($usuario['email']) ?>
                            </td>


                            <!--
                                Exibe a data de nascimento.

                                formatar_data() transforma a data
                                para o formato utilizado pelo site.

                                escapar() protege o resultado antes da exibição.
                            -->
                            <td>
                                <?= escapar(formatar_data($usuario['nasc'])) ?>
                            </td>


                            <!--
                                Informa se o usuário está ativo.

                                pg_para_bool() converte o valor recebido
                                do PostgreSQL para um valor booleano.

                                O operador ternário funciona assim:
                                condição ? valor_se_verdadeiro : valor_se_falso

                                Se ativo for true, mostra "Sim".
                                Se ativo for false, mostra "Não".
                            -->
                            <td>
                                <?= pg_para_bool($usuario['ativo']) ? 'Sim' : 'Não' ?>
                            </td>


                            <!--
                                Mostra o tipo de usuário com base na coluna admin.

                                Se admin for true, mostra "Administrador".
                                Se admin for false, mostra "Comum".
                            -->
                            <td>
                                <?= pg_para_bool($usuario['admin']) ? 'Administrador' : 'Comum' ?>
                            </td>


                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- ==================================================
                 ATALHO PARA EDITAR O USUÁRIO
                 ================================================== -->

            <!--
                Exibe um link para a página de atualização do usuário.

                O ID é enviado pela URL.

                Exemplo:
                update.php?id=5

                Assim, a página update.php pode identificar
                qual usuário deverá ser editado.
            -->
            <p>
                <a
                    class="btn-admin"
                    href="update.php?id=<?= (int) $usuario['id'] ?>"
                >
                    Editar este usuário
                </a>
            </p>


        <?php endif; ?>


    </main>


    <!--
        Inclui o rodapé do site.
        O conteúdo é carregado a partir de includes/footer.php.
    -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>


</body>

</html>