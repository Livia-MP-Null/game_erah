<div align="center">

# Game Erah

### Uma viagem pela evolução dos jogos, década por década

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-336791?style=flat-square&logo=postgresql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white)
![Figma](https://img.shields.io/badge/Figma-F24E1E?style=flat-square&logo=figma&logoColor=white)

[Sobre](#sobre-o-projeto) · [Como rodar](#como-rodar-o-projeto) · [Estrutura](#estrutura-de-pastas) · [Requisitos](#requisitos) · [Banco de dados](#banco-de-dados) · [Diagramas](#diagramas)

</div>

---

<a name="sobre-o-projeto"></a>
## Sobre o projeto

O **Game Erah** é uma aplicação web feita em **PHP** com banco de dados **PostgreSQL** que conta a história dos videogames de um jeito simples e visual. Cada década tem seus jogos de destaque, e quem visita o site consegue ver como a tecnologia, os gráficos e a jogabilidade mudaram ao longo do tempo.

A plataforma tem três tipos de acesso:

| Perfil | O que pode fazer |
|--------|------------------|
| **Visitante** | Navegar pelas décadas, consultar jogos com classificações por categoria e ver a ficha técnica |
| **Usuário cadastrado** | Tudo do visitante + curtir, favoritar jogos e enviar ou excluir seus próprios comentários |
| **Administrador** | Gerenciar (CRUD) usuários e jogos, visualizar painel com métricas e gerenciar comentários |

> [!NOTE]
> O CRUD completo (cadastrar, listar, atualizar e apagar) funciona integrado ao banco PostgreSQL, tanto para usuários quanto para os jogos e suas mídias.

---

## Índice

- [Sobre o projeto](#sobre-o-projeto)
- [Como rodar o projeto](#como-rodar-o-projeto)
  - [Pré-requisitos](#pré-requisitos)
  - [Passo a passo detalhado](#passo-a-passo-detalhado)
- [Estrutura de pastas](#estrutura-de-pastas)
- [Requisitos](#requisitos)
  - [Requisitos funcionais](#requisitos-funcionais)
  - [Requisitos não funcionais](#requisitos-não-funcionais)
  - [Regras de negócio](#regras-de-negócio)
- [O que o sistema faz](#o-que-o-sistema-faz)
- [Banco de dados](#banco-de-dados)
- [Diagramas](#diagramas)
- [Protótipos](#protótipos)
- [Como contribuir](#como-contribuir)
- [Contato](#contato)

---

<a name="como-rodar-o-projeto"></a>
## Como rodar o projeto

<a name="pre-requisitos"></a>
### Pré-requisitos

Antes de começar, confira se você tem na máquina:

- **PHP** com a extensão `pdo_pgsql` habilitada
- **PostgreSQL** instalado e rodando
- **Git**

<a name="passo-a-passo-detalhado"></a>
### Passo a passo detalhado

<details>
<summary><b>1️ Clonar o repositório</b></summary>

<br>

Abra o *Git Bash* e rode:

```bash
# Baixa o projeto do GitHub
git clone [https://github.com/Livia-MP-Null/game_erah.git](https://github.com/Livia-MP-Null/game_erah.git)

# Entra na pasta do projeto
cd game_erah
