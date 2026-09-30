DROP table usuarios;
CREATE TABLE usuarios (
    id SERIAL UNIQUE PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    nasc DATE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    ativo BOOLEAN
)
SELECT * FROM usuarios;