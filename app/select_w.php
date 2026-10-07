<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_admin.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Consulta usuário</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    
    <main>
<h1>Consulta aluno:</h1>

        <form action="" method="post">

            <label for="id" style="color: white;">ID:</label>

            <input
                type="number"
                name="id"
                id="id"
                placeholder="Insira o ID para consultá-lo"
                required>

            <br>

            <input type="submit" value="Consultar">

            <br>

            <a href="select.php">Consulta DB</a>

        </form>

    </main>

    <?php

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        consultar_user($conexao, $_POST['id']);
    }

    ?>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>