-- =====================================================================
--  game.erah | LIMPAR VÍDEOS ANTIGOS (OPCIONAL)
--
--  Não é preciso mudar a estrutura do banco: o link do vídeo é guardado
--  na MESMA coluna "video" (VARCHAR 255), onde antes ficava o nome do
--  arquivo enviado.
--
--  Use este arquivo só se algum jogo ainda tiver o NOME DE UM ARQUIVO
--  (algo como 168333c5b3c3d2200c230c7df3ae7bac.mp4) na coluna video.
-- =====================================================================

-- 1) Só olha: lista os jogos cujo "video" não é um link (ainda é nome de arquivo).
SELECT id, titulo, video
FROM jogo
WHERE video IS NOT NULL
  AND video !~ '^https?://';

-- 2) Limpa esses valores. Depois é só colar o link do vídeo pela página de editar jogo.
--    (Descomente as 3 linhas abaixo para rodar.)
-- UPDATE jogo
-- SET video = NULL
-- WHERE video IS NOT NULL AND video !~ '^https?://';
