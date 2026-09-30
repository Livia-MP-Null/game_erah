<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <title>game.erah - Gestão de Usuários</title>

    <!-- Um CSS para cada parte da página -->
    <link rel="stylesheet" href="/game_erah/style/base.css">
    <link rel="stylesheet" href="/game_erah/style/header.css">
    <link rel="stylesheet" href="/game_erah/style/hero.css">
    <link rel="stylesheet" href="/game_erah/style/objetivo.css">
    <link rel="stylesheet" href="/game_erah/style/pilares.css">
    <link rel="stylesheet" href="/game_erah/style/sobre.css">
    <link rel="stylesheet" href="/game_erah/style/galeria.css">
    <link rel="stylesheet" href="/game_erah/style/footer.css">
</head>

<body>

    <!-- HEADER -->
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>

        <!-- 1) HERO (imagem de fundo + título) -->
        <section class="hero">

            <div class="hero-conteudo">

                <h1>Os <span>melhores jogos</span><br>de cada década</h1>

                <p>
                    game.erah é um site que vai mostrar de forma divertida
                    a evolução dos jogos com o passar do tempo.
                </p>

                <p class="boas-vindas">
                    Seja bem vindo! Utilize o menu acima para navegar pelo site.
                </p>

            </div>

        </section>

        <!-- 2) OBJETIVO -->
        <section class="objetivo">
            <h2>Nosso objetivo:</h2>
            <p>
                "Criar uma plataforma web interativa e responsiva que resgate a história dos videogames,
                permitindo que entusiastas e novos jogadores compreendam a evolução da indústria através de
                uma linha do tempo cronológica que destaca os marcos em jogabilidade, gráficos e popularidade
                de cada década."
            </p>
        </section>

        <!-- 3) PILARES -->
        <section class="pilares">
            <h2>Três grandes pilares da evolução tecnológica e cultural dos jogos.</h2>

            <div class="pilares-lista">
                <div class="card card-vermelho">
                    <div class="icone">&#128196;</div>
                    <h3>Evolução da Jogabilidade</h3>
                    <p>Descubra como os jogos deixaram os comandos simples de apenas um botão para trás e se transformaram em mecânicas complexas, mundos abertos e físicas realistas.</p>
                </div>

                <div class="card card-laranja">
                    <div class="icone">&#128197;</div>
                    <h3>Evolução Gráfica</h3>
                    <p>Acompanhe o salto visual dos jogos: a jornada fascinante desde os primeiros pixels em telas pretas e brancas até o fotorrealismo impressionante com Ray Tracing.</p>
                </div>

                <div class="card card-amarelo">
                    <div class="icone">&#128101;</div>
                    <h3>Os Mais Jogados</h3>
                    <p>Explore os maiores fenômenos de popularidade de cada época. Conheça os títulos que definiram gerações e arrastaram multidões de jogadores ao redor do mundo.</p>
                </div>
            </div>
        </section>

        <!-- 4) O QUE É -->
        <section class="sobre">
            <h2>O que é game.erah</h2>
            <p>
                Programa feito em PHP para mostrar a evolução dos jogos. Ele foi criado para trazer um
                conhecimento de evolução da tecnologia em games, trazendo uma tela arrumada, fácil e simples
                para os visitantes.
            </p>
        </section>

        <!-- 5) GALERIA (faixa de imagens) -->
        <section class="galeria">
            <div class="galeria-faixa">
                <img src="/game_erah/img/assasine.webp" alt="Assassin's creed">
                <img src="/game_erah/img/crash.webp" alt="Crash">
                <img src="/game_erah/img/gta.webp" alt="Gta">
                <img src="/game_erah/img/mario.webp" alt="Mario">
                <img src="/game_erah/img/minecraft.webp" alt="Minecraft">
                <img src="/game_erah/img/mortalcombat.webp" alt="Mortal Combat">
                <img src="/game_erah/img/rayman.webp" alt="Rayman">
                <img src="/game_erah/img/reddead.webp" alt="Red dead">
            </div>
            <a class="galeria-botao" href="/game_erah/app/select.php">Explore o mundo dos games</a>
        </section>

    </main>

    <!-- FOOTER -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>

</html>