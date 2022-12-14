<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_VALIDATION)); ?>

<!-- #principal -->
<form id="principal" action="" method="post">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#">Déclaration envoyée</a></li>
    </ul>

    <!-- #application_sv -->
    <div id="application_sv" class="clearfix">
        <div id="confirmation_fin_declaration">
            <h2 class="titre_section">Confirmation</h2>
            <div class="contenu_section">
                <div class="bloc_vert">
                    <p class="important">Votre Déclaration de Production a bien été enregistrée au CIVA.</p>
                    <p>Vous allez recevoir d'ici quelques instants un e-mail de confirmation avec en pièce jointe votre déclaration de Production au format PDF.</p>
                </div>
            </div>
        </div>
         <div id="confirmation_feed_back">
        <h2 class="titre_section">Votre avis</h2>
        <div class="contenu_section">
            <div class="bloc_vert">
                <p class="important">Votre retour d'expérience nous intéresse</p>
                <p>Laissez nous vos commentaires à propos de la saisie de la déclaration de Production.</p>
            </div>
            <div class="ligne_form ligne_btn">
                <a href="<?php echo url_for('sv_feed_back', $sv); ?>">
                    <img src="/images/boutons/btn_donnez_votre_avis.png" alt="Donnez votre avis" />
                </a>
            </div>
        </div>
        </div>
    </div>
    <!-- fin #application_sv -->

    <ul id="btn_etape" class="btn_prev_suiv clearfix">
      <li class="prec"><a href="<?php echo url_for('mon_espace_civa_production', $sv->etablissement) ?>"><img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à mon espace CIVA" name="boutons[previous]" /></a></li>
    </ul>

</form>
<!-- fin #principal -->

