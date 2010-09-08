

<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Déclaration de récolte de <?php echo $annee; ?></a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="validation_dr">
        <p class="intro_declaration"></p>
        <!-- #acheteurs_caves -->
        <?php include_component('declaration', 'recapDeclaration') ?>
        <!-- fin #acheteurs_caves -->
        </div>
    </div>
    <!-- fin #application_dr -->
    <?php include_partial('global/boutons', array('display' => array('retour','previsualiser'))) ?>

</form>
<!-- fin #principal -->
<script>
    ajax_url_to_print = "<?php echo url_for('@print?annee='.$annee); ?>?ajax=1";
</script>
<div style="display: none" id="popup_loader" title="Génération du PDF">
    <div class="popup-loading">
    <p>La génération de votre PDF est en cours.<br/>Merci de patienter.<br/><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>