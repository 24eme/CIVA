<?php use_helper('ds'); ?>
<h3 class="titre_section">Déclaration de <?php echo getPeriodeFr($sf_user->getPeriodeDS()) ?><a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds<?php if($type_ds == DSCivaClient::TYPE_DS_NEGOCE): ?>_negoce<?php endif; ?>" title="Message aide"></a></h3> <span class="label_type_ds"><?php echo strtoupper($type_ds); ?></span>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_ds_non_editable'); ?></p>
    <div class="ligne_form ligne_btn">
        <?php if($ds && $ds->isValideeTiers()): ?>
            <?php echo link_to('<img src="/images/boutons/btn_visualiser.png" alt="" class="btn" />', 'ds_visualisation',$ds); ?>
        <?php endif; ?>
    </div>
</div>
