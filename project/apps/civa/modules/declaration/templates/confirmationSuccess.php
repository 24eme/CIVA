<?php include_partial('global/etapes', array('etape' => 5)) ?>
<?php include_partial('global/actions', array('etape' => 0)) ?>

<!-- #principal -->
<form id="principal" action="" method="post">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#">Déclaration envoyée</a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="confirmation_fin_declaration">
            <h2 class="titre_section">Confirmation</h2>
            <div class="contenu_section">
                <div class="bloc_vert">
                    <p class="important">Votre déclaration de récoltes a bien été enregistrée au CIVA.</p>
                    <p>Vous pouvez retrouver tous les éléments renseignés dans votre espace CIVA.</p>
                </div>
                <div id="div-btn-email"><a href="" id="btn-email-acheteur"></a></div>
            </div>
        </div>
    </div>
    <!-- fin #application_dr -->

    <?php include_partial('global/boutons', array('display' => array('retour','previsualiser'))) ?>

</form>
<!-- fin #principal -->

<?php include_partial('generationDuPdf', array('annee' => $annee)) ?>
<?php include_partial('envoiMailDRAcheteurs', array('annee' => $annee)) ?>
<?php //include_partial('envoiMailDR', array('annee' => $annee)) ?>