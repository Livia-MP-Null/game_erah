<?php

require_once __DIR__ . '/../database/conect.php';


// CADASTRAR USUÁRIO
function cadastrar($conexao, $nome, $nasc, $senha, $email)
{
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, senha, nasc, email)
            VALUES (:nome, :senha, :nasc, :email)";

    try {
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":nasc", $nasc);
        $stmt->bindParam(":email", $email);

        $stmt->execute();

        echo "Aluno inserido com sucesso!";

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}


// APAGAR
function apagar($conexao, $id)
{
    $sql = "DELETE FROM usuarios WHERE id = :id";

    try {
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        echo "Registro $id deletado.";

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}


// RELATÓRIO
function relatorio($conexao)
{
    $sql = "SELECT * FROM usuarios ORDER BY id";

    try {
        $stmt = $conexao->prepare($sql);
        $stmt->execute();

        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $usuario) {

            echo "<hr>";

            echo "ID: {$usuario['id']}<br>";
            echo "Nome: {$usuario['nome']}<br>";
            echo "Nascimento: {$usuario['nasc']}<br>";
            echo "E-mail: {$usuario['email']}<br>";
            echo "Senha: {$usuario['senha']}<br>";
        }

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}


// ATUALIZAR
function atualizar($conexao, $id, $nome, $senha, $nasc, $email)
{
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios
            SET nome = :nome,
                senha = :senha,
                nasc = :nasc,
                email = :email
            WHERE id = :id";

    try {
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":nasc", $nasc);
        $stmt->bindParam(":email", $email);

        $stmt->execute();

        echo "Usuário atualizado com sucesso!";

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}


// CONSULTAR POR ID
function consultar_user($conexao, $id)
{
    $sql = "SELECT nome, senha, email, nasc
            FROM usuarios
            WHERE id = :id";

    try {
        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {

            echo "Nome: {$usuario['nome']}<br>";
            echo "Nascimento: {$usuario['nasc']}<br>";
            echo "E-mail: {$usuario['email']}<br>";
            echo "Senha: {$usuario['senha']}<br>";
            echo "<hr>";

        } else {

            echo "Usuário não encontrado.";
        }

    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}


// CADASTRO USADO PELO LOGIN
function cadastrar_user($conexao, $nome, $email, $senha, $nasc)
{
    $senha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha, nasc)
            VALUES (:nome, :email, :senha, :nasc)";

    try {

        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":senha", $senha);
        $stmt->bindParam(":nasc", $nasc);

        $stmt->execute();

        return true;

    } catch (PDOException $e) {

        echo "Erro: " . $e->getMessage();

        return false;
    }
}


// CONSULTAR USUÁRIO PELO E-MAIL
function consulta_user($conexao, $email)
{
    try {

        $sql = "SELECT id, email, senha, admin
                FROM usuarios
                WHERE email = :email";

        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(":email", $email);

        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario;

    } catch (PDOException $e) {

        echo "Erro: " . $e->getMessage();

        return false;
    }
}

