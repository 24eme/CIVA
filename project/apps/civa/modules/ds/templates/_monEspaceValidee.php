<?php use_helper('ds'); ?>
<h3 class="titre_section">Déclaration de <?php echo getPeriodeFr($sf_user->getPeriodeDS($type_ds)) ?><a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds<?php if($ds->type_ds == DSCivaClient::TYPE_DS_NEGOCE): ?>_negoce<?php endif; ?>" title="Message aide"></a></h3> <span class="label_type_ds"><?php echo strtoupper($ds->type_ds); ?></span>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_ds_validee'); ?></p>
    <div class="ligne_form ligne_btn">
        <?php echo link_to('<img src="/images/boutons/btn_visualiser.png" alt="" class="btn" />', 'ds_visualisation',$ds); ?>
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <a href="<?php echo url_for('ds_invalider_civa',$ds) ?>" onclick="return confirm('Si vous éditez cette DS, pensez à la revalider.')"><img src="/images/boutons/btn_editer_ds.png" alt="" class="btn" id="rendreEditable"  /></a>
            <?php if($ds->isDecembre()): //if (CurrentClient::getCurrent()->exist('ds_non_editable') && CurrentClient::getCurrent()->ds_non_editable == 0): ?>
                <a href="<?php echo url_for('ds_invalider_recoltant',$ds) ?>" onclick="return confirm('Etes-vous sûr de vouloir dévalider cette DS ?')"><img src="/images/boutons/btn_devalider_ds.png" alt="" class="btn" id=""  /></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
