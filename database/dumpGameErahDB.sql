--
-- PostgreSQL database dump
--

\restrict 8p6osUiQKrpKCTLHvFvB64fynXThIQIqcdaGG9bggsKZwqim0nMIphJ99PWBSaN

-- Dumped from database version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)
-- Dumped by pg_dump version 18.6 (Ubuntu 18.6-0ubuntu0.26.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: typeemail; Type: DOMAIN; Schema: public; Owner: game_erah
--

CREATE DOMAIN public.typeemail AS text
	CONSTRAINT typeemail_check CHECK ((VALUE ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'::text));


ALTER DOMAIN public.typeemail OWNER TO game_erah;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: categoria; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.categoria (
    id integer NOT NULL,
    nome character varying(255)
);


ALTER TABLE public.categoria OWNER TO game_erah;

--
-- Name: categoria_id_seq; Type: SEQUENCE; Schema: public; Owner: game_erah
--

CREATE SEQUENCE public.categoria_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categoria_id_seq OWNER TO game_erah;

--
-- Name: categoria_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: game_erah
--

ALTER SEQUENCE public.categoria_id_seq OWNED BY public.categoria.id;


--
-- Name: comentario; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.comentario (
    id integer NOT NULL,
    usuario_id integer,
    jogo_id integer,
    texto text NOT NULL,
    criado_em timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.comentario OWNER TO game_erah;

--
-- Name: comentario_id_seq; Type: SEQUENCE; Schema: public; Owner: game_erah
--

CREATE SEQUENCE public.comentario_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.comentario_id_seq OWNER TO game_erah;

--
-- Name: comentario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: game_erah
--

ALTER SEQUENCE public.comentario_id_seq OWNED BY public.comentario.id;


--
-- Name: curtida; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.curtida (
    usuario_id integer NOT NULL,
    jogo_id integer NOT NULL
);


ALTER TABLE public.curtida OWNER TO game_erah;

--
-- Name: decada; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.decada (
    id integer NOT NULL,
    nome character varying(255),
    ano_inicio integer
);


ALTER TABLE public.decada OWNER TO game_erah;

--
-- Name: decada_id_seq; Type: SEQUENCE; Schema: public; Owner: game_erah
--

CREATE SEQUENCE public.decada_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.decada_id_seq OWNER TO game_erah;

--
-- Name: decada_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: game_erah
--

ALTER SEQUENCE public.decada_id_seq OWNED BY public.decada.id;


--
-- Name: favorito; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.favorito (
    usuario_id integer NOT NULL,
    jogo_id integer NOT NULL
);


ALTER TABLE public.favorito OWNER TO game_erah;

--
-- Name: jogo; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.jogo (
    id integer NOT NULL,
    titulo character varying(255),
    ano_lancamento integer,
    desenvolvedora character varying(255),
    plataformas character varying(255),
    descricao text,
    imagem character varying(255),
    decada_id integer,
    video character varying(255),
    criado_por character varying(255)
);


ALTER TABLE public.jogo OWNER TO game_erah;

--
-- Name: jogo_categoria; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.jogo_categoria (
    jogo_id integer NOT NULL,
    categoria_id integer NOT NULL,
    posicao integer
);


ALTER TABLE public.jogo_categoria OWNER TO game_erah;

--
-- Name: jogo_id_seq; Type: SEQUENCE; Schema: public; Owner: game_erah
--

CREATE SEQUENCE public.jogo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jogo_id_seq OWNER TO game_erah;

--
-- Name: jogo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: game_erah
--

ALTER SEQUENCE public.jogo_id_seq OWNED BY public.jogo.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: game_erah
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nome character varying(255),
    nasc date,
    email public.typeemail,
    senha character varying(255),
    admin boolean DEFAULT false NOT NULL,
    ativo boolean DEFAULT true NOT NULL
);


ALTER TABLE public.usuarios OWNER TO game_erah;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: game_erah
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO game_erah;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: game_erah
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: categoria id; Type: DEFAULT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.categoria ALTER COLUMN id SET DEFAULT nextval('public.categoria_id_seq'::regclass);


--
-- Name: comentario id; Type: DEFAULT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.comentario ALTER COLUMN id SET DEFAULT nextval('public.comentario_id_seq'::regclass);


--
-- Name: decada id; Type: DEFAULT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.decada ALTER COLUMN id SET DEFAULT nextval('public.decada_id_seq'::regclass);


--
-- Name: jogo id; Type: DEFAULT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.jogo ALTER COLUMN id SET DEFAULT nextval('public.jogo_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Data for Name: categoria; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.categoria (id, nome) FROM stdin;
1	Melhor jogabilidade
2	Mais jogados
3	Melhor qualidade gráfica
\.


--
-- Data for Name: comentario; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.comentario (id, usuario_id, jogo_id, texto, criado_em) FROM stdin;
1	1	10	Muito bom, que saia o remake logo!	2026-10-09 16:37:34.983378
2	1	4	bla	2026-10-14 11:27:15.064065
\.


--
-- Data for Name: curtida; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.curtida (usuario_id, jogo_id) FROM stdin;
1	1
5	1
1	10
1	4
\.


--
-- Data for Name: decada; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.decada (id, nome, ano_inicio) FROM stdin;
1	Anos 2020	2020
2	Anos 80	1980
3	Anos 2000	2000
4	Anos 90	1990
5	Anos 60	1960
6	Anos 2010	2010
7	Anos 70	1970
\.


--
-- Data for Name: favorito; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.favorito (usuario_id, jogo_id) FROM stdin;
1	10
1	4
\.


--
-- Data for Name: jogo; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.jogo (id, titulo, ano_lancamento, desenvolvedora, plataformas, descricao, imagem, decada_id, video, criado_por) FROM stdin;
4	Dactyl Nightmare	1991	W Industries	\N	O que é Dactyl Nightmare?\r\nLançado em 1991 para Virtuality, uma linha de jogos de realidade virtual encontrada nos fliperamas dos Estados Unidos no início dos anos 90, Dactyl Nightmare é basicamente um FPS para dois jogadores que se passa em um cenário com várias plataformas. Os jogadores devem enfrentar uns aos outros, usando armas como machados e bestas, enquanto precisam se cuidar de um pterodáctilo que voa pelos arredores e se abate sobre o jogador para agarrá-lo e soltá-lo fora do cenário. A seguir, você pode ver o comercial promocional de Dactyl Nightmare:	fad0500d15fb08a9e8cbd35a460dcbf3.jpg	4	https://www.youtube.com/watch?v=hBkP2to1P_c	W Industries
2	Pong	1972	Atari	\N	Pong é um jogo eletrônico de esporte de arcade com temática de tênis de mesa, com gráficos bidimensionais, desenvolvido pela Atari e lançado originalmente em 1972. Foi um dos primeiros jogos de arcade; foi criado por Allan Alcorn como um exercício de treinamento atribuído a ele pelo cofundador da Atari, Nolan Bushnell, mas Bushnell e o cofundador da Atari, Ted Dabney, ficaram surpresos com a qualidade do trabalho de Alcorn e decidiram fabricar o jogo. Bushnell baseou o conceito do título em um jogo eletrônico de pingue-pongue incluído no Magnavox Odyssey, o primeiro console doméstico de jogos eletrônicos. Em resposta, a Magnavox mais tarde processou a Atari por violação de patente.\r\n\r\nPong foi o primeiro jogo comercialmente bem-sucedido e ajudou a estabelecer a indústria de jogos eletrônicos junto com o Magnavox Odyssey. Logo após seu lançamento, várias empresas começaram a produzir jogos que imitavam de perto sua jogabilidade. Eventualmente, os concorrentes da Atari lançaram novos tipos de jogos que se desviaram do formato original de Pong em vários graus, e isso, por sua vez, levou a Atari a incentivar sua equipe a ir além de Pong e produzir jogos mais inovadores.	f9f83887431313d1c6e8f382406c64e4.jpg	7	https://www.youtube.com/watch?v=fhd7FfGCdCo	Atari
5	World of Warcraft	2004	Blizzard Entertainment	\N	World of Warcraft (também conhecido pelo acrônimo WoW) é um jogo on-line do gênero MMORPG, desenvolvido e distribuído pela produtora Blizzard Entertainment e lançado em 2004. O jogo se passa no mundo fantástico de Azeroth, introduzido no primeiro jogo da série, Warcraft: Orcs & Humans, lançado em 1994.\r\n\r\nÉ considerado o mais popular MMORPG de todo o mundo, tendo chegado a ter 11 milhões de jogadores ativos no ano de 2010.[1][2] Em 2016, contava com 5 milhões de jogadores regulares.[3]\r\n\r\nJoga-se com um programa cliente ligado a uma rede de servidores. Existem servidores para atender Estados Unidos, Oceania, Europa, Rússia, China, Coreia do Sul, Taiwan, Sudeste Asiático, Brasil e América Latina. O acesso aos servidores requer uma chave original e é pago mensalmente, mediante assinatura com dinheiro real ou com ouro conquistado dentro do jogo.[4] A partir de julho de 2011, o jogo também passou a ser distribuído sem custos por meio da versão Starter Edition, que permite ao jogador ir até o nível 20 de personagem dentro do jogo[5] (veja mais na subcategoria "Versão Gratuita de World of Warcraft" abaixo).Jogabilidade\r\nIniciando uma sessão de personagem ou de jogo\r\nAssim como em outros MMORPGs, os jogadores controlam um avatar de personagem em um mundo de jogo, em perspectiva de terceira ou primeira pessoa, explorando o cenário, lutando contra diversos monstros, completando missões e interagindo com personagens não jogáveis ​​(NPCs) ou outros jogadores. Também similar a outros MMORPGs, World of Warcraft exige que o jogador pague uma assinatura usando um cartão de crédito ou débito, cartões pré-pagos da Blizzard ou um Token WoW comprado dentro do jogo. Jogadores sem assinatura podem usar uma conta de teste que permite que o personagem alcance o nível 20, mas com muitos recursos bloqueados	014b6a8b07d47436710b9caebad6e365.jpg	3	https://www.youtube.com/watch?v=hTCBfrFZ2qU	Blizzard Entertainment
6	Minecraft	2011	Mojang	\N	Minecraft é um jogo eletrônico sandbox de sobrevivência de 2011 desenvolvido e publicado pela desenvolvedora sueca Mojang Studios. Originalmente criado por Markus "Notch" Persson usando a linguagem de programação Java, a primeira versão alfa pública foi lançada em 17 de maio de 2009. O jogo foi desenvolvido continuamente a partir de então, recebendo um lançamento completo em 18 de novembro de 2011. Posteriormente, Persson deixou a Mojang e cedeu o controle do desenvolvimento a Jens "Jeb" Bergensten. Nos anos desde o seu lançamento, o jogo foi portado para diversas plataformas, incluindo smartphones, tablets e diversos consoles de jogos eletrônicos. Em 2014, a Mojang e a propriedade intelectual de Minecraft foram compradas pela Microsoft por US$ 2.5 bilhões.\r\n\r\nEm Minecraft, os jogadores exploram um mundo aberto tridimensional intencionalmente em blocos, pixelizado e gerado proceduralmente com terreno virtualmente infinito, podendo descobrir e extrair matérias-primas, ferramentas artesanais, construir estruturas ou terraplenagens e, dependendo do modo de jogo, podem combater inimigos controlados por computador, bem como cooperar ou competir contra outros jogadores no mesmo mundo. Esses modos incluem um modo sobrevivência, no qual o jogador precisa adquirir recursos para construir o mundo e se manter vivo, e um modo criativo, onde os jogadores têm recursos ilimitados e a capacidade de voar. A grande comunidade do jogo oferece uma grande variedade de conteúdo gerado pelos usuários, como modificações, servidores, skins de jogadores, pacotes de texturas e mapas personalizados, que adicionam novas mecânicas e possibilidades ao jogo. O cenário de speedrunning do jogo atraiu um número significativo de seguidores.Jogabilidade\r\nMinecraft é um jogo eletrônico dos gêneros sandbox e sobrevivência que não possui objetivos específicos a serem alcançados, permitindo aos jogadores uma grande liberdade de escolha de como jogá-lo.[20] No entanto, existe um sistema de conquistas,[21] conhecido como "progressos" na edição Java. A jogabilidade é apresentada numa perspectiva em primeira pessoa por padrão, mas os jogadores têm a opção de selecionarem uma visão em terceira pessoa.[22] O mundo aberto de Minecraft é composto de objetos tridimensionais brutos — principalmente cubos e fluidos, sendo comumente chamados de "blocos" — representando vários materiais, como pedra, minérios, troncos de árvores, água e lava. A jogabilidade principal gira em torno de pegar e colocar esses objetos, que são organizados em uma grade tridimensional, enquanto os jogadores podem se mover livremente pelo mundo. Os jogadores podem "extrair" blocos e depois colocá-los em outro lugar, permitindo que eles construam coisas distintas.[23] Muitos analistas descreveram o sistema de física de Minecraft como irrealista;[24] líquidos fluem continuamente por uma distância horizontal limitada dos blocos de origem, que podem ser removidos colocando um bloco sólido em seu lugar ou colocando-o em um balde. Minecraft também contém um material conhecido como redstone, que pode ser usado para fabricar dispositivos mecânicos primitivos e circuitos elétricos, permitindo a construção de muitos sistemas complexos.[25]	fe3634d003c7b0fdc59aa6a589689ec5.jpg	6	https://www.youtube.com/watch?v=MmB9b5njVbA	Markus Persson
9	River Raid	1982	Activision	\N	River Raid é um jogo de Video game para o console Atari 2600, criado por Carol Shaw, da empresa Activision, em 1982. É considerado um clássico e um dos jogos mais populares de seu tempo. A versão do jogo para Atari 2600 vendeu um milhão de cópias, sem contar cópias não autorizadas e cartuchos com vários jogos. Assim, ele é um dos 13 jogos que fazem parte do seleto grupo que vendeu ao menos 1 milhão de cópias no Atari 2600.\r\n\r\nO Jogo\r\nO jogador (ou até 2 jogadores, em turnos alternados) controla um avião que sobrevoa um rio. Ao contrário dos jogos de tiro até então, onde todo o jogo se passava em uma tela fixa, em River Raid a aeronave move-se verticalmente ao longo do rio, repleto de navios, helicópteros e aviões inimigos, com nível de dificuldade progressivo. A velocidade do avião é regulada pelo jogador. Outra inovação foi a introdução de um tanque de combustível limitado, que obriga o jogador a reabastecer o avião nos postos espalhados pelo rio.\r\n\r\nInovações\r\nAlém de ser considerado o pai do Checkpoint, River Raid foi um marco de inovação para a época. O jogo tem diferentes velocidades para controle do avião, e é possível diminuir e acelerar a velocidade, colidir com vários objetos diferentes e abastecer. Parece rudimentar hoje em dia, mas eram muitos elementos e recursos se comparados aos outros jogos da época, em que o jogador era apenas um pontinho na tela. Além do avião amarelo, havia barcos parados, alguns que se movimentavam aleatoriamente, helicópteros e o avião kamikaze, que cruzava a tela rapidamente.\r\n\r\nAinda é desconhecido alguém que tenha chegado ao final do jogo.[1] Apesar do final de River Raid ser considerado mito pelos jogadores, ele existe. Ao alcançar a pontuação de 999.999, o fim é apresentado: O avião literalmente explode sozinho.\r\n\r\nSequências e remakes\r\nRiver Raid foi portado para todos os sistemas presentes na década de 80, como Atari 2600, Windows e MSX. Também foram lançadas versões diferentes para Colecovision, Atari 5200, Commodore 64 e Intellivision. Atualmente há inúmeros remakes e versões não autorizadas presentes nas lojas de aplicativos para Android e iOS.\r\n\r\nEm 1988, a Activision lançou River Raid II, sequência programa por David Lubar. Com jogabilidade similar, o novo River Raid tinha gráficos melhores, cenários diferentes e dificuldade ainda maior.	8c945cb7152d6ba1addae376a6c4d549.webp	2	https://www.youtube.com/watch?v=zYLA_uO-XH0	Carol Shaw
10	Star Wars: Knights of the Old Republic	2003	BioWare	\N	Star Wars: Cavaleiros da Velha República (do inglês: Knights of the Old Republic - KotOR) é um jogo eletrônico de RPG desenvolvido em 2003 pela Aspyr para computador com o sistema Apple Macintosh, e pela BioWare para videogame Xbox em julho de 2003 e para computador com o sistema Microsoft Windows em novembro de 2003 e, publicado pela LucasArts e Disney.[1]\r\n\r\nEste jogo é o primeiro do ramo de RPG para a saga de Star Wars. A sequência Star Wars: Cavaleiros da Velha República II: Os Lords Sith foi desenvolvida pela Obsidian Entertainment sob sugestão da BioWare.[2]	4d5a61c6f1612c3088ae7313b6bfb3f4.webp	3	https://www.youtube.com/watch?v=ulYmv5cEsgA	George Lucas
11	BioShock	2007	2K Boston e 2K Austrália	\N	BioShock é um jogo eletrônico de tiro em primeira pessoa produzido pela Irrational Games (na altura, com o nome 2K Boston) e distribuído pela 2K Games. Foi lançado para as plataformas Microsoft Windows e Xbox 360 em Agosto de 2007; a versão para PlayStation 3, criada pela Irrational, 2K Marin, 2K Australia e Digital Extremes, foi lançada em Outubro de 2008 e a versão para Mac OS X, produzida pela Feral Interactive, em Outubro de 2009. Uma versão portátil também foi produzida pela IG Fun. O conceito para o jogo foi criado por Ken Levine, diretor criativo da Irrational, baseado em ideias do Objectivismo (como descrito por Ayn Rand) enquanto incorpora influências de outros autores como George Orwell. O jogo é considerado um sucessor espiritual da série System Shock, onde muitos da equipe da Irrational, incluindo Levine, já tinham trabalhado.\r\n\r\nA história de BioShock decorre em 1960, no qual o jogador controla Jack, depois do seu avião cair no oceano, junto a um terminal batisférico que leva à cidade subaquática de Rapture. Construída pelo magnata Andrew Ryan, a cidade tinha como objectivo inicial ser uma utopia isolada, mas a descoberta do ADAM, um plasmídeo que dá poderes sobre-humanos, fez com que a cidade entrasse em declínio turbulento. Jack tenta encontrar uma maneira de escapar, lutando contra enormes quantidades de inimigos obcecados por ADAM, como os mortíferos Big Daddies, enquanto também se encontra com alguns humanos sãos e através deles acaba eventualmente por aprender mais sobre o passado de Rapture. O jogador, como Jack, consegue derrotar os inimigos com o uso de várias armas, utilizando plasmídeos que lhe dão poderes únicos ou fazendo com que as próprias defesas da cidade se virem contra os seus habitantes. O jogo inclui elementos de survival horror (horror de sobrevivência) e de jogos em role-playing (RPG), dando ao jogador diferentes opções ao confrontar-se com inimigos como disfarçar-se, bem como escolhas morais entre matar ou salvar personagens.BioShock é um jogo de tiro em primeira pessoa, com personalizações como em RPGs e elementos de stealth, similar a System Shock 2. O jogador desempenha o papel de Jack, que prossegue o seu caminho por Rapture, usando armas e plasmídeos (alterações genéticas), para conseguir completar objectivos. Por vezes, o jogador pode utilizar tácticas de stealth para evitar ser detectado por câmeras de segurança e por metralhadoras automáticas.[13] Enquanto explora Rapture, o jogador adquire dinheiro, que pode ser usado em máquinas de venda para comprar munições, saúde/energia, entre outros equipamentos adicionais.[14] Também é possível encontrar fragmentos ou peças, que podem ser usadas nas máquinas "U-Invent" para criar novas armas e itens úteis. As câmaras, as metralhadoras, os cofres, fechaduras e as máquinas de venda, podem ser infiltradas nos seus sistemas para criar vantagens ao jogador, como atacar os seus inimigos, revelar o seu conteúdo, permitir entrar em áreas antes bloqueadas, ou permitir ao jogador comprar itens com desconto.[15] Para se infiltrar nos sistemas, o jogador tem de completar um mini-jogo de tempo limitado, similar a Pipe Mania.[16] Cedo durante o jogo é dado ao jogador uma "câmara de pesquisa", que permite a Jack tirar fotografias dos inimigos e com isso analisá-los. Quanto melhor a qualidade delas mais benefícios ela trará. Depois de fazer várias observações sobre um inimigo, o jogador recebe a capacidade de criar mais dano, genes tónicos, e outro bónus ao enfrentar esse tipo de inimigo em confrontos futuros.[17] As "Vita-Chambers", câmaras envidraçadas, são encontradas pelo jogo, mas não podem ser usar directamente. Em vez disso, se Jack morrer, o seu corpo é reconstituído na mais próxima, mantendo todos os seus bens, mas apenas uma parte de toda a sua saúde.[18] Numa atualização feita ao jogo, o jogador tem a opção de desactivar as "Vita-Chambers", assim se Jack morrer, o jogador terá de reiniciar a partir de um jogo previamente salvo.	f12cf30c2af410f5714b14d7df4b6a75.jpg	3	https://www.youtube.com/watch?v=_KVfPMSK8hA	Irrational Games
7	Grand Theft Auto V (GTA V)	2013	Grand Theft Auto V (GTA V)	\N	Grand Theft Auto V é um jogo eletrônico de ação-aventura de 2013 desenvolvido pela Rockstar North e publicado pela Rockstar Games. É o sétimo título principal da série Grand Theft Auto, após Grand Theft Auto IV (2008), e o décimo quinto jogo da franquia no geral. Ambientado no estado fictício de San Andreas, baseado no sul da Califórnia, o modo história para um jogador acompanha três protagonistas — o ladrão de bancos aposentado Michael De Santa (Ned Luke), o gângster de rua Franklin Clinton (Shawn Fonteno) e o traficante de armas e drogas Trevor Philips (Steven Ogg) — e suas tentativas de realizar assaltos enquanto sofrem pressão de uma agência governamental corrupta e de criminosos poderosos. Os jogadores podem explorar livremente o mundo aberto de San Andreas e a cidade fictícia de Los Santos, baseada em Los Angeles.\r\n\r\nO mundo do jogo é explorado a pé e de veículo, tanto em perspectiva de terceira quanto de primeira pessoa. Os jogadores controlam os protagonistas durante o modo para um jogador e podem alternar entre eles, tanto durante quanto fora das missões. A história gira em torno de sequências de assaltos, e muitas missões envolvem tiroteios e direção de veículos. Um sistema de "procurado" controla a intensidade da resposta policial aos jogadores que cometem crimes. Em Grand Theft Auto Online, o modo multijogador online do jogo, até 30 jogadores participam de diversos modos de jogo cooperativos e competitivos.Jogabilidade\r\n\r\nOs jogadores são capazes de se protegerem atrás de objetos para que não tomem danos de inimigos\r\nGrand Theft Auto V é um jogo eletrônico de ação e aventura[1] que pode ser jogado a partir de uma perspectiva em terceira pessoa[2] ou primeira pessoa, porém esta última é disponível apenas nas plataformas PlayStation 4, Xbox One e Microsoft Windows.[3] Os jogadores completam missões, que são cenários lineares com objetivos definidos, a fim de progredirem pela história principal.[4] Os jogadores podem vagar livremente pelo mundo de jogo quando não estão dentro de missões. O mundo é composto pela área rural do estado de San Andreas e pela cidade ficcional de Los Santos, com sua área total sendo bem maior do que em títulos anteriores da série.[5] O mapa pode ser explorado completamente assim que o prólogo é completado, porém a progressão na história libera mais conteúdos de jogabilidade.	8b9760839162616c649371957cbde976.jpg	6	https://www.youtube.com/watch?v=ahOIJNLLtkI	Grand Theft Auto V (GTA V)
12	Cyberpunk 2077	2020	CD Projekt Red	\N	Cyberpunk 2077 é um jogo eletrônico de RPG de ação de 2020 desenvolvido pela CD Projekt Red e publicado pela CD Projekt. Baseado na série de jogos de mesa Cyberpunk de Mike Pondsmith, o enredo se passa na metrópole fictícia de Night City, na Califórnia, dentro do universo distópico Cyberpunk. O jogador assume o papel de V (dublado por Gavin Drea ou Cherami Leigh, dependendo do gênero do personagem), um mercenário que relutantemente recebe um "biochip" cibernético contendo um engrama do lendário astro do rock e terrorista Johnny Silverhand (interpretado por Keanu Reeves). À medida que a consciência de Johnny começa a sobrepor a de V, os dois precisam trabalhar juntos para se separar e salvar a vida de V, enquanto são perseguidos pela corporação Arasaka, um império tecnológico corporativo e líderes de fato da cidade.\r\n\r\nO desenvolvimento do jogo começou após o lançamento de The Witcher 3: Wild Hunt – Blood and Wine (2016). O jogo foi desenvolvido por uma equipe de cerca de 500 pessoas usando o motor gráfico REDengine 4. A CD Projekt lançou uma nova divisão em Breslávia, na Polônia, e firmou parcerias com a Digital Scapes, Nvidia, Q-LOC e Jali Research para auxiliar na produção, enquanto a Pondsmith atuou como consultora. A trilha sonora original foi composta por Marcin Przybyłowicz e contou com a contribuição de diversos artistas licenciados. Após anos de expectativa, Cyberpunk 2077 foi lançado para PlayStation 4, Stadia, Windows e Xbox One em dezembro de 2020, seguido pelo PlayStation 5 e Xbox Series X/S em fevereiro de 2022, o Nintendo Switch 2 em junho de 2025 como título de lançamento e macOS em julho de 2025. Uma expansão de conteúdo para download, Phantom Liberty, foi lançada para PlayStation 5, Windows e Xbox Series X/S em setembro de 2023, juntamente com os lançamentos para Nintendo Switch 2 e macOSCyberpunk 2077 é um jogo eletrônico de RPG de ação jogado numa perspectiva em primeira pessoa na pele de V (Vincent/Valerie),[3] um mercenário cuja voz,[4] rosto, estilo de cabelo, tipo de corpo e modificações, história de origem e roupas são personalizáveis. As categorias de estatísticas – Corpo, Inteligência, Reflexos, Habilidade Técnica e Moral – são diretamente influenciadas pelas classes de personagens que os jogadores assumem, sendo estas NetRunner (hacking), Techie (maquinário) e Solo (combate). A árvore de vantagens é ramificada nas categorias corpo a corpo, lâminas, revólveres, espingardas, rifles, hacking, combate à duas mãos, assassinato, "sangue frio", engenharia e atletismo. V deve consultar um "medicânico" para atualizar e comprar implantes de cyberware; os mercados negros do jogo oferecem habilidades de nível militar.[3][5][6][7][8][9] A raridade de qualquer equipamento é exibida por um sistema de camadas coloridas.[10] V é capaz se proteger, mirar, correr, pular, fazer pulos duplos e deslizar.[11][12] Ataques corpo a corpo podem ser tratados com armas de combate corpo a corpo.[6][13] Existem três tipos de armas de longo alcance, todas quais podem ser personalizadas e modificadas – Armas de Energia (padrão), Armas de Tecnologia (cuja munição é capaz de penetrar paredes e inimigos) e Armas Inteligentes (com balas teleguiadas).[14][15] Armas de longo alcance são equipadas para ricochetearem na direção do alvo e retardá-los no bullet time. Quatro tipos de dano podem ser infligidos e resistidos – Físico, Térmico, PEM e Químico.[5][9][16] O uso de armas aumenta a precisão e a velocidade de recarga, que se manifestam nas animações dos personagens.[17] Armeiros consertam e atualizam armas.[6] O jogo pode ser concluído sem matar ninguém,[18] com opções não letais para armas e cyberware	42542ddbc2d387a503fe6894a8057411.webp	1	https://www.youtube.com/watch?v=DvVjkqB3LH0	CD Projekt Red
1	Spacewar!	1962	Steve Russell	\N	Spacewar! é amplamente reconhecido como o primeiro videogame digital de computador da história. Lançado oficialmente em fevereiro de 1962, o jogo foi concebido e programado por um grupo de estudantes e entusiastas do Instituto de Tecnologia de Massachusetts (MIT), liderados por Steve Russell. Desenvolvido no gigantesco computador DEC PDP-1, o título nasceu puramente como uma forma de testar o poder de processamento gráfico em tempo real da máquina e divertir os alunos. O jogo, com gráficos monocromáticos, apresenta duas naves espaciais, cada uma controlada por um jogador humano, "agulha" e "cunha", que lutam entre si realizando manobras num poço gravitacional de uma estrela. Cada uma das naves possui uma quantidade limitada de combustível para realizar as manobras e um número limitado de torpedos, e segue a mecânica clássica, mantendo-se em movimento mesmo quando o jogador não acelera a nave. As naves são destruídas quando um torpedo inimigo as atinge ou colidem com a estrela, embora a aproximação dela possa proporcionar uma assistência gravitacional. A qualquer momento, os jogadores podem entrar no hiperespaço para se moverem para um novo local aleatório no ecrã, embora cada utilização desta habilidade possa resultar na destruição da sua própria nave. O jogo era inicialmente controlado por interruptores no PDP-1, mas Alan Kotok e Bob Saunders construíram um Gamepad para reduzir a dificuldade dos controlos do jogo.	02533919d6eb266f3991c69d572b67e2.jpg	5	https://www.youtube.com/watch?v=eePWlLKm_Bg	Steve Russell
3	Pac-Man	1980	Tōru Iwatani	\N	Pac-Man , originalmente intitulado Puck Man [ a ] ​​no Japão, é um jogo de labirinto de 1980 desenvolvido e publicado pela Namco para arcades . Foi lançado no Japão em 22 de maio de 1980 e pela Midway Manufacturing na América do Norte em outubro de 1980. [ 5 ] O jogador controla Pac-Man , que deve comer todos os pontos dentro de um labirinto fechado enquanto evita quatro fantasmas coloridos . Comer grandes pontos piscantes chamados "Power Pellets" faz com que os fantasmas fiquem temporariamente azuis e vulneráveis, permitindo que Pac-Man os coma para ganhar pontos de bônus.\r\n\r\nPac-Man foi criado por Toru Iwatani , que liderou uma equipe de nove pessoas; o desenvolvimento do jogo começou no início de 1979. Iwatani queria criar um jogo que agradasse tanto a mulheres quanto a homens, porque a maioria dos videogames da época tinha temas que atraíam interesses tradicionalmente masculinos, como guerra ou esportes. [ 6 ] [ 7 ] Embora a inspiração para o personagem Pac-Man tenha sido a imagem de uma pizza com uma fatia removida, Iwatani disse que arredondou o caractere japonês para boca, kuchi ( em japonês :口). Os personagens do jogo foram feitos para serem fofos e coloridos para atrair jogadores mais jovens. O título original japonês, Puck Man, derivava da frase japonesa paku paku taberu , que se refere a devorar algo; o título foi alterado para Pac-Man para o lançamento na América do Norte devido ao medo de vândalos danificarem os gabinetes convertendo o P em um F , como em fuck (foda-se) .Pac-Man é um jogo de videogame de ação [ 9 ] de perseguição em labirinto ; o jogador controla o personagem homônimo através de um labirinto fechado. O objetivo do jogo é comer todos os pontos colocados no labirinto enquanto evita quatro fantasmas coloridos — Blinky (vermelho), Pinky (rosa), Inky (ciano) e Clyde (laranja) — que perseguem Pac-Man. Quando Pac-Man come todos os pontos, o jogador avança para o próximo nível. Os níveis são indicados por ícones de frutas na parte inferior da tela. Entre os níveis, há pequenas cenas com Pac-Man e Blinky em situações humorísticas e cômicas.\r\n\r\nSe Pac-Man for apanhado por um fantasma, perde uma vida; o jogo termina quando todas as vidas são perdidas. Cada um dos quatro fantasmas tem a sua própria inteligência artificial (IA) única, ou "personalidade": Blinky persegue Pac-Man diretamente; Pinky e Inky tentam posicionar-se à frente de Pac-Man, geralmente encurralando-o; e Clyde alterna entre perseguir e fugir de Pac-Man dependendo da distância entre eles.	9bd68f29e53ea17a20a798718c29d48d.webp	2	https://www.youtube.com/watch?v=dD797m5uA6g	Tōru Iwatani
8	Roblox	2006	David Baszucki e Erik Cassel	\N	Roblox é uma plataforma de jogos online e um sistema de criação de jogos desenvolvido pela Roblox Corporation que permite aos usuários programar e jogar jogos criados por eles próprios ou por outros usuários. Foi criado por David Baszucki e Erik Cassel em 2004 e lançado ao público em 2006. Em fevereiro de 2025, a plataforma registrava uma média de 85.3 milhões de usuários ativos diários. Segundo a empresa, sua base mensal de jogadores inclui metade de todas as crianças norte-americanas com menos de 16 anos.\r\n\r\nA plataforma hospeda milhões de jogos criados por usuários (oficialmente chamados de "experiências"), todos criados usando um dialeto da linguagem de programação Lua e o mecanismo de jogo da plataforma, Roblox Studio.[7] Embora o Roblox seja gratuito, ele oferece compras no jogo feitas por meio de sua moeda virtual, conhecida como Robux, e os desenvolvedores de jogos na plataforma podem criar itens que custam Robux. Além disso, a plataforma hospeda uma grande economia virtual centrada nesses itens e Robux. Usando o programa "Developer Exchange" da plataforma, os criadores podem trocar os Robux ganhos por moeda real. A plataforma também tem sido usada para hospedar eventos e concertos virtuais, bem como advergames.	1fcb3fb31008a6ecad5b37cfc6352be7.jpg	3	https://www.youtube.com/watch?v=cmeVco-_8ww	Mais jogado da decada de 20
\.


--
-- Data for Name: jogo_categoria; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.jogo_categoria (jogo_id, categoria_id, posicao) FROM stdin;
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: game_erah
--

COPY public.usuarios (id, nome, nasc, email, senha, admin, ativo) FROM stdin;
2	loma	2222-02-01	loma@gmail.com	$2y$12$TIsHaRRv78KVmuwfdf6bzuhOunusDHqsbpL9ruQuBN3o5.7OvhTwq	f	t
1	game.erah	1111-01-01	game.era@gmail.com	$2y$12$.CXERhXNdFeQV4YaYdzEIuI.ckViOGKUJq011HVNJ.lo/zNZFM6X.	t	t
4	Sherolaine	2001-01-01	Sherolaine@gmail.com	$2y$12$gvN1FynQuzlsblxmPfJj7.mrYZoQChymq8kFJ2xGqsBJspQlepJ/2	f	t
5	Shana	2121-01-02	Shana@gmail.com	$2y$12$BLL9c1z7z/h8Up1pmpFN.uDCKPCyVzensDWHgoOS4TJ5AiC4dnhrS	f	t
6	wewe	1900-10-10	cassondra7360@uorak.com	$2y$12$k4JBHG81HDvGek/HBwV/feIO8Ut5PfcZwtxag5jweqJyfRlXx2uG6	f	f
7	Wanna	2000-01-01	Wanna1@gmail.com	$2y$12$X6Sq1MvWfJlufsa4gzUr7uz3hZZ63abMHPylpapDkgXAh17iH3nsC	f	t
\.


--
-- Name: categoria_id_seq; Type: SEQUENCE SET; Schema: public; Owner: game_erah
--

SELECT pg_catalog.setval('public.categoria_id_seq', 3, true);


--
-- Name: comentario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: game_erah
--

SELECT pg_catalog.setval('public.comentario_id_seq', 2, true);


--
-- Name: decada_id_seq; Type: SEQUENCE SET; Schema: public; Owner: game_erah
--

SELECT pg_catalog.setval('public.decada_id_seq', 7, true);


--
-- Name: jogo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: game_erah
--

SELECT pg_catalog.setval('public.jogo_id_seq', 12, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: game_erah
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 8, true);


--
-- Name: categoria categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.categoria
    ADD CONSTRAINT categoria_pkey PRIMARY KEY (id);


--
-- Name: comentario comentario_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.comentario
    ADD CONSTRAINT comentario_pkey PRIMARY KEY (id);


--
-- Name: curtida curtida_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.curtida
    ADD CONSTRAINT curtida_pkey PRIMARY KEY (usuario_id, jogo_id);


--
-- Name: decada decada_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.decada
    ADD CONSTRAINT decada_pkey PRIMARY KEY (id);


--
-- Name: favorito favorito_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.favorito
    ADD CONSTRAINT favorito_pkey PRIMARY KEY (usuario_id, jogo_id);


--
-- Name: jogo_categoria jogo_categoria_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.jogo_categoria
    ADD CONSTRAINT jogo_categoria_pkey PRIMARY KEY (jogo_id, categoria_id);


--
-- Name: jogo jogo_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.jogo
    ADD CONSTRAINT jogo_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: ux_usuarios_email; Type: INDEX; Schema: public; Owner: game_erah
--

CREATE UNIQUE INDEX ux_usuarios_email ON public.usuarios USING btree (email);


--
-- Name: curtida curtida_jogo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.curtida
    ADD CONSTRAINT curtida_jogo_id_fkey FOREIGN KEY (jogo_id) REFERENCES public.jogo(id) ON DELETE CASCADE;


--
-- Name: curtida curtida_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.curtida
    ADD CONSTRAINT curtida_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: comentario fk_comentario_jogo; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.comentario
    ADD CONSTRAINT fk_comentario_jogo FOREIGN KEY (jogo_id) REFERENCES public.jogo(id);


--
-- Name: comentario fk_comentario_usuario; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.comentario
    ADD CONSTRAINT fk_comentario_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: favorito fk_favorito_jogo; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.favorito
    ADD CONSTRAINT fk_favorito_jogo FOREIGN KEY (jogo_id) REFERENCES public.jogo(id);


--
-- Name: favorito fk_favorito_usuario; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.favorito
    ADD CONSTRAINT fk_favorito_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: jogo fk_jogo_decada; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.jogo
    ADD CONSTRAINT fk_jogo_decada FOREIGN KEY (decada_id) REFERENCES public.decada(id);


--
-- Name: jogo_categoria jogo_categoria_categoria_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.jogo_categoria
    ADD CONSTRAINT jogo_categoria_categoria_id_fkey FOREIGN KEY (categoria_id) REFERENCES public.categoria(id);


--
-- Name: jogo_categoria jogo_categoria_jogo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: game_erah
--

ALTER TABLE ONLY public.jogo_categoria
    ADD CONSTRAINT jogo_categoria_jogo_id_fkey FOREIGN KEY (jogo_id) REFERENCES public.jogo(id);


--
-- PostgreSQL database dump complete
--

\unrestrict 8p6osUiQKrpKCTLHvFvB64fynXThIQIqcdaGG9bggsKZwqim0nMIphJ99PWBSaN

