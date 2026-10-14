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
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/adm_style.css">

    <title>Excluir usuário</title>

</head>

<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="admin-conteudo">

        <div class="admin-titulo">

            <h1>Excluir usuário</h1>

            <p>
                Informe o ID do usuário que deseja excluir.
            </p>

        </div>


        <section class="admin-card">

            <form action="" method="post" class="admin-form">

                <div class="campo">

                    <label for="id">
                        ID do usuário
                    </label>

                    <input
                        type="number"
                        name="id"
                        id="id"
                        min="1"
                        required
                    >

                </div>


                <div class="admin-acoes">

                    <button
                        type="submit"
                        class="btn-perigo"
                        onclick="return confirm('Tem certeza que deseja apagar este usuário?');"
                    >
                        Apagar usuário
                    </button>

                    <a
                        href="select.php"
                        class="btn-secundario"
                    >
                        Consultar usuários
                    </a>

                </div>

            </form>

        </section>

    </main>


    <?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            apagar($conexao, $id);
        }

    }

    ?>


    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>