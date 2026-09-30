<?php 
$host = "192.168.10.55";
$dbname = "game_erah";
$user = "game_erah";
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