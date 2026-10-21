<?php
// ======================================================================
// select.php  |  RELATÓRIO DE USUÁRIOS  (somente administrador)
// ======================================================================

// Importa as configurações do site (define o BASE_URL)
require_once __DIR__ . '/../includes/config.php';

// Confere login + administrador e cria a conexão $conexao.
require_once __DIR__ . '/../includes/verifica_admin.php';

// Ferramentas de segurança.
require_once __DIR__ . '/../includes/seguranca.php';

// Funções de usuários.
require_once __DIR__ . '/../includes/functions_usuarios.php';

// Cabeçalhos HTTP de proteção.
cabecalhos_seguranca();

// Para aqui se o banco não estiver acessível.
exigir_conexao($conexao ?? null);

$usuarios = [];
$erroPagina = '';

try {
    $usuarios = usuarios_listar($conexao);
} catch (Throwable $e) {
    error_log('select.php: ' . $e->getMessage());
    $erroPagina = 'Não foi possível carregar o relatório agora.';
}

$idLogado = (int) ($_SESSION['id'] ?? 0);
$flash = flash_pegar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS do site -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/style/adm_style.css">

    <title>Relatório de Usuários</title>
</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="pagina-relatorio">

        <h1>Relatório:</h1>

        <?php if ($flash): ?>
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>"><?= escapar($flash['texto']) ?></p>
        <?php endif; ?>

        <?php if ($erroPagina): ?>
            <p class="msg-erro"><?= escapar($erroPagina) ?></p>
        <?php endif; ?>

        <?php if (!$erroPagina && !$usuarios): ?>
            <p>Nenhum usuário cadastrado.</p>
        <?php endif; ?>

        <?php if ($usuarios): ?>

            <p class="total-usuarios"><?= count($usuarios) ?> usuário(s) cadastrado(s).</p>

            <div class="tabela-rolagem">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Nascimento</th>
                            <th>Ativo</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= (int) $u['id'] ?></td>
                                <td><?= escapar($u['nome']) ?></td>
                                <td><?= escapar($u['email']) ?></td>
                                <td><?= escapar(formatar_data($u['nasc'])) ?></td>
                                <td><?= pg_para_bool($u['ativo']) ? 'Sim' : 'Não' ?></td>
                                <td><?= pg_para_bool($u['admin']) ? 'Administrador' : 'Comum' ?></td>
                                <td class="acoes">
                                    <a href="update.php?id=<?= (int) $u['id'] ?>" class="btn-editar">Editar</a>

                                    <?php if ((int) $u['id'] === $idLogado): ?>
                                        <span class="tag-voce">(você)</span>
                                    <?php else: ?>
                                        <form action="delete.php" method="post"
                                              onsubmit="return confirm('Apagar este usuário? Isso não pode ser desfeito.');">
                                            <?= csrf_campo() ?>
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <input type="hidden" name="voltar" value="select">
                                            <button type="submit" class="btn-perigo">Apagar</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </main>

    <!-- Rodapé do site -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>