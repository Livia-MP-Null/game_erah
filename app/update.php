<?php
// ======================================================================
// update.php | ATUALIZAR USUÁRIO (somente administrador)
//
// Este arquivo permite consultar os dados de um usuário e atualizá-los.
//
// Medidas de segurança que devem aparecer:
// - verificar se a pessoa está logada e possui permissão de administrador;
// - utiliza token CSRF para proteger o envio do formulário;
// - verificar se o ID informado é válido e se o usuário existe;
// - validar os dados recebidos antes de atualizar o banco;
// - impedeir que dois usuários diferentes utilizem o mesmo e-mail;
// - permitir deixar a senha em branco para manter a senha atual;
// - registrar detalhes de erros internos no log, sem exibi-los na tela.
// ======================================================================


// ----------------------------------------------------------------------
// 1. INCLUSÃO DOS ARQUIVOS NECESSÁRIOS
// ----------------------------------------------------------------------

// Inclui a verificação de autenticação e autorização.
// Confere se a pessoa está logada e se é administradora.
// Esse arquivo também disponibiliza a conexão com o banco em $conexao.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Inclui as ferramentas de segurança, como:
// - proteção contra envio de formulário forjado (CSRF);
// - leitura segura dos dados enviados;
// - redirecionamentos e mensagens temporárias.
require_once __DIR__ . '/../includes/seguranca.php';

// Inclui as funções responsáveis pelas operações com usuários.
// Exemplo: buscar, validar e atualizar os dados de um usuário.
require_once __DIR__ . '/../includes/functions_usuarios.php';


// ----------------------------------------------------------------------
// 2. CONFIGURAÇÕES DE SEGURANÇA E CONEXÃO
// ----------------------------------------------------------------------

// Envia cabeçalhos HTTP que ajudam a proteger a página.
// A implementação desses cabeçalhos está em seguranca.php.
cabecalhos_seguranca();

// Verifica se a conexão com o banco de dados está disponível.
// O operador ?? utiliza null caso $conexao não esteja definida.
// Se a conexão não estiver disponível, a função interrompe o processo
// conforme o comportamento definido em seguranca.php.
exigir_conexao($conexao ?? null);


// ----------------------------------------------------------------------
// 3. INICIALIZAÇÃO DAS VARIÁVEIS
// ----------------------------------------------------------------------

// Array que armazenará as mensagens de erro.
// Um array permite mostrar mais de um erro na mesma página.
$erros = [];

// Guarda os dados que serão exibidos no formulário.
// Inicialmente, os campos estão vazios.
// O ID será preenchido quando um usuário for localizado.
$dados = [
    'id'    => '',
    'nome'  => '',
    'email' => '',
    'nasc'  => ''
];


// ----------------------------------------------------------------------
// 4. CARREGAMENTO DOS DADOS AO ABRIR A PÁGINA
// ----------------------------------------------------------------------

// REQUEST_METHOD identifica o método HTTP utilizado na requisição.
//
// GET: normalmente usado para abrir a página e consultar informações.
// POST: normalmente usado para enviar o formulário.
//
// Este bloco só é executado quando a página NÃO foi enviada por POST
// e existe um parâmetro chamado "id" na URL.
//
// Exemplo:
// update.php?id=5
//
// Nesse exemplo, a página tentará carregar os dados do usuário de ID 5.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['id'])) {

    // Lê o ID recebido pela URL e normaliza seu formato.
    // normalizar_id() deve devolver um inteiro positivo ou null,
    // conforme as regras implementadas na função.
    $idUrl = normalizar_id(ler_get('id'));

    // Se o ID for válido, busca o usuário no banco de dados.
    // Se for inválido, não realiza a busca e utiliza false.
    $encontrado = $idUrl ? usuario_buscar($conexao, $idUrl) : false;

    // Verifica se a busca encontrou um usuário.
    if ($encontrado) {

        // Copia os dados encontrados para o array $dados.
        // Assim, os campos do formulário aparecem preenchidos.
        //
        // O ID é convertido para string porque será exibido
        // em um campo de formulário HTML.
        $dados = [
            'id'    => (string) $encontrado['id'],
            'nome'  => $encontrado['nome'],
            'email' => $encontrado['email'],
            'nasc'  => $encontrado['nasc'],
        ];

    } else {

        // Se o ID for inválido ou não existir no banco,
        // adiciona uma mensagem ao array de erros.
        $erros[] = 'Não existe usuário com o ID informado.';
    }
}


// ----------------------------------------------------------------------
// 5. PROCESSAMENTO DO FORMULÁRIO ENVIADO
// ----------------------------------------------------------------------

// Verifica se a pessoa enviou o formulário pelo método POST.
// O código de atualização só será executado nessa situação.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // try permite executar operações que podem gerar exceções.
    // Se ocorrer um erro, o processamento será encaminhado
    // para um dos blocos catch abaixo.
    try {

        // 5.1. VERIFICAÇÃO DO TOKEN CSRF
        // Confere se o formulário enviado possui um token válido.
        // Isso ajuda a impedir que outro site provoque uma alteração
        // não autorizada utilizando a sessão da pessoa.
        csrf_verificar();


        // 5.2. LEITURA E VALIDAÇÃO DO ID
        // Lê o ID enviado pelo formulário e verifica seu formato.
        // O resultado deve ser um inteiro positivo ou null.
        $id = normalizar_id(ler_post('id'));

        // Se o ID não for válido, interrompe o fluxo de atualização
        // lançando uma exceção específica para erros de validação.
        if ($id === null) {
            throw new ErroValidacao(
                'Informe um ID válido (número inteiro maior que zero).'
            );
        }


        // 5.3. VERIFICAÇÃO DA EXISTÊNCIA DO USUÁRIO
        // Mesmo que o ID seja um número válido, ele pode não existir.
        // Por isso, é necessário consultar o banco antes de atualizar.
        if (!usuario_buscar($conexao, $id)) {
            throw new ErroValidacao(
                'Não existe usuário com o ID ' . $id . '.'
            );
        }


        // 5.4. LEITURA E NORMALIZAÇÃO DOS CAMPOS
        // ler_post() obtém os valores enviados pelo formulário.
        //
        // usuario_normalizar() prepara os dados conforme as regras
        // definidas em functions_usuarios.php, por exemplo,
        // removendo espaços desnecessários ou normalizando valores.
        //
        // A senha também é recebida, mas é opcional na atualização.
        $dados = usuario_normalizar([
            'nome'  => ler_post('nome'),
            'email' => ler_post('email'),
            'senha' => ler_post('senha'),
            'nasc'  => ler_post('nasc'),
        ]);

        // Guarda o ID validado no array para que ele continue
        // disponível no formulário caso ocorra algum erro.
        $dados['id'] = (string) $id;


        // 5.5. VALIDAÇÃO DOS DADOS
        // Verifica se os campos respeitam as regras definidas
        // em usuario_erros_validacao().
        //
        // O primeiro false indica que esta operação não é um cadastro
        // novo, conforme o contrato dessa função.
        //
        // O segundo false indica que a atualização não está tratando
        // o campo "ativo" por meio desta validação.
        //
        // A senha é opcional neste formulário: se ficar em branco,
        // a função de atualização deverá manter a senha atual.
        $erros = usuario_erros_validacao($dados, false, false);


        // 5.6. VERIFICAÇÃO DE E-MAIL DUPLICADO
        // Só verifica o e-mail se não houver erros anteriores.
        //
        // usuario_email_em_uso() recebe o ID atual para distinguir
        // o próprio usuário dos demais registros.
        //
        // Assim, o usuário pode manter seu e-mail atual, mas não pode
        // utilizar um e-mail que pertença a outra pessoa.
        if (
            !$erros &&
            usuario_email_em_uso($conexao, $dados['email'], $id)
        ) {
            $erros[] = 'Este e-mail já pertence a outro usuário.';
        }


        // 5.7. ATUALIZAÇÃO NO BANCO DE DADOS
        // Só executa a atualização quando todas as validações passam.
        if (!$erros) {

            // Chama a função responsável por atualizar o registro.
            // Ela recebe a conexão, o ID do usuário e os novos dados.
            // A regra de senha opcional deve ser tratada nessa função:
            // senha vazia mantém a senha existente.
            usuario_atualizar($conexao, $id, $dados);


            // 5.8. MENSAGEM DE SUCESSO
            // Armazena uma mensagem temporária na sessão.
            // A chave "ok" identifica o tipo de mensagem.
            // A função flash_definir() está em seguranca.php.
            $flashMensagem = 'Usuário ' . $id . ' atualizado com sucesso.';
            flash_definir('ok', $flashMensagem);


            // 5.9. REDIRECIONAMENTO APÓS A ATUALIZAÇÃO
            // Volta para a página de atualização do mesmo usuário.
            //
            // O redirecionamento evita que um simples F5 tente
            // reenviar o formulário POST.
            // O parâmetro id permite carregar os dados atualizados.
            redirecionar('update.php?id=' . $id);
        }

    } catch (ErroValidacao $e) {

        // Trata erros esperados de validação.
        // Exemplos: ID inválido ou usuário inexistente.
        // A mensagem da exceção é adicionada à lista de erros.
        $erros[] = $e->getMessage();

    } catch (Throwable $e) {

        // Trata outros erros, incluindo exceções e erros internos
        // que implementem Throwable.
        //
        // O detalhe técnico é registrado no log do servidor.
        // Ele não é exibido diretamente para a pessoa que acessa o site.
        error_log('update.php: ' . $e->getMessage());

        // Exibe uma mensagem genérica para evitar revelar detalhes
        // internos do banco de dados ou da aplicação.
        $erros[] = 'Não foi possível atualizar agora. Tente novamente.';
    }
}


// ----------------------------------------------------------------------
// 6. PROTEÇÃO E MENSAGEM TEMPORÁRIA
// ----------------------------------------------------------------------

// Remove a senha do array usado para preencher o formulário.
// Isso evita que o valor enviado seja reutilizado na exibição.
// O campo de senha deve permanecer vazio por segurança.
unset($dados['senha']);

// Recupera a mensagem temporária armazenada anteriormente.
// Depois de ser recuperada, ela normalmente deixa de ficar disponível,
// evitando que a mensagem de sucesso se repita indefinidamente.
$flash = flash_pegar();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Define a codificação para exibir corretamente acentos e caracteres especiais. -->
    <meta charset="UTF-8">

    <!-- Faz a página se adaptar à largura da tela de celulares e computadores. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Carrega os estilos gerais, do cabeçalho e da área administrativa. -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/adm_style.css">

    <!-- Define o texto que aparece na aba do navegador. -->
    <title>Atualizar usuário</title>
</head>

<body>

    <!-- Inclui o cabeçalho compartilhado pelas páginas do site. -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <!-- Título principal da página. -->
        <h1>Atualiza dados:</h1>


        <!-- ----------------------------------------------------------
             7. EXIBIÇÃO DA MENSAGEM TEMPORÁRIA
             ---------------------------------------------------------- -->

        <!-- Só exibe o parágrafo se existir uma mensagem flash. -->
        <?php if ($flash): ?>

            <!--
                A classe CSS depende do tipo da mensagem:
                "msg-ok" para sucesso e "msg-erro" para outros tipos.
                escapar() protege o texto exibido na página.
            -->
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>">
                <?= escapar($flash['texto']) ?>
            </p>

        <?php endif; ?>


        <!-- ----------------------------------------------------------
             8. EXIBIÇÃO DOS ERROS DE VALIDAÇÃO
             ---------------------------------------------------------- -->

        <!-- Percorre cada mensagem armazenada no array $erros. -->
        <?php foreach ($erros as $erro): ?>

            <!-- Mostra uma mensagem de erro, protegendo seu conteúdo. -->
            <p class="msg-erro"><?= escapar($erro) ?></p>

        <?php endforeach; ?>


        <!-- ----------------------------------------------------------
             9. FORMULÁRIO DE ATUALIZAÇÃO
             ---------------------------------------------------------- -->

        <!--
            action="" envia os dados para a própria página.
            method="post" envia os campos no corpo da requisição HTTP,
            em vez de colocá-los na URL.
            class="meu-formulario" permite aplicar estilos pelo CSS.
        -->
        <form action="" method="post" class="meu-formulario">

            <!--
                Insere o campo oculto com o token CSRF.
                O token será conferido por csrf_verificar() no PHP.
            -->
            <?= csrf_campo() ?>


            <!-- CAMPO: ID DO USUÁRIO -->
            <div class="campo">

                <!-- O for associa o rótulo ao campo cujo id é "id". -->
                <label for="id">ID:</label>

                <!--
                    type="number": permite informar um número.
                    name="id": nome usado pelo PHP para receber o valor.
                    id="id": identifica o campo no HTML.
                    required: torna o preenchimento obrigatório no navegador.
                    min="1": define o menor valor permitido.
                    step="1": permite apenas incrementos de números inteiros.
                    placeholder: mostra uma orientação quando está vazio.
                    value: recupera o ID armazenado em $dados.
                    escapar(): protege o valor antes de colocá-lo no HTML.
                -->
                <input
                    type="number"
                    name="id"
                    id="id"
                    required
                    min="1"
                    step="1"
                    placeholder="Insira o ID para atualizar"
                    value="<?= escapar($dados['id']) ?>"
                >

            </div>


            <!-- CAMPO: NOME -->
            <div class="campo">

                <label for="nome">Nome:</label>

                <!--
                    type="text": campo de texto comum.
                    name="nome": permite ler o valor com ler_post('nome').
                    required: exige o preenchimento no navegador.
                    minlength e maxlength: usam constantes PHP para limitar
                    o tamanho permitido do nome no formulário.
                    value: mostra o nome atual ou o valor enviado anteriormente.
                -->
                <input
                    type="text"
                    name="nome"
                    id="nome"
                    required
                    minlength="<?= NOME_MIN ?>"
                    maxlength="<?= NOME_MAX ?>"
                    value="<?= escapar($dados['nome']) ?>"
                >

            </div>


            <!-- CAMPO: NOVA SENHA -->
            <div class="campo">

                <!--
                    A senha é opcional na atualização.
                    Se o campo ficar vazio, a senha atual deve ser mantida
                    pela lógica de usuario_atualizar().
                -->
                <label for="senha">
                    Nova senha (deixe em branco para manter a atual):
                </label>

                <!--
                    type="password": oculta os caracteres digitados.
                    name="senha": permite receber a senha pelo PHP.
                    minlength e maxlength: aplicam os limites definidos
                    pelas constantes SENHA_MIN e SENHA_MAX.
                    autocomplete="new-password": informa ao navegador
                    que o campo serve para definir uma nova senha.
                    Não há atributo value: a senha nunca é preenchida
                    automaticamente com o valor anterior.
                -->
                <input
                    type="password"
                    name="senha"
                    id="senha"
                    minlength="<?= SENHA_MIN ?>"
                    maxlength="<?= SENHA_MAX ?>"
                    autocomplete="new-password"
                >

            </div>


            <!-- CAMPO: E-MAIL -->
            <div class="campo">

                <label for="email">E-mail:</label>

                <!--
                    type="email": permite ao navegador verificar o formato
                    básico de um endereço de e-mail.
                    required: torna o preenchimento obrigatório.
                    maxlength: limita o tamanho conforme EMAIL_MAX.
                    value: apresenta o e-mail atual do usuário.
                    A validação no servidor continua necessária.
                -->
                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                    maxlength="<?= EMAIL_MAX ?>"
                    value="<?= escapar($dados['email']) ?>"
                >

            </div>


            <!-- CAMPO: DATA DE NASCIMENTO -->
            <div class="campo">

                <label for="nasc">Nascimento:</label>

                <!--
                    type="date": exibe um campo para selecionar uma data.
                    required: exige o preenchimento.
                    min="1900-01-01": impede datas anteriores a esse limite
                    pela validação do navegador.
                    max="<?= date('Y-m-d') ?>": define a data atual como limite,
                    evitando datas futuras no formulário.
                    value: apresenta a data de nascimento atual.
                    O servidor também deve validar a data recebida.
                -->
                <input
                    type="date"
                    name="nasc"
                    id="nasc"
                    required
                    min="1900-01-01"
                    max="<?= date('Y-m-d') ?>"
                    value="<?= escapar($dados['nasc']) ?>"
                >

            </div>


            <!-- ------------------------------------------------------
                 10. BOTÕES DO FORMULÁRIO
                 ------------------------------------------------------ -->

            <div class="botoes">

                <!--
                    Envia os campos do formulário para o PHP.
                    Como não há type definido, input assume "submit"
                    quando o navegador aplica o padrão HTML.
                -->
                <input type="submit" value="Atualizar">

                <!--
                    Restaura os campos aos valores iniciais do formulário.
                    Não salva nem atualiza dados no banco de dados.
                -->
                <input type="reset" value="Limpar">

            </div>

        </form>

    </main>


    <!-- Inclui o rodapé compartilhado pelas páginas do site. -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
