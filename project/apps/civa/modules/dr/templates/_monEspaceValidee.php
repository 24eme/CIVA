<h3 class="titre_section">Déclaration de l'année <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_dr_validee'); ?></p>
    <div class="ligne_form ligne_btn">
        <?php echo link_to('<img src="/images/boutons/btn_visualiser.png" alt="" class="btn" />', 'dr_visualisation', array('id' => $dr->_id, 'annee' => $sf_user->getCampagne())); ?>
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <a href="<?php echo url_for('dr_invalider_civa', $dr) ?>" onclick="return confirm('Si vous éditez cette DR, pensez à la revalider.')"><img src="/images/boutons/btn_editer_dr.png" alt="" class="btn" id="rendreEditable"  /></a>
            <?php if (DRClient::getInstance()->isTeledeclarationOuverte()): ?>
                <a href="<?php echo url_for('dr_invalider_recoltant', $dr) ?>" onclick="return confirm('Etes-vous sûr de vouloir dévalider cette DR ?')"><img src="/images/boutons/btn_devalider_dr.png" alt="" class="btn" id=""  /></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
