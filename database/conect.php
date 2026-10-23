<?php 
$host = "localhost";
$dbname = "dumpgameerahdb";
$user = "game_erah";
$pass = "sua_senha_aqui";
try {
    $conexao = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $pass
        );
} catch (PDOException $e) {
    echo "Erro: ". $e->getMessage();
}
?>