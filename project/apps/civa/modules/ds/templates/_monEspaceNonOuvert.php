<?php use_helper('ds'); ?>
<h3 class="titre_section">Déclaration de <?php echo getPeriodeFr($sf_user->getPeriodeDS()) ?><a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds" title="Message aide"></a></h3>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_ds_non_ouverte'); ?></p>
</div>