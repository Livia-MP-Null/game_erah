<?php

session_start();

require_once __DIR__ . '/../database/conect.php';
require_once __DIR__ . '/../includes/functions.php';

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = consulta_user($conexao, $email);

    if ($usuario) {

        if (password_verify($senha, $usuario['senha'])) {

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];

            header("Location: ../index.php");
            exit();
        } else {

            $erro = "Senha incorreta.";
        }
    } else {

        $erro = "E-mail não cadastrado.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="../style/style.css">

</head>

<body>
<?php include_once __DIR__ . "/../includes/header.php"?>
    <h1>Login</h1>

    <?php if ($erro != "") { ?>

        <p>
            <?php echo $erro; ?>
        </p>

    <?php } ?>

    <form method="POST">

        <label for="email">E-mail:</label>

        <input
            type="email"
            name="email"
            id="email"
            required>

        <br>

        <label for="senha">Senha:</label>

        <input
            type="password"
            name="senha"
            id="senha"
            required>

        <br><br>

        <button type="submit">
            Entrar
        </button>

    </form>

    <p>
        Ainda não possui uma conta?
        <a href="cadastrar.php">Cadastre-se aqui</a>
    </p>

</body>

</html>