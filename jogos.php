<?php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions_jogos.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/jogos.css">

    <style>
        /* =========================
           CORREÇÃO DO CABEÇALHO
           ========================= */

        .topo {
            width: 100%;
            background: #000;
            border-bottom: 1px solid #222;
            position: relative;
            z-index: 100;
        }

        .navbar {
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            padding: 0 70px;
            box-sizing: border-box;
        }

        .logo {
            color: #ff8c00;
            font-size: 32px;
            font-weight: bold;
            text-decoration: none;
            white-space: nowrap;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-left: auto;
            margin-right: 45px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            white-space: nowrap;
        }

        .menu a:hover {
            color: #ff8c00;
        }

        .login {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .login a {
            text-decoration: none;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 25px;
            white-space: nowrap;
        }

        /* =========================
           BARRA ADMIN
           ========================= */

        .admin-bar {
            width: 100%;
            height: 55px;
            background: #111;
            border-bottom: 2px solid #ff8c00;

            display: flex;
            align-items: center;

            gap: 30px;
            padding: 0 70px;

            box-sizing: border-box;

            position: relative;
            z-index: 1000;
        }

        .admin-bar-titulo {
            color: #000;
            background: #ff8c00;

            font-size: 18px;
            font-weight: bold;

            padding: 8px 18px;
            border-radius: 8px;

            white-space: nowrap;
        }

        .admin-bar > a,
        .admin-grupo > span {
            color: white;
            text-decoration: none;

            font-size: 18px;
            white-space: nowrap;

            cursor: pointer;

            padding: 18px 0;
        }

        .admin-bar > a:hover,
        .admin-grupo > span:hover {
            color: #ff8c00;
        }

        .admin-grupo {
            position: relative;
            height: 55px;

            display: flex;
            align-items: center;
        }

        .admin-menu {
            position: absolute;

            top: 55px;
            left: 0;

            min-width: 230px;

            background: #111;

            border: 1px solid #333;
            border-radius: 0 0 8px 8px;

            padding: 8px 0;

            display: none;
            flex-direction: column;

            z-index: 2000;
        }

        .admin-grupo:hover .admin-menu,
        .admin-grupo:focus-within .admin-menu {
            display: flex;
        }

        .admin-menu a {
            display: block;

            color: white;
            text-decoration: none;

            font-size: 15px;

            padding: 12px 18px;

            white-space: nowrap;
        }

        .admin-menu a:hover {
            background: #ff8c00;
            color: #000;
        }
    </style>

    <title>Jogos por década</title>

</head>

<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="pagina-decadas">

        <h1>Escolha uma década</h1>

        <p class="sub">
            Cada década reúne os jogos que marcaram a época.
        </p>

        <?php include __DIR__ . '/includes/botoes_decadas.php'; ?>

    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>