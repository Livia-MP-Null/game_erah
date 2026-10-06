<?php
// USO ÚNICO: abra no navegador uma vez e depois APAGUE este arquivo.
// Cria (ou promove) o administrador de desenvolvimento.

require_once __DIR__ . '/../database/conect.php';

$email = 'game.era@gmail.com';
$hash  = password_hash('game.era', PASSWORD_DEFAULT);

$existe = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
$existe->execute([$email]);

if ($existe->fetch()) {
    $s = $conexao->prepare("UPDATE usuarios SET admin = TRUE, ativo = TRUE, senha = ? WHERE email = ?");
    $s->execute([$hash, $email]);
    echo "Usuário já existia: agora é administrador.";
} else {
    $s = $conexao->prepare("INSERT INTO usuarios (nome, nasc, email, senha, admin, ativo)
                            VALUES (?, ?, ?, ?, TRUE, TRUE)");
    $s->execute(['Administrador', '2000-01-01', $email, $hash]);
    echo "Admin criado.";
}
