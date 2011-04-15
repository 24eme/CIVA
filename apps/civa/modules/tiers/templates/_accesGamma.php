<form id="form_gamma" action="<?php echo url_for('@gamma') ?>" method="post">
    <h3 class="titre_section">Alsace Gamm@ <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_gamma" title="Message aide"></a></h3>
    <div class="contenu_section">
        <!--<p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_gamma'); ?></p>-->
<?php if (!$tiers->exist('gamma')) : ?>
        <div class="ligne_form">
            <input type="radio" id="gamma_type_acces_inscription" name="gamma[type_acces]" value="inscription" checked="checked" />
            <label for="gamma_type_acces_inscription">Inscription à Alsace Gamm@</label>
        </div>
<?php else : ?>
        <div class="ligne_form">
            <input type="radio" id="gamma_type_acces_inscription" name="gamma[type_acces]" value="plateforme" checked="checked"  />
            <label for="gamma_type_acces_inscription">Alsace Gamm@ (Plateforme réelle)</label>
        </div>
<?php endif; ?>
        <div class="ligne_form">
            <input type="radio" id="gamma_type_acces_plateforme_test" name="gamma[type_acces]" value="plateforme_test" />
            <label for="gamma_type_acces_plateforme_test">Plateforme de test</label>
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

<div id="popup_inscription_gamma" class="popup_inscription_gamma" title="Alsace Gamm@">
    <div id="popup_inscription_gamma_content">
        <p class="warning">Attention</p>
        <p class="intro">Vous allez vous inscrire sur la plateforme réelle Alsace Gamm@ du CIVA.</p>
        <p>Avez-vous déjà adhéré à Gamm@ sur Prodouane ?</p>
        <form id="form_gamma_inscription" action="<?php echo url_for('@gamma') ?>" method="post">
            <div class="ligne_form">
                <input type="radio" class="check_choix" id="gamma_inscription_choix_oui" name="gamma_inscription[choix]" value="1" checked="checked" />
                <label for="gamma_inscription_choix_oui">Oui</label>
                <span>Ouvrez votre compte sur Alsace Gamm@</span>
            </div>
            <div class="ligne_form">
                <input type="radio" class="check_choix" id="gamma_inscription_choix_non" name="gamma_inscription[choix]" value="0" />
                <label for="gamma_inscription_choix_non">Non</label>
                <span>Vous devez impérativement vous inscrire auprès de la Direction Régionale des Douanes de votre département à l'aide du document ci-joint.</span>
                <a href="<?php echo url_for('declaration/downloadAdhesionGamma'); ?>" class="telecharger-documentation-gamma" title="Document d'adhésion alsace gamm@"></a>
            </div>

           <div class="ligne_form ligne_btn">
                <input type="image" id="mon_espace_civa_gamma_valider" name="gamma_bouton" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
           </div>
        </form>
    </div>
</div>


