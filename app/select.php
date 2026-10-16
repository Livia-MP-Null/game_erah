<?php
// ======================================================================
// select.php  |  RELATÓRIO DE USUÁRIOS  (somente administrador)
//
// Mostra todos os usuários cadastrados. Segurança:
//   - só admin vê esta página
//   - a SENHA (nem o hash) nunca é mostrada
//   - todo texto vindo do banco passa por escapar() antes de ir para a tela
//   - o botão "Apagar" usa POST + token CSRF
// ======================================================================

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

// Lista de usuários (começa vazia caso a consulta falhe).
$usuarios = [];

// Mensagem de erro da própria página (só usada se a consulta falhar).
$erroPagina = '';

try {
    // Busca todos os usuários (a função não traz a coluna senha).
    $usuarios = usuarios_listar($conexao);

} catch (Throwable $e) {
    // Erro interno: detalhes só no log.
    error_log('select.php: ' . $e->getMessage());

    // Mensagem genérica para a tela.
    $erroPagina = 'Não foi possível carregar o relatório agora.';
}

// ID de quem está logado. Usado para não mostrar o botão "Apagar" na própria linha.
$idLogado = (int) $_SESSION['id'];

// Mensagem "flash" (resultado de apagar, vindo do delete.php).
$flash = flash_pegar();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Os três arquivos de estilo do site (mesmos das suas páginas originais) -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/adm_style.css">
    <title>Relatório</title>
</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Relatório:</h1>

        <!-- Resultado da última ação (ex.: usuário apagado) -->
        <?php if ($flash): ?>
            <p class="<?= $flash['tipo'] === 'ok' ? 'msg-ok' : 'msg-erro' ?>"><?= escapar($flash['texto']) ?></p>
        <?php endif; ?>

        <!-- Erro ao carregar a lista -->
        <?php if ($erroPagina): ?>
            <p class="msg-erro"><?= escapar($erroPagina) ?></p>
        <?php endif; ?>

        <?php if (!$erroPagina && !$usuarios): ?>
            <p>Nenhum usuário cadastrado.</p>
        <?php endif; ?>

        <?php if ($usuarios): ?>

            <p><?= count($usuarios) ?> usuário(s) cadastrado(s).</p>

            <!-- tabela-rolagem: no celular, a tabela rola de lado em vez de estourar a tela -->
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

                        <!-- Uma linha da tabela para cada usuário -->
                        <?php foreach ($usuarios as $u): ?>
                            <tr>

                                <!-- (int) garante que o ID é número; escapar() protege os textos -->
                                <td><?= (int) $u['id'] ?></td>
                                <td><?= escapar($u['nome']) ?></td>
                                <td><?= escapar($u['email']) ?></td>
                                <td><?= escapar(formatar_data($u['nasc'])) ?></td>

                                <!-- pg_para_bool lê corretamente o true/false vindo do banco -->
                                <td><?= pg_para_bool($u['ativo']) ? 'Sim' : 'Não' ?></td>
                                <td><?= pg_para_bool($u['admin']) ? 'Administrador' : 'Comum' ?></td>

                                <td class="acoes">

                                    <!-- Link que abre a página de atualizar já com os dados deste usuário -->
                                    <a href="update.php?id=<?= (int) $u['id'] ?>">Editar</a>

                                    <?php if ((int) $u['id'] === $idLogado): ?>
                                        <!-- Na própria conta não há botão de apagar -->
                                        <span>(você)</span>
                                    <?php else: ?>
                                        <!-- Formulário de apagar: POST + token CSRF + confirmação -->
                                        <form action="delete.php" method="post"
                                              onsubmit="return confirm('Apagar este usuário? Isso não pode ser desfeito.');">
                                            <?= csrf_campo() ?>
                                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                            <!-- "voltar" avisa o delete.php para voltar a este relatório -->
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
