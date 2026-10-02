
    

    CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255),
    nasc DATE,
    email TYPEEMAIL,
    senha VARCHAR(255)
    );

    CREATE TABLE DECADA (
        id SERIAL PRIMARY KEY,
        nome VARCHAR(255),
        ano_inicio int);
    
    CREATE TABLE CATEGORIA (
        id SERIAL PRIMARY KEY,
        nome VARCHAR(255) );
    
    CREATE TABLE JOGO (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255),
    ano_lancamento INT,
    desenvolvedora VARCHAR(255),
    plataformas VARCHAR(255),
    descricao TEXT,
    imagem VARCHAR(255),
    decada_id INT,
    CONSTRAINT fk_jogo_decada FOREIGN KEY (decada_id) REFERENCES DECADA(id)
);

CREATE TABLE FAVORITO (
    usuario_id INT,
    jogo_id INT,
    CONSTRAINT fk_favorito_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_favorito_jogo FOREIGN KEY (jogo_id) REFERENCES JOGO(id),
    PRIMARY KEY (usuario_id, jogo_id)
);

CREATE TABLE COMENTARIO (
    id SERIAL PRIMARY KEY,
    usuario_id INT,
    jogo_id INT,
    texto TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comentario_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

    

    DECADA    ||--o{ JOGO       : "reúne"
    JOGO      }o--o{ CATEGORIA  : "destaca-se em"
    usuarios   ||--o{ FAVORITO   : "marca"
    JOGO      ||--o{ FAVORITO   : "é marcado"
    usuarios   ||--o{ COMENTARIO : "escreve"
    JOGO      ||--o{ COMENTARIO : "recebe"


