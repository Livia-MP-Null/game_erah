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

// Limites usados nas validações (mude aqui se precisar).
const NOME_MIN  = 2;     // mínimo de letras do nome
const NOME_MAX  = 100;   // máximo de letras do nome
const EMAIL_MAX = 254;   // tamanho máximo de um e-mail (padrão da internet)
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

        // E-mail: tira espaços e põe tudo em minúsculas, para que
        // "Ana@Gmail.com" e "ana@gmail.com" sejam tratados como o mesmo e-mail.
        'email' => mb_strtolower(trim($bruto['email'] ?? '')),

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
// $exigirSenha: true no cadastro (senha obrigatória); false na atualização
//               (em branco = manter a senha atual).
// $exigirAtivo: true no cadastro (precisa escolher Sim ou Não).
// Devolve uma lista vazia se estiver tudo certo.
function usuario_erros_validacao($d, $exigirSenha, $exigirAtivo)
{
    // Começamos sem nenhum erro.
    $erros = [];

    // ---- NOME ----
    if ($d['nome'] === '') {
        // Campo vazio.
        $erros[] = 'O nome é obrigatório.';
    } elseif (mb_strlen($d['nome']) < NOME_MIN || mb_strlen($d['nome']) > NOME_MAX) {
        // mb_strlen conta letras acentuadas corretamente (strlen contaria bytes).
        $erros[] = 'O nome deve ter entre ' . NOME_MIN . ' e ' . NOME_MAX . ' caracteres.';
    }

    // ---- E-MAIL ----
    if ($d['email'] === '') {
        $erros[] = 'O e-mail é obrigatório.';
    } elseif (mb_strlen($d['email']) > EMAIL_MAX || !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        // filter_var confere se o formato é de e-mail de verdade (algo@dominio.com).
        $erros[] = 'Informe um e-mail válido.';
    }

    // ---- SENHA ----
    if ($d['senha'] === '') {
        // Só é erro se a senha for obrigatória (cadastro).
        if ($exigirSenha) {
            $erros[] = 'A senha é obrigatória.';
        }
    } elseif (strlen($d['senha']) < SENHA_MIN) {
        // strlen conta bytes, que é o que importa para o password_hash.
        $erros[] = 'A senha deve ter no mínimo ' . SENHA_MIN . ' caracteres.';
    } elseif (strlen($d['senha']) > SENHA_MAX) {
        $erros[] = 'A senha deve ter no máximo ' . SENHA_MAX . ' caracteres.';
    }

    // ---- DATA DE NASCIMENTO ----
    if ($d['nasc'] === '') {
        $erros[] = 'A data de nascimento é obrigatória.';
    } else {
        // Tenta ler o texto como data no formato ano-mês-dia.
        $data = DateTime::createFromFormat('Y-m-d', $d['nasc']);

        // Datas impossíveis (como 2020-02-31) são "corrigidas" pelo PHP para outro dia.
        // Se o texto de volta não for igual ao original, a data era inválida.
        if (!$data || $data->format('Y-m-d') !== $d['nasc']) {
            $erros[] = 'A data de nascimento é inválida.';
        } elseif ($data > new DateTime('today')) {
            // Ninguém nasce no futuro.
            $erros[] = 'A data de nascimento não pode ser no futuro.';
        } elseif ($data < new DateTime('1900-01-01')) {
            // Evita datas absurdas.
            $erros[] = 'A data de nascimento é inválida.';
        }
    }

    // ---- ATIVO ----
    // Só aceita exatamente 'true' ou 'false' (o true do in_array liga a comparação estrita).
    if ($exigirAtivo && !in_array($d['ativo'], ['true', 'false'], true)) {
        $erros[] = 'Escolha se o usuário está ativo (Sim ou Não).';
    }

    // Devolve a lista (vazia = tudo certo).
    return $erros;
}


// ----------------------------------------------------------------------
// usuario_buscar(): procura um usuário pelo ID.
// ----------------------------------------------------------------------
// Devolve os dados (SEM a senha) ou false se o ID não existir.
function usuario_buscar($c, $id)
{
    // O "?" será preenchido depois, separado do comando (isso impede SQL injection).
    // Repare que a coluna "senha" NÃO está na lista: ela nunca deve sair do banco
    // sem necessidade.
    $s = $c->prepare('SELECT id, nome, email, nasc, ativo, admin FROM usuarios WHERE id = ?');

    // Executa a consulta entregando o valor do "?".
    $s->execute([(int) $id]);

    // Devolve a linha encontrada como lista (ou false se não achou nada).
    return $s->fetch(PDO::FETCH_ASSOC);
}


// ----------------------------------------------------------------------
// usuario_buscar_por_email(): procura um usuário pelo e-mail (usada no LOGIN).
// ----------------------------------------------------------------------
// Diferente de usuario_buscar, esta função TRAZ a senha (o hash), porque o
// login precisa dela para conferir a senha digitada. Por isso ela só deve
// ser usada dentro do login: o hash nunca deve ser mostrado na tela.
// Devolve os dados ou false se o e-mail não existir.
function usuario_buscar_por_email($c, $email)
{
    // lower() nos dois lados: maiúsculas e minúsculas não importam no e-mail.
    // LIMIT 1 garante que no máximo uma linha volta.
    $s = $c->prepare(
        'SELECT id, nome, email, senha, ativo, admin
         FROM usuarios
         WHERE lower(email) = lower(?)
         LIMIT 1'
    );

    // Entrega o valor do "?".
    $s->execute([$email]);

    // Devolve a linha como lista (ou false se não achou).
    return $s->fetch(PDO::FETCH_ASSOC);
}


// ----------------------------------------------------------------------
// usuario_email_em_uso(): o e-mail já pertence a alguém?
// ----------------------------------------------------------------------
// $ignorarId: ID de um usuário que deve ser ignorado na conferência (usado
// na atualização, para o usuário poder manter o próprio e-mail).
// O valor 0 significa "não ignorar ninguém" (nenhum usuário tem ID 0).
function usuario_email_em_uso($c, $email, $ignorarId = 0)
{
    // lower() nos dois lados compara sem diferenciar maiúsculas de minúsculas.
    $s = $c->prepare('SELECT 1 FROM usuarios WHERE lower(email) = lower(?) AND id <> ? LIMIT 1');

    // Entrega os dois valores, na ordem dos "?".
    $s->execute([$email, (int) $ignorarId]);

    // fetchColumn devolve 1 se achou alguém, ou false se não achou; (bool) vira true/false.
    return (bool) $s->fetchColumn();
}


// ----------------------------------------------------------------------
// usuario_criar(): grava um usuário novo. Devolve o ID criado.
// ----------------------------------------------------------------------
function usuario_criar($c, $d)
{
    // Transforma a senha em um "hash": um código embaralhado que não volta
    // ao texto original. Nem o administrador do banco consegue ler a senha.
    $hash = password_hash($d['senha'], PASSWORD_DEFAULT);

    try {
        // Prepara o comando. A coluna "admin" não aparece aqui de propósito:
        // todo usuário criado por este formulário entra como comum (padrão FALSE).
        // "RETURNING id" faz o banco devolver o ID do usuário criado.
        $s = $c->prepare(
            'INSERT INTO usuarios (nome, nasc, email, senha, ativo)
             VALUES (?, ?, ?, ?, ?)
             RETURNING id'
        );

        // Executa. O "ativo" vai como texto 'true' ou 'false', que o PostgreSQL entende.
        // (Se enviássemos o false do PHP, ele viraria texto vazio e daria erro.)
        $s->execute([$d['nome'], $d['nasc'], $d['email'], $hash, $d['ativo']]);

        // Devolve o ID do novo usuário.
        return (int) $s->fetchColumn();

    } catch (PDOException $e) {

        // Código 23505 = violação de "valor único" (e-mail repetido). Pode acontecer
        // se duas pessoas cadastrarem o mesmo e-mail ao mesmo tempo.
        if ($e->getCode() === '23505') {
            throw new ErroValidacao('Já existe um usuário com este e-mail.');
        }

        // Qualquer outro erro é interno: repassa para quem chamou tratar (e registrar no log).
        throw $e;
    }
}


// ----------------------------------------------------------------------
// usuario_atualizar(): altera nome, e-mail, nascimento e (se informada) a senha.
// ----------------------------------------------------------------------
function usuario_atualizar($c, $id, $d)
{
    try {
        if ($d['senha'] !== '') {
            // Senha preenchida: troca a senha também (sempre guardando o hash).
            $s = $c->prepare('UPDATE usuarios SET nome = ?, nasc = ?, email = ?, senha = ? WHERE id = ?');
            $s->execute([$d['nome'], $d['nasc'], $d['email'], password_hash($d['senha'], PASSWORD_DEFAULT), (int) $id]);
        } else {
            // Senha em branco: mantém a senha atual (ela nem aparece no comando).
            $s = $c->prepare('UPDATE usuarios SET nome = ?, nasc = ?, email = ? WHERE id = ?');
            $s->execute([$d['nome'], $d['nasc'], $d['email'], (int) $id]);
        }
    } catch (PDOException $e) {

        // E-mail repetido (índice único do banco).
        if ($e->getCode() === '23505') {
            throw new ErroValidacao('Já existe um usuário com este e-mail.');
        }

        // Erro interno: repassa.
        throw $e;
    }
}


// ----------------------------------------------------------------------
// usuario_apagar(): apaga um usuário, com várias travas de segurança.
// ----------------------------------------------------------------------
// $idLogado: ID de quem está apagando (o administrador logado).
// Não precisa de nenhuma configuração especial no banco: os favoritos,
// comentários e curtidas do usuário são apagados aqui mesmo, pelo PHP.
function usuario_apagar($c, $id, $idLogado)
{
    // Trava 1: o ID precisa existir.
    $usuario = usuario_buscar($c, $id);
    if (!$usuario) {
        throw new ErroValidacao('Não existe usuário com o ID ' . (int) $id . '.');
    }

    // Trava 2: o admin não pode apagar a própria conta enquanto está logado nela
    // (senão ele ficaria sem acesso ao painel).
    if ((int) $id === (int) $idLogado) {
        throw new ErroValidacao('Você não pode apagar a sua própria conta enquanto está logado nela.');
    }

    // Trava 3: nunca apagar o ÚLTIMO administrador (o site ficaria sem admin).
    if (pg_para_bool($usuario['admin'])) {
        // Conta quantos administradores existem.
        $total = (int) $c->query('SELECT COUNT(*) FROM usuarios WHERE admin = TRUE')->fetchColumn();

        if ($total <= 1) {
            throw new ErroValidacao('Não é possível apagar o último administrador.');
        }
    }

    // Tabelas que guardam coisas feitas por este usuário. O banco não deixa
    // apagar o usuário enquanto essas linhas existirem, então apagamos
    // primeiro essas linhas e depois o usuário.
    // (Esta lista é fixa, escrita por nós: por isso é seguro usá-la dentro do comando.)
    $tabelasDoUsuario = ['favorito', 'comentario', 'curtida'];

    // Transação: ou apaga TUDO, ou não apaga NADA (se algo falhar no meio, desfaz).
    $c->beginTransaction();

    try {
        foreach ($tabelasDoUsuario as $tabela) {

            // to_regclass devolve vazio (NULL) quando a tabela não existe.
            // Assim, se alguma delas ainda não foi criada no banco, apenas pulamos.
            $existe = $c->prepare('SELECT to_regclass(?) IS NOT NULL');
            $existe->execute([$tabela]);

            if (pg_para_bool($existe->fetchColumn())) {
                // Apaga as linhas desta tabela que pertencem ao usuário.
                $c->prepare("DELETE FROM $tabela WHERE usuario_id = ?")->execute([(int) $id]);
            }
        }

        // Agora sim, apaga o usuário.
        $c->prepare('DELETE FROM usuarios WHERE id = ?')->execute([(int) $id]);

        // Confirma tudo de uma vez.
        $c->commit();

    } catch (PDOException $e) {

        // Algo falhou: desfaz tudo o que foi feito dentro da transação.
        if ($c->inTransaction()) {
            $c->rollBack();
        }

        // Código 23503 = outra tabela (que não está na lista acima) ainda aponta
        // para este usuário.
        if ($e->getCode() === '23503') {
            throw new ErroValidacao('Este usuário ainda tem registros ligados a ele e não pôde ser apagado.');
        }

        // Qualquer outro erro é interno: repassa para ser registrado no log.
        throw $e;
    }
}


// ----------------------------------------------------------------------
// usuarios_listar(): devolve todos os usuários, sem a senha.
// ----------------------------------------------------------------------
function usuarios_listar($c)
{
    // Não há "?" porque não há valor vindo do usuário nesta consulta.
    // ORDER BY id deixa a lista na ordem de cadastro.
    return $c->query('SELECT id, nome, email, nasc, ativo, admin FROM usuarios ORDER BY id')
             ->fetchAll(PDO::FETCH_ASSOC);
}
