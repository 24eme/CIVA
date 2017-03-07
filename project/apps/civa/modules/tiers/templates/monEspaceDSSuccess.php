<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">
    <?php include_partial('tiers/onglets', array('active' => 'stock_'.$type_ds, 'compte' => $compte, 'blocs' => $blocs)) ?>

    <div id="espace_alsace_recolte" class="contenu clearfix">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>
        <?php include_component('ds', 'monEspace', array('type_ds' => $type_ds, 'etablissement' => $etablissement)) ?>
        <?php include_component('ds', 'monEspaceColonne', array('type_ds' => $type_ds, 'etablissement' => $etablissement)) ?>
    </div>
</div>
