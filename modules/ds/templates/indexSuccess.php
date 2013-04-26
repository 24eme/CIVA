<!-- #principal -->
<section id="principal" class="ds">
    <p id="fil_ariane"><strong>Page d'accueil</strong></p>

    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <form action="<?php echo url_for("ds", array('cvi' => '7523700100')); ?>" method="POST" >
        
            <button type="submit">VALIDER</button>
        </form>
        <?php //include_component('ds', 'chooseEtablissement'); ?>
        <?php //include_partial('historiqueDsGeneration', array('generations' => $generations)); ?>


        <?php //include_partial('generation', array('generationForm' => $generationForm, 'type' => 'ds')); ?>
    </section>
    <!-- fin #contenu_etape -->
</section>
<!-- fin #principal -->

