<?php

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nasc = $_POST['nasc'];
    $ativo = $_POST['ativo'] === 'true' ? 1 : 0;

    $cadastro = cadastrar_user(
        $conexao,
        $nome,
        $email,
        $senha,
        $nasc,
        $ativo
    );

    if ($cadastro) {
        header("Location: ../login/login.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <h1>Registre-se no sistema:</h1>

    <hr>

    <main>

        <form action="" method="POST">

            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>

            <br>

            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>

            <br>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>

            <br>

            <label for="nasc">Nascimento:</label>
            <input type="date" name="nasc" id="nasc" required>

            <br>

            <label>Ativo:</label>

            <input type="radio" name="ativo" id="ativo-sim" value="true" checked>
            <label for="ativo-sim">Sim</label>

            <input type="radio" name="ativo" id="ativo-nao" value="false">
            <label for="ativo-nao">Não</label>

            <br><br>

            <input type="reset" value="Limpar">
            <input type="submit" value="Enviar">

        </form>

        <p>
            Já tem cadastro?
            <a href="./login.php">Entre aqui</a>
        </p>

    </main>

    <hr>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>