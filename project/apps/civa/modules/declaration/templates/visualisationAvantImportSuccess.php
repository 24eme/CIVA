<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Prévisualisation de récolte de <?php echo $annee; ?></a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="validation_dr">
            <p class="intro_declaration"></p>
            <!-- #acheteurs_caves -->
                <?php include_component('declaration', 'recapDeclaration', array('visualisation_avant_import' => $visualisation_avant_import  )) ?>
            <!-- fin #acheteurs_caves -->
        </div>
    </div>
    <!-- fin #application_dr -->
    <?php include_partial('global/boutons', array('display' => array('retour'))) ?>

</form>
<!-- fin #principal -->
<?php //§include_partial('generationDuPdf', array('annee' => $annee)) ?>
<?php //include_partial('envoiMailDR', array('annee' => $annee)) ?>