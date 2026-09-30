<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/verifica_user.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../style/style.css">

    <title>Relatório</title>

</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>



    <main>
        <h1>Relatório:</h1>
        <?php

        if (isset($conexao) && $conexao !== null) {

            relatorio($conexao);
        } else {

            echo "<p>Sem conexão com o banco de dados para gerar o relatório.</p>";
        }

        ?>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>