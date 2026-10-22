<?php 
$host = "192.168.1.193";
$dbname = "game_erahdb";
$user = "game_era";
$pass = "LIli3004";
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