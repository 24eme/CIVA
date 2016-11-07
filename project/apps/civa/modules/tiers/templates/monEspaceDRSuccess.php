<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">

    <?php include_partial('tiers/onglets', array('active' => 'recolte', 'compte' => $compte, 'blocs' => $blocs)) ?>

    <div id="espace_alsace_recolte" class="contenu clearfix">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>

        <?php include_partial('dr/monEspace', array('etablissement' => $etablissement, 'dr' => $dr, 'campagne' => $campagne)) ?>
        <?php include_component('dr', 'monEspaceColonne',array('etablissement' => $etablissement)) ?>
    </div>


</div>
