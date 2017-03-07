<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">

    <?php include_partial('tiers/onglets', array('active' => 'vrac', 'compte' => $compte, 'blocs' => $blocs)) ?>

 	<div id="espace_alsace_contrats" class="contenu clearfix">

        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>

        <?php include_component('vrac', 'monEspace', array('compte' => $compte)) ?>
    </div>

</div>
