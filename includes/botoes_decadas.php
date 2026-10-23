<?php


?>
<nav class="decadas-botoes" aria-label="Escolha uma década">
    <?php foreach (decadas_site() as $ano => $rotulo): ?>
<!--O que faz: O PHP chama a função decadas_site(), que retorna uma lista (array) com as décadas cadastradas. O comando foreach passa por cada item dessa lista, separando-os em duas variáveis:
$ano: O identificador numérico (ex: 1980).
$rotulo: O texto amigável que aparece para o usuário (ex: "Anos 80").-->
        <a class="btn-decada d-<?= (int) $ano ?>" href="<?= BASE_URL ?>/decada.php?d=<?= (int) $ano ?>">
    <!--class="btn-decada d-: Cria uma classe CSS genérica (btn-decada) e uma classe específica (ex: d-1980 garantindo que seja um número inteiro seguro com (int)), permitindo que você estilize cada década com uma cor ou detalhe diferente, se quiser.
href=" BASE_URL/decada.php?d=": Cria o link de redirecionamento. 
Quando o usuário clica, ele é levado para a página decada.php levando na URL o parâmetro d com o ano correspondente 
(ex: /decada.php?d=1980), informando ao backend qual década deve ser exibida.-->
        
        
        
        <!--Exibe o texto do rótulo (como "Anos 80") dentro do botão,-->
        <?= esc_html($rotulo) ?>
        </a>
    <?php endforeach; ?>
</nav>
