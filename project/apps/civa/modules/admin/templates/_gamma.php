<form id="form_gamma" action="<?php echo url_for('@admin_gamma') ?>" method="post">
    <h3 class="titre_section">Alsace Gamm@ <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_gamma" title="Message aide"></a></h3>
    <div class="contenu_section">
        <!--<p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_gamma'); ?></p>-->
        <div class="ligne_form">
            <input type="radio" id="gamma_type_acces_prod" name="gamma_type_acces" value="prod" checked="checked" />
            <label for="gamma_type_acces_prod">Administrateur Alsace Gamm@</label>
        </div>
        <div class="ligne_form">
            <input type="radio" id="gamma_type_acces_test" name="gamma_type_acces" value="test" />
            <label for="gamma_type_acces_test">Administrateur Plateforme de test</label>
        </div>
        <div class="ligne_form ligne_btn">
            <input type="image" id="mon_espace_civa_gamma_valider" name="gamma_bouton" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
        </div>
    </div>
</form>
<div style="display: none;" id="popup_loader" title="Ouverture d'Alsace Gamm@">
    <div class="popup-loading" style="background: none; padding-top: 20px;">
        <img src="/css/jquery.ui/images/ui-anim_basic_16x16.gif" style="padding-bottom: 9px;" />
        <p>L'ouverture de votre compte Alsace Gamm@ est en cours.<br />Merci de patienter.<br /><small>A la première ouverture, la procédure peut prendre du temps.</small></p>
    </div>
</div>