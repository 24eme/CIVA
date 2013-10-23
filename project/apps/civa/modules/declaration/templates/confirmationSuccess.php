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
                <div id="div-btn-email">
                <a href="" id="btn-email-acheteur"></a>
                </div>
            </div>
        </div>
         <div id="confirmation_feed_back">
        <h2 class="titre_section">Votre avis</h2>
        <div class="contenu_section">
            <div class="bloc_vert">
                <p class="important">Votre retour d'expérience nous intéresse</p>
                <p>Laisser nous votre commentaires à propos de la saisie de la déclaration de Stocks</p>
            </div>
            <div class="ligne_form ligne_btn">
                <a href="<?php echo url_for('recolte_feed_back'); ?>">
                    <img src="/images/boutons/btn_donnez_votre_avis.png" alt="Donnez votre avis" />
                </a>
            </div>
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