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
    <title>Cadastrar</title>
</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Cadastro de usuário:</h1>

        <form action="" method="post">

            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome">

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha">

            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email">

            <label for="nasc">Nascimento:</label>
            <input type="date" name="nasc" id="nasc">

            <label>Ativo:</label>

            <input type="radio" name="ativo" id="ativo_sim" value="true">
            <label for="ativo_sim">Sim</label>

            <input type="radio" name="ativo" id="ativo_nao" value="false">
            <label for="ativo_nao">Não</label>


            <input type="submit" value="Cadastrar">
            <input type="reset" value="Limpar">

        </form>

    </main>

    <?php

    // Esse if faz o PHP começar somente quando o formulário for enviado
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $name = $_POST['nome'];
        $nasc = $_POST['nasc'];
        $senha = $_POST['senha'];
        $email = $_POST['email'];

        // Converte "true" e "false" para 1 e 0
        $ativo = $_POST['ativo'] === 'true' ? 1 : 0;

        cadastrar($conexao, $name, $nasc, $senha, $email, $ativo);
    }

    include __DIR__ . '/../includes/footer.php';

    ?>

</body>

</html>