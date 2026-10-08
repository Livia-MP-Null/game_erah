-- =====================================================================

-- =====================================================================

-- 1) Tabela de curtidas 
CREATE TABLE IF NOT EXISTS curtida (
    usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    jogo_id    INT NOT NULL REFERENCES jogo(id)     ON DELETE CASCADE,
    PRIMARY KEY (usuario_id, jogo_id)
);

-- 2) Apagar um USUÁRIO que já favoritou ou comentou.
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN
        SELECT c.conrelid::regclass::text AS tabela,
               c.conname                  AS nome,
               pg_get_constraintdef(c.oid) AS def
        FROM pg_constraint c
        WHERE c.contype = 'f'
          AND c.confrelid = 'usuarios'::regclass
          AND c.confdeltype = 'a'
    LOOP
        EXECUTE format('ALTER TABLE %s DROP CONSTRAINT %I', r.tabela, r.nome);
        EXECUTE format('ALTER TABLE %s ADD CONSTRAINT %I %s ON DELETE CASCADE',
                       r.tabela, r.nome, r.def);
    END LOOP;
END $$;

-- 3) Permissão para o usuário que o site usa
GRANT ALL ON ALL TABLES    IN SCHEMA public TO game_erah;
GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO game_erah;

-- 4) CONFERÊNCIA: devem aparecer curtida, favorito, comentario...
SELECT conrelid::regclass AS tabela, conname AS restricao, pg_get_constraintdef(oid) AS definicao
FROM pg_constraint
WHERE contype = 'f' AND confrelid IN ('usuarios'::regclass, 'jogo'::regclass)
ORDER BY 1;
