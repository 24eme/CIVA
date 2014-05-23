<h3 class="titre_section">Déclaration de l'année <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
<div class="contenu_section">
    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_dr_validee'); ?></p>
    <div class="ligne_form ligne_btn">
        <?php echo link_to('<img src="/images/boutons/btn_visualiser.png" alt="" class="btn" />', '@visualisation?annee=' . $sf_user->getCampagne()); ?>
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <a href="<?php echo url_for('@declaration_invalider_civa') ?>" onclick="return confirm('Si vous éditez cette DR, pensez à la revalider.')"><img src="/images/boutons/btn_editer_dr.png" alt="" class="btn" id="rendreEditable"  /></a>
            <?php if (CurrentClient::getCurrent()->exist('dr_non_editable') && CurrentClient::getCurrent()->dr_non_editable == 0): ?>   
                <a href="<?php echo url_for('@declaration_invalider_recoltant') ?>" onclick="return confirm('Etes-vous sûr de vouloir dévalider cette DR ?')"><img src="/images/boutons/btn_devalider_dr.png" alt="" class="btn" id=""  /></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>