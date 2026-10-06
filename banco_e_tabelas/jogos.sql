-- Rode este arquivo quando tiver acesso ao PostgreSQL

-- 1) Coluna de admin na tabela de usuários
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS admin BOOLEAN NOT NULL DEFAULT FALSE;

-- 2) Tabela de jogos
-- A década NÃO é guardada: ela é calculada a partir do ano (ex.: 1994 -> 1990)
CREATE TABLE IF NOT EXISTS jogos (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    ano_lancamento INT NOT NULL CHECK (ano_lancamento BETWEEN 1950 AND 2100),
    desenvolvedora VARCHAR(255) NOT NULL DEFAULT 'Desconhecida',
    descricao TEXT NOT NULL,
    imagem VARCHAR(255),   -- nome do arquivo em uploads/jogos/
    video VARCHAR(255),    -- nome do arquivo em uploads/jogos/
    criado_em TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Se a tabela jogos já existia sem a coluna da desenvolvedora, esta linha adiciona
-- (se a coluna já existe, não faz nada)
ALTER TABLE jogos ADD COLUMN IF NOT EXISTS desenvolvedora VARCHAR(255) NOT NULL DEFAULT 'Desconhecida';
