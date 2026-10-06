<?php
require_once __DIR__ . '/../database/conect.php';

$hash = password_hash('game.era', PASSWORD_DEFAULT);

$stmt = $conexao->prepare("
    INSERT INTO usuarios (nome, nasc, email, senha, admin)
    VALUES (?, ?, ?, ?, TRUE)
    ON CONFLICT (email) DO UPDATE SET admin = TRUE, senha = EXCLUDED.senha
");
$stmt->execute(['Administrador', '2000-01-01', 'game.era@gmail.com', $hash]);

echo "Admin criado.";