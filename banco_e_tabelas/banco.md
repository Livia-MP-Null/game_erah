```mermaid
---
title: game.erah
---
erDiagram
    usuarios{
        int ID PK "UNIQUE AUTOINCREMENT"
        varchar(255) nomeuser "NOT NULL"
        varchar(20) telefone "NOT NULL"
        varchar(254) email "NOT NULL UNIQUE"
        date nascimento "NOT NULL"
        
    }
    jogos{
        INT ID PK "UNIQUE AUTOINCREMENT"
        varchar(255) titulo "NOT NULL"
        INT ano_lancamento "NOT NULL"
        varchar(255) desenvolvedora "NOT NULL"
        varchar(255) distribuidora "NOT NULL"
        text descricao "NOT NULL"
    }
    destaques_jogos{
        INT ID PK "UNIQUE AUTOINCREMENT"
        INT jogo_id FK "NOT NULL"
        varchar(255) destaque_jogos "NOT NULL"
        INT posicao_ranking "NOT NULL"
    }

---
relacionamento de tabelas
---

```