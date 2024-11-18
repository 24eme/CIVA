<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance($sv->type), 'step' => SVEtapes::ETAPE_VALIDATION)); ?>

<!-- #principal -->
<form id="principal" action="" method="post">

    <h2 style="margin-bottom: 20px;">Déclaration envoyée</h2>

    <div class="row">
        <div class="col-xs-7">
          <div class="panel panel-default panel-success">
            <div class="panel-heading"><h3 class="panel-title">Confirmation</h3></div>
            <div class="panel-body" style="padding-bottom: 36px;">
              <p><strong>Votre Déclaration de Production a bien été enregistrée au CIVA.</strong></p>
              <p>Vous allez recevoir d'ici quelques instants un e-mail de confirmation avec en pièce jointe votre déclaration de Production au format PDF.</p>
            </div>
          </div>
        </div>

        <div class="col-xs-5">
            <div class="panel panel-default panel-default">
                <div class="panel-heading"><h3 class="panel-title">Votre avis</h3></div>
                <div class="panel-body">
                    <p>Votre retour d'expérience nous intéresse</p>
                    <p>Laissez nous vos commentaires à propos de la saisie de la déclaration de Production.</p>
                    <a href="<?php echo url_for('sv_feed_back', $sv); ?>">
                    <img src="/images/boutons/btn_donnez_votre_avis.png" alt="Donnez votre avis" /></a>
                </div>
            </div>
        </div>
    </div>

    <?php  if($sv->hasAutorisation(SVClient::AUTORISATION_AVA)): ?>
        <div class="row">
            <div class="col-xs-7">
                <div class="panel panel-default panel-default">
                    <div class="panel-heading"><h3 class="panel-title">Autorisation de transmission à l'AVA (ODG)</h3></div>
                        <div class="panel-body">
                            <p>Vous pourrez directement exploiter les données de votre Déclaration de Production en télédéclarant votre Déclaration de Revendication sur le <a style="text-decoration: underline;" target="_blank" rel="noopener noreferrer" href="<?php echo sfConfig::get('app_ava_url') ?>">portail de télédéclaration de l'Association des Viticulteurs d'Alsace</a></p>
                        </div>
                </div>
            </div>
        </div>
      <?php endif; ?>

    <div class="text-left">
      <a href="<?php echo url_for('mon_espace_civa_production', $sv->etablissement) ?>"><img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à mon espace CIVA" name="boutons[previous]" /></a>
    </div>

</form>
<!-- fin #principal -->
