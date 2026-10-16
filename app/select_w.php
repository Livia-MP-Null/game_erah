<?php
// ======================================================================
// select_w.php  |  CONSULTAR USUÁRIO POR ID  (somente administrador)
//
// Segurança:
//   - só admin; o ID é validado (inteiro maior que zero)
//   - ID que não existe mostra uma mensagem clara (não quebra a página)
//   - a senha nunca é mostrada
//   - usa o método GET (?id=5): consultar não altera nada no banco,
//     então não precisa de token CSRF e o endereço pode ser compartilhado
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

// Erro a mostrar (começa sem erro).
$erro = '';

// Usuário encontrado (começa sem nenhum).
$usuario = null;

// Texto que volta para dentro do campo ID.
$idDigitado = '';

// Só consulta quando a pessoa enviou um ID (a URL tem ?id=...).
if (isset($_GET['id'])) {

    // Guarda o que foi digitado para mostrar de volta no campo (será escapado ao exibir).
    $idDigitado = ler_get('id');

    // Confere se é um ID válido: devolve o número inteiro ou null.
    $id = normalizar_id($idDigitado);

    if ($id === null) {
        // Vazio, letras, zero, negativo, decimal ou grande demais.
        $erro = 'Informe um ID válido (número inteiro maior que zero).';
    } else {
        try {
            // Procura o usuário no banco.
            $usuario = usuario_buscar($conexao, $id);

            // Se não achou, o ID não existe.
            if (!$usuario) {
                $erro = 'Não existe usuário com o ID ' . $id . '.';
            }

        } catch (Throwable $e) {
            // Erro interno: detalhes só no log.
            error_log('select_w.php: ' . $e->getMessage());

            // Mensagem genérica na tela.
            $erro = 'Não foi possível consultar agora. Tente novamente.';
        }
    }
}
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
    <title>Consulta usuário</title>
</head>

<body>

    <!-- Cabeçalho do site -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main>

        <h1>Consulta usuário:</h1>

        <!-- Erro da consulta (ID inválido ou inexistente) -->
        <?php if ($erro): ?>
            <p class="msg-erro"><?= escapar($erro) ?></p>
        <?php endif; ?>

        <!-- method="get": o ID aparece na URL (?id=5). Não há token porque nada é alterado. -->
        <form action="" method="get">

            <label for="id">ID do usuário:</label>
            <input type="number" name="id" id="id" required min="1" step="1"
                   placeholder="Insira o ID para consultá-lo"
                   value="<?= escapar($idDigitado) ?>">

            <input type="submit" value="Consultar">

            <a href="select.php">Ver o relatório completo</a>

        </form>

        <!-- Resultado: só aparece se o usuário foi encontrado -->
        <?php if ($usuario): ?>

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
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <!-- Todo texto vindo do banco passa por escapar() -->
                            <td><?= (int) $usuario['id'] ?></td>
                            <td><?= escapar($usuario['nome']) ?></td>
                            <td><?= escapar($usuario['email']) ?></td>
                            <td><?= escapar(formatar_data($usuario['nasc'])) ?></td>
                            <td><?= pg_para_bool($usuario['ativo']) ? 'Sim' : 'Não' ?></td>
                            <td><?= pg_para_bool($usuario['admin']) ? 'Administrador' : 'Comum' ?></td>
                        </tr>
                    </tbody>

                </table>
            </div>

            <!-- Atalho para editar este usuário -->
            <p><a class="btn-admin" href="update.php?id=<?= (int) $usuario['id'] ?>">Editar este usuário</a></p>

        <?php endif; ?>

    </main>

    <!-- Rodapé do site -->
    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
