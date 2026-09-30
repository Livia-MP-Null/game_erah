<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_user.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Deleta User</title>
</head>

<body>
    
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <main>
        <h1>Página para apagar o usuário:</h1>
        <form action="" method="post">
            <label for="id" style="color: white;">ID: </label>
            <input type="number" name="id" id="id">
            <input type="submit" value="Apagar">
        </form>
        <a href="select.php">Consulta DB</a>
    </main>
    <?php
    require_once __DIR__ . '/../database/conect.php';
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        apagar($conexao, $_POST['id']);
    } ?>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>

</html>