<!-- #principal -->
<?php //include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds_principale, 'etape' => 1)); ?>
<section id="principal" class="ds">
    <!-- #contenu_etape -->
    <section id="contenu_etape">
        <form action="<?php echo url_for("ds", array('cvi' => '7523700100')); ?>" method="POST" >
        
            <button type="submit">Créer ou continuer la DS</button>
        </form>
        <?php //include_component('ds', 'chooseEtablissement'); ?>
        <?php //include_partial('historiqueDsGeneration', array('generations' => $generations)); ?>


        <?php //include_partial('generation', array('generationForm' => $generationForm, 'type' => 'ds')); ?>
    </section>
    <!-- fin #contenu_etape -->
</section>
<!-- fin #principal -->

