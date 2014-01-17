<h3 class="titre_section">Déclaration de l'année <a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds" title="Message aide"></a></h3>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_ds_non_editable'); ?></p>
    <div class="ligne_form ligne_btn">
        <?php if($ds && $ds->isValideeTiers()): ?>
            <?php echo link_to('<img src="/images/boutons/btn_visualiser.png" alt="" class="btn" />', 'ds_visualisation',$ds); ?>
        <?php endif; ?>
    </div>
</div>