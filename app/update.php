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
    <title>Atualizar</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>


    <main>

        <h1>Atualiza dados:</h1>

        <form action="" method="post" class="meu-formulario">

            <div class="campo">
                <label for="id">ID:</label>
                <input
                    type="number"
                    name="id"
                    id="id"
                    placeholder="Insira ID para atualizar"
                    required
                >
            </div>

            <div class="campo">
                <label for="nome">Nome:</label>
                <input
                    type="text"
                    name="nome"
                    id="nome"
                    required
                >
            </div>

            <div class="campo">
                <label for="senha">Senha:</label>
                <input
                    type="password"
                    name="senha"
                    id="senha"
                    required
                >
            </div>

            <div class="campo">
                <label for="email">E-mail:</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                >
            </div>

            <div class="campo">
                <label for="nasc">Nascimento:</label>
                <input
                    type="date"
                    name="nasc"
                    id="nasc"
                    required
                >
            </div>

            <div class="botoes">
                <input type="submit" value="Atualizar">
                <input type="reset" value="Limpar">
            </div>

        </form>

        <?php

        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            atualizar(
                $conexao,
                $_POST['id'],
                $_POST['nome'],
                $_POST['senha'],
                $_POST['nasc'],
                $_POST['email']
            );

        }

        ?>

    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>