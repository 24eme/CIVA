<?php include_partial('tiers/onglets', array('active' => 'production', 'compte' => $compte, 'blocs' => $blocs)) ?>

<div id="application_dr" class="mon_espace_civa clearfix">
    <?php include_partial('tiers/title') ?>

     <div id="espace_acheteurs" class="contenu clearfix">
 	    <?php if($sf_user->hasFlash('confirmation')) : ?>
        	<p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
		<?php endif; ?>
        <div id="nouvelle_declaration">
          <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
          <div class="contenu_section">
              <p class="intro">Vous souhaitez :</p>
              <form action="<?= url_for('sv_etablissement', ['identifiant' => $etablissement->identifiant]) ?>" method="POST" enctype="multipart/form-data">
              <?php echo $formCreation->renderHiddenFields() ?>
              <?php echo $formCreation->renderGlobalErrors() ?>
              <div class="form_ligne">
                  <?php echo $formCreation['file']->renderError() ?>
                  <?php echo $formCreation['file']->renderLabel() ?>
                  <?php echo $formCreation['file']->render() ?>
              </div>
                <div class="ligne_form">
                  <?php echo $formCreation['type_creation']->renderError() ?>
                  <?php echo $formCreation['type_creation']->render() ?>
                </div>
                <div class="ligne_form ligne_btn">
                   <button type="submit" id="mon_espace_civa_valider" class="btn">
                      <img src="/images/boutons/btn_valider.png" alt="Valider" />
                   </button>
                </div>
              </form>
          </div>
        </div>
    </div>
    <?php if (!$sf_user->isInDelegateMode() && $sf_user->hasCredential(myUser::CREDENTIAL_DELEGATION)): ?>
        <div class="contenu clearfix">
            <?php include_component('tiers', 'delegationForm', array('form' => isset($formDelegation) ? $formDelegation : null)) ?>
        </div>
    <?php endif;?>
</div>
