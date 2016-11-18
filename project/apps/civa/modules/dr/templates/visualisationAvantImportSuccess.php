<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Prévisualisation de récolte de <?php echo $campagne; ?></a></li>
    </ul>

    <div id="application_dr" class="clearfix">

        <div id="validation_dr">
            <div class="intro_declaration">
                <?php include_partial('dr/importMessage', array('acheteurs' => $acheteurs)) ?>
                <br />
            </div>
            <?php include_component('dr', 'recapDeclaration', array('dr' => $dr)) ?>
        </div>
    </div>
    <?php include_partial('dr/boutons', array('display' => array('retour','previsualiser'), 'etablissement' => $dr->getEtablissement(), array('dr' => $dr))) ?>

</form>

<?php include_partial('dr/generationDuPdf', array('annee' => $campagne, 'etablissement' => $etablissement, 'from_csv' => true)) ?>
