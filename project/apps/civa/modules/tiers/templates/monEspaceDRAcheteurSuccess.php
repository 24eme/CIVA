<?php include_partial('tiers/onglets', array('active' => 'recolte_acheteur', 'compte' => $compte, 'blocs' => $blocs)) ?>

<div id="application_dr" class="mon_espace_civa clearfix">
    <?php include_partial('tiers/title') ?>

     <div id="espace_acheteurs" class="contenu clearfix">
 	    <?php if($sf_user->hasFlash('confirmation')) : ?>
        	<p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
		<?php endif; ?>

      	<?php include_component('dr_acheteur', 'monEspace', array('formUploadCsv' => $formUploadCsv, 'etablissement' => $etablissement)) ?>
    </div>
    <?php if (!$sf_user->isInDelegateMode() && $sf_user->hasCredential(myUser::CREDENTIAL_DELEGATION)): ?>
        <div class="contenu clearfix">
            <?php include_component('tiers', 'delegationForm', array('form' => isset($formDelegation) ? $formDelegation : null)) ?>
        </div>
    <?php endif;?>
</div>
