<h3 class="titre_section">Aucun lieux de stockage enregistré <a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds<?php if($type_ds == DSCivaClient::TYPE_DS_NEGOCE): ?>_negoce<?php endif; ?>" title="Message aide"></a></h3> <span class="label_type_ds"><?php echo strtoupper($type_ds); ?></span>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_no_lieux_de_stockage'); ?></p>
</div>