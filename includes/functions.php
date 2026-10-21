```php
<?php

// ======================================================================
// ARQUIVO DE FUNÇÕES DE USUÁRIOS
// ======================================================================
//
// Este arquivo reúne funções que realizam operações na tabela "usuarios"
// do banco de dados.
//
// Operações disponíveis:
// - cadastrar(): cadastra um usuário;
// - apagar(): exclui um usuário pelo ID;
// - relatorio(): lista os usuários cadastrados;
// - atualizar(): altera os dados de um usuário;
// - consultar_user(): consulta um usuário pelo ID;
// - cadastrar_user(): cadastra um usuário pelo fluxo de login;
// - consulta_user(): busca um usuário pelo e-mail.
//
// O uso de funções evita repetir o mesmo código em várias páginas.
// ======================================================================


// Inclui o arquivo responsável pela conexão com o banco de dados.
// __DIR__ representa a pasta em que este arquivo está localizado.
// O caminho ../database/conect.php aponta para a pasta database.
//
// require_once importa o arquivo apenas uma vez durante a execução,
// evitando inclusões repetidas do mesmo arquivo.
require_once __DIR__ . '/../database/conect.php';


// ======================================================================
// 1. CADASTRAR USUÁRIO
// ======================================================================

// Recebe a conexão e os dados do novo usuário.
// Os parâmetros são fornecidos pela página que chama a função.
//
// $conexao -> conexão PDO com o banco de dados;
// $nome    -> nome do usuário;
// $nasc    -> data de nascimento;
// $senha   -> senha digitada;
// $email   -> endereço de e-mail.
function cadastrar($conexao, $nome, $nasc, $senha, $email)
{
    // Transforma a senha em um hash seguro.
    // A senha original não deve ser armazenada diretamente no banco.
    // PASSWORD_DEFAULT permite que o PHP escolha o algoritmo padrão.
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    // Comando SQL para inserir um novo registro na tabela usuarios.
    //
    // INSERT INTO indica a inserção de um registro.
    // Os nomes entre parênteses representam as colunas que receberão dados.
    // Os valores :nome, :senha, :nasc e :email são parâmetros nomeados.
    $sql = "INSERT INTO usuarios (nome, senha, nasc, email)
            VALUES (:nome, :senha, :nasc, :email)";

    // try tenta executar as operações que podem gerar uma exceção.
    try {

        // Prepara o comando SQL para execução.
        // Os parâmetros serão associados aos valores posteriormente.
        $stmt = $conexao->prepare($sql);

        // Associa a variável $nome ao parâmetro :nome do SQL.
        $stmt->bindParam(":nome", $nome);

        // Associa o hash da senha ao parâmetro :senha.
        $stmt->bindParam(":senha", $senha);

        // Associa a data de nascimento ao parâmetro :nasc.
        $stmt->bindParam(":nasc", $nasc);

        // Associa o e-mail ao parâmetro :email.
        $stmt->bindParam(":email", $email);

        // Executa o comando SQL com os valores associados.
        $stmt->execute();

        // Exibe uma mensagem indicando que a operação foi concluída.
        echo "Usuário inserido com sucesso!";

    } catch (PDOException $e) {

        // Captura erros relacionados à operação com o banco.
        // A mensagem detalhada pode revelar informações internas,
        // portanto, em um site publicado, o ideal é registrá-la no log.
        echo "Erro: " . $e->getMessage();
    }
}


// ======================================================================
// 2. APAGAR USUÁRIO
// ======================================================================

// Exclui um usuário da tabela utilizando seu ID.
//
// $conexao -> conexão com o banco;
// $id      -> identificador do usuário que será excluído.
function apagar($conexao, $id)
{
    // DELETE FROM remove registros da tabela.
    // WHERE id = :id restringe a exclusão ao ID informado.
    // Sem a cláusula WHERE, o comando poderia excluir todos os registros.
    $sql = "DELETE FROM usuarios WHERE id = :id";

    try {

        // Prepara o comando SQL.
        $stmt = $conexao->prepare($sql);

        // Associa o ID recebido ao parâmetro :id.
        $stmt->bindParam(":id", $id);

        // Executa a exclusão no banco de dados.
        $stmt->execute();

        // Informa o ID solicitado na operação.
        // Para confirmar se algum registro foi realmente excluído,
        // seria necessário verificar o resultado da execução.
        echo "Registro $id deletado.";

    } catch (PDOException $e) {

        // Captura e trata erros ocorridos durante a exclusão.
        echo "Erro: " . $e->getMessage();
    }
}


// ======================================================================
// 3. RELATÓRIO DE USUÁRIOS
// ======================================================================

// Consulta os usuários cadastrados e exibe seus dados.
//
// $conexao -> conexão com o banco de dados.
function relatorio($conexao)
{
    // SELECT * solicita todas as colunas da tabela usuarios.
    // ORDER BY id organiza os resultados pelo ID em ordem crescente.
    $sql = "SELECT * FROM usuarios ORDER BY id";

    try {

        // Prepara a consulta SQL.
        $stmt = $conexao->prepare($sql);

        // Executa a consulta.
        $stmt->execute();

        // Recupera todos os registros encontrados.
        //
        // fetchAll() retorna uma lista com os resultados.
        // PDO::FETCH_ASSOC organiza cada registro como um array associativo,
        // em que as chaves são os nomes das colunas.
        //
        // Exemplo: $usuario['nome'] acessa o nome de um registro.
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Percorre a lista de usuários.
        // Em cada repetição, $usuario representa um registro diferente.
        foreach ($usuarios as $usuario) {

            // Insere uma linha horizontal para separar os registros.
            echo "<hr>";

            // Exibe o ID do usuário.
            echo "ID: {$usuario['id']}<br>";

            // Exibe o nome.
            echo "Nome: {$usuario['nome']}<br>";

            // Exibe a data de nascimento.
            echo "Nascimento: {$usuario['nasc']}<br>";

            // Exibe o e-mail.
            echo "E-mail: {$usuario['email']}<br>";

            // ATENÇÃO:
            // Esta linha exibe o hash da senha armazenado no banco.
            // Mesmo sendo um hash, ele não deve ser mostrado no relatório.
            // Em uma versão segura, remova esta linha.
            echo "Senha: {$usuario['senha']}<br>";
        }

    } catch (PDOException $e) {

        // Captura erros ocorridos durante a consulta.
        echo "Erro: " . $e->getMessage();
    }
}


// ======================================================================
// 4. ATUALIZAR USUÁRIO
// ======================================================================

// Altera os dados de um usuário já cadastrado.
//
// $conexao -> conexão com o banco;
// $id      -> ID do usuário que será alterado;
// $nome    -> novo nome;
// $senha   -> nova senha;
// $nasc    -> nova data de nascimento;
// $email   -> novo e-mail.
function atualizar($conexao, $id, $nome, $senha, $nasc, $email)
{
    // Gera um hash para a nova senha.
    //
    // IMPORTANTE:
    // Nesta implementação, a senha é sempre substituída.
    // Se $senha estiver vazia, será gerado um hash da string vazia.
    // Portanto, esta função ainda não permite manter a senha anterior.
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    // UPDATE altera os dados de um registro existente.
    // SET define os novos valores das colunas.
    // WHERE id = :id limita a alteração ao usuário informado.
    $sql = "UPDATE usuarios
            SET nome = :nome,
                senha = :senha,
                nasc = :nasc,
                email = :email
            WHERE id = :id";

    try {

        // Prepara o comando SQL de atualização.
        $stmt = $conexao->prepare($sql);

        // Associa cada parâmetro SQL ao valor correspondente.
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":nasc", $nasc);
        $stmt->bindParam(":email", $email);

        // Executa a atualização no banco.
        $stmt->execute();

        // Exibe uma mensagem após a execução do comando.
        // Para confirmar se houve alteração, pode-se verificar
        // o número de linhas afetadas, conforme o comportamento do banco.
        echo "Usuário atualizado com sucesso!";

    } catch (PDOException $e) {

        // Captura erros relacionados à atualização.
        echo "Erro: " . $e->getMessage();
    }
}


// ======================================================================
// 5. CONSULTAR USUÁRIO PELO ID
// ======================================================================

// Busca um usuário pelo ID e exibe os dados encontrados.
//
// $conexao -> conexão com o banco;
// $id      -> identificador do usuário procurado.
function consultar_user($conexao, $id)
{
    // SELECT define quais colunas serão consultadas.
    // Neste caso, solicita nome, senha, e-mail e nascimento.
    // WHERE id = :id procura apenas o registro correspondente ao ID.
    $sql = "SELECT nome, senha, email, nasc
            FROM usuarios
            WHERE id = :id";

    try {

        // Prepara a consulta SQL.
        $stmt = $conexao->prepare($sql);

        // Associa o ID informado ao parâmetro :id.
        $stmt->bindParam(":id", $id);

        // Executa a consulta.
        $stmt->execute();

        // Recupera apenas um registro.
        // fetch() retorna um array associativo se encontrar um usuário
        // ou false se não houver resultado.
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se a consulta encontrou um usuário.
        if ($usuario) {

            // Exibe o nome encontrado.
            echo "Nome: {$usuario['nome']}<br>";

            // Exibe a data de nascimento.
            echo "Nascimento: {$usuario['nasc']}<br>";

            // Exibe o e-mail.
            echo "E-mail: {$usuario['email']}<br>";

            // Não exiba o hash da senha na página.
            // O correto é remover esta linha.
            echo "Senha: {$usuario['senha']}<br>";

            // Separa visualmente o resultado.
            echo "<hr>";

        } else {

            // Executado quando nenhum usuário corresponde ao ID.
            echo "Usuário não encontrado.";
        }

    } catch (PDOException $e) {

        // Captura erros durante a consulta.
        echo "Erro: " . $e->getMessage();
    }
}


// ======================================================================
// 6. CADASTRAR USUÁRIO PELO FLUXO DE LOGIN
// ======================================================================

// Cadastra um usuário e retorna true em caso de sucesso
// ou false se ocorrer uma exceção PDO.
//
// Esta função possui a mesma finalidade geral de cadastrar(),
// mas recebe os parâmetros em uma ordem diferente e retorna
// um valor que a página chamadora pode utilizar.
function cadastrar_user($conexao, $nome, $email, $senha, $nasc)
{
    // Transforma a senha em um hash antes de armazená-la.
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    // Insere o nome, e-mail, hash da senha e data de nascimento.
    $sql = "INSERT INTO usuarios (nome, email, senha, nasc)
            VALUES (:nome, :email, :senha, :nasc)";

    try {

        // Prepara o comando SQL.
        $stmt = $conexao->prepare($sql);

        // Associa cada valor ao respectivo parâmetro.
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":nasc", $nasc);

        // Executa a inserção.
        $stmt->execute();

        // Informa à página chamadora que o cadastro foi concluído.
        return true;

    } catch (PDOException $e) {

        // Registra o erro no log em vez de expor detalhes do banco.
        error_log('cadastrar_user: ' . $e->getMessage());

        // Informa à página chamadora que houve uma falha.
        return false;
    }
}


// ======================================================================
// 7. CONSULTAR USUÁRIO PELO E-MAIL
// ======================================================================

// Busca um usuário pelo endereço de e-mail.
// Esta função pode ser utilizada durante o login.
//
// $conexao -> conexão com o banco;
// $email   -> endereço de e-mail informado no login.
//
// Retorno:
// - array associativo com os dados encontrados;
// - false quando não houver usuário ou ocorrer uma exceção PDO.
function consulta_user($conexao, $email)
{
    try {

        // Solicita apenas as colunas necessárias ao login:
        // ID para identificar o usuário;
        // e-mail para identificar a conta;
        // senha para verificar a senha digitada;
        // admin para verificar o tipo de acesso.
        //
        // A senha armazenada deve ser um hash, conferido posteriormente
        // com password_verify().
        $sql = "SELECT id, email, senha, admin
                FROM usuarios
                WHERE email = :email";

        // Prepara a consulta para receber o e-mail como parâmetro.
        $stmt = $conexao->prepare($sql);

        // Associa o e-mail recebido ao parâmetro :email.
        $stmt->bindParam(":email", $email);

        // Executa a busca pelo endereço de e-mail.
        $stmt->execute();

        // Recupera o primeiro registro correspondente.
        // Se não existir usuário com esse e-mail, retorna false.
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Devolve os dados à página que chamou a função.
        // Diferentemente de outras funções deste arquivo,
        // esta não imprime os dados diretamente na página.
        return $usuario;

    } catch (PDOException $e) {

        // Registra os detalhes técnicos no log do servidor.
        error_log('consulta_user: ' . $e->getMessage());

        // Retorna false para informar que não foi possível obter
        // o resultado esperado.
        return false;
    }
}
