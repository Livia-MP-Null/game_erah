<?php
// ======================================================================
// functions_usuarios.php
// Funções que VALIDAM e GRAVAM usuários no banco (tabela "usuarios").
//
// IMPORTANTE: este arquivo NÃO exige nenhuma mudança no banco de dados.
// Tudo é feito em PHP, usando só as colunas que já existem em "usuarios"
// (id, nome, nasc, email, senha, ativo, admin).
//
// Regras de segurança aplicadas aqui:
//   - todas as consultas usam "?" (prepared statements): impede SQL injection
//   - a senha é guardada com password_hash (nunca em texto puro)
//   - nenhum dado é aceito sem validação
//   - detalhes técnicos de erro não vão para a tela
// ======================================================================

// Carrega as ferramentas de segurança (ErroValidacao, etc.).
require_once __DIR__ . '/seguranca.php';

// Limites usados nas validações.
const NOME_MIN  = 2;     // mínimo de letras do nome
const NOME_MAX  = 100;   // máximo de letras do nome
const EMAIL_MAX = 254;   // tamanho máximo de um e-mail
const SENHA_MIN = 8;     // mínimo de caracteres da senha
const SENHA_MAX = 72;    // o password_hash só considera os 72 primeiros bytes


// ----------------------------------------------------------------------
// usuario_normalizar(): "limpa" os dados que vieram do formulário.
// ----------------------------------------------------------------------
function usuario_normalizar($bruto)
{
    return [
        // Nome: tira espaços do começo e do fim.
        'nome'  => trim($bruto['nome'] ?? ''),

        // E-mail: tira espaços e põe tudo em minúsculas (usando strtolower nativo)
        'email' => strtolower(trim($bruto['email'] ?? '')),

        // Senha: NÃO usamos trim, porque espaços podem fazer parte dela.
        'senha' => (string) ($bruto['senha'] ?? ''),

        // Data de nascimento: tira espaços.
        'nasc'  => trim($bruto['nasc'] ?? ''),

        // Ativo: vem como o texto 'true' ou 'false' do botão de opção.
        'ativo' => trim($bruto['ativo'] ?? ''),
    ];
}


// ----------------------------------------------------------------------
// usuario_erros_validacao(): confere cada campo e devolve a LISTA de erros.
// ----------------------------------------------------------------------
function usuario_erros_validacao($d, $exigirSenha, $exigirAtivo)
{
    $erros = [];

    // ---- NOME ----
    if ($d['nome'] === '') {
        $erros[] = 'O nome é obrigatório.';
    } elseif (strlen($d['nome']) < NOME_MIN || strlen($d['nome']) > NOME_MAX) {
        $erros[] = 'O nome deve ter entre ' . NOME_MIN . ' e ' . NOME_MAX . ' caracteres.';
    }

    // ---- E-MAIL ----
    if ($d['email'] === '') {
        $erros[] = 'O e-mail é obrigatório.';
    } elseif (strlen($d['email']) > EMAIL_MAX || !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }

    // ---- SENHA ----
    if ($d['senha'] === '') {
        if ($exigirSenha) {
            $erros[] = 'A senha é obrigatória.';
        }
    } elseif (strlen($d['senha']) < SENHA_MIN) {
        $erros[] = 'A senha deve ter no mínimo ' . SENHA_MIN . ' caracteres.';
    } elseif (strlen($d['senha']) > SENHA_MAX) {
        $erros[] = 'A senha deve ter no máximo ' . SENHA_MAX . ' caracteres.';
    }

    // ---- DATA DE NASCIMENTO ----
    if ($d['nasc'] === '') {
        $erros[] = 'A data de nascimento é obrigatória.';
    } else {
        $data = DateTime::createFromFormat('Y-m-d', $d['nasc']);

        if (!$data || $data->format('Y-m-d') !== $d['nasc']) {
            $erros[] = 'A data de nascimento é inválida.';
        } elseif ($data > new DateTime('today')) {
            $erros[] = 'A data de nascimento não pode ser no futuro.';
        } elseif ($data < new DateTime('1900-01-01')) {
            $erros[] = 'A data de nascimento é inválida.';
        }
    }

    // ---- ATIVO ----
    if ($exigirAtivo && !in_array($d['ativo'], ['true', 'false'], true)) {
        $erros[] = 'Escolha se o usuário está ativo (Sim ou Não).';
    }

    return $erros;
}


// ----------------------------------------------------------------------
// usuario_buscar(): procura um usuário pelo ID.
// ----------------------------------------------------------------------
function usuario_buscar($c, $id)
{
    $s = $c->prepare('SELECT id, nome, email, nasc, ativo, admin FROM usuarios WHERE id = ?');
    $s->execute([(int) $id]);
    return $s->fetch(PDO::FETCH_ASSOC);
}


// ----------------------------------------------------------------------
// usuario_buscar_por_email(): procura um usuário pelo e-mail (LOGIN).
// ----------------------------------------------------------------------
function usuario_buscar_por_email($c, $email)
{
    $s = $c->prepare(
        'SELECT id, nome, email, senha, ativo, admin
         FROM usuarios
         WHERE lower(email) = lower(?)
         LIMIT 1'
    );
    $s->execute([$email]);
    return $s->fetch(PDO::FETCH_ASSOC);
}


// ----------------------------------------------------------------------
// usuario_email_em_uso(): o e-mail já pertence a alguém?
// ----------------------------------------------------------------------
function usuario_email_em_uso($c, $email, $ignorarId = 0)
{
    $s = $c->prepare('SELECT 1 FROM usuarios WHERE lower(email) = lower(?) AND id <> ? LIMIT 1');
    $s->execute([$email, (int) $ignorarId]);
    return (bool) $s->fetchColumn();
}


// ----------------------------------------------------------------------
// usuario_criar(): grava um usuário novo.
// ----------------------------------------------------------------------
function usuario_criar($c, $d)
{
    $hash = password_hash($d['senha'], PASSWORD_DEFAULT);

    try {
        $s = $c->prepare(
            'INSERT INTO usuarios (nome, nasc, email, senha, ativo)
             VALUES (?, ?, ?, ?, ?)
             RETURNING id'
        );
        $s->execute([$d['nome'], $d['nasc'], $d['email'], $hash, $d['ativo']]);
        return (int) $s->fetchColumn();

    } catch (PDOException $e) {
        if ($e->getCode() === '23505') {
            throw new ErroValidacao('Já existe um usuário com este e-mail.');
        }
        throw $e;
    }
}


// ----------------------------------------------------------------------
// usuario_atualizar(): altera nome, e-mail, nascimento e a senha (se informada).
// ----------------------------------------------------------------------
function usuario_atualizar($c, $id, $d)
{
    try {
        if ($d['senha'] !== '') {
            $s = $c->prepare('UPDATE usuarios SET nome = ?, nasc = ?, email = ?, senha = ? WHERE id = ?');
            $s->execute([$d['nome'], $d['nasc'], $d['email'], password_hash($d['senha'], PASSWORD_DEFAULT), (int) $id]);
        } else {
            $s = $c->prepare('UPDATE usuarios SET nome = ?, nasc = ?, email = ? WHERE id = ?');
            $s->execute([$d['nome'], $d['nasc'], $d['email'], (int) $id]);
        }
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() === '23505') {
            throw new ErroValidacao('Já existe um usuário com este e-mail.');
        }
        throw $e;
    }
}


// ----------------------------------------------------------------------
// usuario_apagar(): apaga um usuário, com várias travas de segurança.
// ----------------------------------------------------------------------
function usuario_apagar($c, $id, $idLogado)
{
    $usuario = usuario_buscar($c, $id);
    if (!$usuario) {
        throw new ErroValidacao('Não existe usuário com o ID ' . (int) $id . '.');
    }

    if ((int) $id === (int) $idLogado) {
        throw new ErroValidacao('Você não pode apagar a sua própria conta enquanto está logado nela.');
    }

    if (pg_para_bool($usuario['admin'])) {
        $total = (int) $c->query('SELECT COUNT(*) FROM usuarios WHERE admin = TRUE')->fetchColumn();
        if ($total <= 1) {
            throw new ErroValidacao('Não é possível apagar o último administrador.');
        }
    }

    $tabelasDoUsuario = ['favorito', 'comentario', 'curtida'];
    $c->beginTransaction();

    try {
        foreach ($tabelasDoUsuario as $tabela) {
            $existe = $c->prepare('SELECT to_regclass(?) IS NOT NULL');
            $existe->execute([$tabela]);

            if (pg_para_bool($existe->fetchColumn())) {
                $c->prepare("DELETE FROM $tabela WHERE usuario_id = ?")->execute([(int) $id]);
            }
        }

        $c->prepare('DELETE FROM usuarios WHERE id = ?')->execute([(int) $id]);
        $c->commit();

    } catch (PDOException $e) {
        if ($c->inTransaction()) {
            $c->rollBack();
        }

        if ($e->getCode() === '23503') {
            throw new ErroValidacao('Este usuário ainda tem registros ligados a ele e não pôde ser apagado.');
        }
        throw $e;
    }
}


// ----------------------------------------------------------------------
// usuarios_listar(): devolve todos os usuários.
// ----------------------------------------------------------------------
function usuarios_listar($c)
{
    return $c->query('SELECT id, nome, email, nasc, ativo, admin FROM usuarios ORDER BY id')
             ->fetchAll(PDO::FETCH_ASSOC);
}