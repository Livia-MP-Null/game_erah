DROP TABLE usuarios;
CREATE DOMAIN typeEmail AS TEXT
CHECK (VALUE ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'); 
-- O trecho "[A-Za-z0-9._%+-]" define que pode ter qualquer coisa nessa area
-- Já o trecho "+@[A-Za-z0-9.-]" fala que tem que ter um @, e pode ser qualquer coisa depois (@gmail)
-- Por fim, o trecho "\.[A-Za-z]{2,}$')" define que tem que ter um ponto, se colocasse-mos só um ., significaria que qualquer coisa poderia ser colocada nesse local, e depois, pode ir qualquer coisa, porém, tem que ter mais de 1 caractere
CREATE TABLE usuarios (
    id SERIAL UNIQUE PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    nasc DATE NOT NULL,
    email typeEmail UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);

select * from usuarios;