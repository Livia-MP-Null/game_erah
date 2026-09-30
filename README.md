<h1 align="center">Game erah</h1>
<h2 align="center">Sistema sobre a evolução dos jogos</h2>
<div align="center">

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white) 
![Git](https://img.shields.io/badge/GIT-E44C30?style=for-the-badge&logo=git&logoColor=white) 
![Figma](https://img.shields.io/badge/Figma-696969?style=for-the-badge&logo=figma&logoColor=figma) 
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-000?style=for-the-badge&logo=postgresql) 
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
</div>

É um programa feito em PHP feito para mostrar a evolução dos jogos. Ele foi criado para trazer um conhecimento de evolução da tecnologia em games, trazendo uma tela arrumada,facil e simples para os visitantes.
A plataforma deixa você fazer as tarefas principais do dia a dia(se vc for um Adm), como cadastrar, ver a lista, mudar os dados e apagar os registros, usando o sistema de CRUD que funciona junto com um banco de dados PostgreSQL.

---

## 1 Como iniciar o projeto na sua máquina
Para este trabalho rodar no seu computador, será necessário ter instalado um ambiente de servidor local  e o banco de dados PostgreSQL.

### 2 Pré-Requisitos
- 1 PHP (v7.4 no mínimo) habilitado com o pdo_pgsql.
- 2 PostgreSQL instalado e a rodar na porta 5432.
- 3 Ter o git instalado na máquina.
---

### 3 Instalação

#### Passo 1: Clonar o Repositório no git hub
Abra o *Git Bash*, e escreva: 
```bash
# Copie o reposítório de fora do GitHub
git clone https://github.com/Livia-MP-Null/game_erah.git

# Abra o arquivo clonado
cd game_erah
```
---

#### Passo 2: Configurar o Banco de Dados
O projeto deve contar com um arquivo SQL dentro da pasta database, nela está toda as estrutura do Banco de Dados ultilizada pelo Backend. 

Siga os passos descritos abaixo para para recriar o banco no seu PostgreSQL:

1. Abra o Terminal 
2. Conecte-se ao Postgres usando o usuário padrão 

```bash
psql -U postgres
```
3. Se já existir o banco de dados de algum teste anterior, delete-o e recrie a database, usando os comandos abaixo:
```sql
DROP DATABASE IF EXISTS game_erahdb; 
CREATE DATABASE game_erahdb;
```
4. Crie um usuário do Postgres específico para gerenciar este Banco de Dados, ainda com o Postgres aberto, escreva os comandos:
```sql
CREATE USER game_erah WITH PASSWORD "*sua_senha*";
ALTER DATABASE game_erahdb OWNER TO game_erah;
\q
```

5. Após transferirmos o Banco de Dados para o usuário *game_erah*, abriremos a pasta *database*, e faça o seguinte comando para restaurar as tabelas e registros:
```bash
# Caso você não tenha aberto a pasta, faça:
cd database
# Usamos o arquivo já existente para recuperar as estruturas
psql -U game_erah -h localhost -d game_erahdb -f dumpgame_erahdb.sql
# Retornamos para a raiz
cd ..
```
---
#### Passo 3: Configurar a Conexão no PHP
Pelo VSCode, abra o arquivo *connect.php* na pasta *database*, editando apenas um campo:
```php
$host = "localhost"; # Não altere esse campo
$dbname = "game_erahdb"; # Não altere esse campo
$user = "game_erah"; # Não altere esse campo
$password = "INSIRA SUA SENHA AQUI"; # Altere apenas esse campo
```
---

#### Passo 4: Acessar a Aplicação localmente
Depois de seguir todos esse passos, você abrirá o terminal e fará os seguintes comandos:
```bash
# No caso de a raiz do projeto não estar aberta, use o comando:
cd game_erah
# Caso você deseje rodar a aplicação localmente, execute o comando:
php -S localhost:8000 
```
Por fim, abra no seu navegador de preferencia, digite o URL *localhost:8000*, e aproveite a aplicação.

---


## Requisitos funcionais
ID	         Titúlo         Descrição                                            Prioridade
RF01. Cadastro do usuário. O sistema deve permitir o registo de novos usuários	       alta.
RF02. Relatório dos usuários.	O sistema deve listar todos os usuários cadastrados na plataforma para a visualização.	  alta.
RF03. Atualização do usuário. O sistema deve possuir a capacidade de alterar o estado : Ativo ou Inativo.            	  alta.
RF04. Exclusão do usuário.	O sistema deve permitir apagar o registo de um usuário do banco de dados.	                  alta
RF05. Consultar o usuário.	O sistema deve listar o Usuário com base no id mostrando as informações sobre ele.	     alta.
RF06. Linha do Tempo por Décadas. O sistema deve permitir que o usuário navegue pela história dos jogos dividida em décadas. alta.
RF07. Filtragem por Categoria de Destaque. O sistema deve oferecer filtros para separar os jogos de cada época em: Mais Jogados, Melhor Qualidade Gráfica e Melhor Jogabilidade. media
RF08 - Ficha Técnica do Jogo. O sistema deve exibir uma página detalhada para cada jogo, contendo ano de lançamento, desenvolvedora, plataformas originais, imagens/vídeos e uma breve descrição do seu impacto cultural. media
RF08 - Painel Administrativo (CMS): O sistema deve permitir que administradores cadastrem, editem ou removam jogos(e usuários), décadas e categorias de destaque. media
RF09 - Busca Global: O sistema deve permitir que o usuário pesquise por um jogo específico digitando o nome na barra de busca. baixa

## Requisitos não funcioanais 
ID	         Titúlo         Descrição                                            Prioridade
RNF01 - Responsividade: A interface do site deve ser totalmente adaptável, funcionando perfeitamente em celulares, tablets e computadores. baixa
RNF02 - Desempenho de Mídia: O site deve carregar imagens (como prints de jogos antigos) e trechos de vídeo (gameplays) de forma otimizada (usando formatos como WebP e lazy loading) para que a página inicial carregue em menos de 2,5 segundos. média
RNF03 - Estética e Design: O design deve ser temático e imersivo, utilizando uma identidade visual que remeta à cultura gamer (como um modo escuro com detalhes em neon ou transições que simulem a evolução gráfica). baixa
RNF04 - Disponibilidade: O site deve ser hospedado em uma plataforma que garanta pelo menos 99.5% de tempo online . baixa
RNF05 - SEO: O código deve seguir as boas práticas de SEO para garantir que as páginas de jogos e décadas apareçam bem posicionadas em buscas no Google. alta

### Objetivos :

#### Cria usuario:

#### exclui usuário:

#### Consulta todos os usuários cadastrados :

#### consulta usuario especifico:

#### Atualiza cadastro de usuário:

#### Consulta jogos por década e categoria:

#### Detalhes dos jogos:

#### Consulta detalhes de um jogo específico :

#### Usuário favorita um jogo:

#### Usuário envia comentário sobre um jogo:
