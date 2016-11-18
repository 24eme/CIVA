<?php include_partial('dr/etapes', array('etape' => 5, 'dr' => $dr)) ?>
<?php include_partial('dr/actions', array('etape' => 0)) ?>

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
                    <p class="important">Votre Déclaration de Récolte a bien été enregistrée au CIVA.</p>
                    <p>Vous allez recevoir d'ici quelques minutes un e-mail de confirmation avec en pièce jointe votre déclaration de Récolte au format PDF et au format Tableur.</p>
                </div>
                <?php if($dr->hasAutorisation(DRClient::AUTORISATION_ACHETEURS)): ?>
                <p>&nbsp;</p>
                <div class="bloc_vert">
                    <p class="important">Votre Déclaration de Récolte a été envoyé par email aux acheteurs :</p>
                    <ul>
                        <?php foreach(DRClient::getInstance()->getAcheteursApporteur($dr->cvi, $dr->campagne) as $acheteur): ?>
                        <li>- <?php echo $acheteur->qualite; ?>, <?php echo $acheteur->nom ?>, <?php echo $acheteur->cvi; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <?php if($dr->hasAutorisation(DRClient::AUTORISATION_AVA)): ?>
                <p>&nbsp;</p>
                <div class="bloc_vert">
                    <p class="important">Autorisation de transmission à l'AVA (ODG) :</p>
                    <p>
                        Vous pourrez directement exploiter les données de votre Déclaration de Récolte en télédéclarant votre Déclaration de Revendication sur le <a style="text-decoration: underline;" target="_blank" href="<?php echo sfConfig::get('app_ava_url') ?>">portail de télédéclaration de l'Association des Viticulteurs d'Alsace</a>.
                    </p>
                </div>
                <?php endif; ?>
                <?php if($has_import && !$dr->hasAutorisation(DRClient::AUTORISATION_ACHETEURS)): ?>
                <div id="div-btn-email">
                <a href="" title="Envoyer à mes acheteurs" alt="Envoyer à mes acheteurs" id="btn-email-acheteur"></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
         <div id="confirmation_feed_back">
        <h2 class="titre_section">Votre avis</h2>
        <div class="contenu_section">
            <div class="bloc_vert">
                <p class="important">Votre retour d'expérience nous intéresse</p>
                <p>Laissez nous vos commentaires à propos de la saisie de la déclaration de Récolte</p>
            </div>
            <div class="ligne_form ligne_btn">
                <a href="<?php echo url_for('dr_feed_back', $dr); ?>">
                    <img src="/images/boutons/btn_donnez_votre_avis.png" alt="Donnez votre avis" />
                </a>
            </div>
        </div>
        </div>
    </div>
    <!-- fin #application_dr -->

    <?php include_partial('dr/boutons', array('display' => array('retour','previsualiser'), 'dr' => $dr, 'etablissement' => $dr->getEtablissement())) ?>

</form>
<!-- fin #principal -->

<?php include_partial('dr/generationDuPdf', array('annee' => $annee, 'etablissement' => $dr->getEtablissement())) ?>
<?php include_partial('envoiMailDRAcheteurs', array('annee' => $annee, 'dr' => $dr)) ?>
<?php //include_partial('envoiMailDR', array('annee' => $annee)) ?>
