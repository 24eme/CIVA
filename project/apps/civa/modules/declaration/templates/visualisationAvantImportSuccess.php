<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Prévisualisation de récolte de <?php echo $annee; ?></a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        
        <div id="validation_dr">
            <div class="intro_declaration">
                <?php include_partial('declaration/importMessage', array('acheteurs' => $acheteurs)) ?>
                <br />
            </div>
            <!-- #acheteurs_caves -->
                <?php include_component('declaration', 'recapDeclaration', array('dr' => $dr)) ?>
            <!-- fin #acheteurs_caves -->
        </div>
    </div>
    <!-- fin #application_dr -->
    <?php include_partial('global/boutons', array('display' => array('retour', 'previsualiser'))) ?>

</form>
<!-- fin #principal -->
<?php include_partial('generationDuPdf', array('annee' => $annee, 'from_csv' => true)) ?>
<?php //include_partial('envoiMailDR', array('annee' => $annee)) ?>