
    <h2 class="titre_principal">Administration</h2>
    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <form action="<?php echo url_for('@login_admin') ?>" method="post" id="principal">
            <h3 class="titre_section">Connexion</h3>
            <div class="contenu_section">
                <p class="intro">Pour vous connecter, merci d'indiquer un numéro de CVI :</p>
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['cvi']->renderError() ?>
                    <?php echo $form['cvi']->renderLabel() ?>
                    <?php echo $form['cvi']->render() ?>
                </div>
                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </div>
            </form>
            <br />
            <form id="form_gamma" action="<?php echo url_for('@gamma-admin') ?>" method="post">
            <h3 class="titre_section">Alsace Gamm@ <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_gamma" title="Message aide"></a></h3>
            <div class="contenu_section">
                <!--<p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_gamma'); ?></p>-->
                <div class="ligne_form">
                    <input type="radio" id="gamma_type_acces_test" name="gamma_type_acces" value="prod" checked="checked" />
                    <label for="type_declaration_brouillon">Administrateur Alsace Gamm@</label>
                </div>
                <div class="ligne_form">
                    <input type="radio" id="gamma_type_acces_test" name="gamma_type_acces" value="test" />
                    <label for="type_declaration_brouillon">Administrateur Plateforme de test</label>
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
        </div>
        <div id="precedentes_declarations">
            <h3 class="titre_section">Export CSV des Tiers</h3>
            <div class="contenu_section">
                <ul>
                    <li><a href="<?php echo url_for('@csv_tiers') ?>">Tous</a></li>
                    <li><a href="<?php echo url_for('@csv_tiers_dr_en_cours') ?>">Déclaration en cours</a></li>
                    <li><a href="<?php echo url_for('@csv_tiers_non_validee_civa') ?>">Non validée par le CIVA</a></li>
                    <!--<li><a href="<?php echo url_for('@csv_tiers_modifications') ?>">Modifications</a></li>-->
                    <li><a href="<?php echo url_for('@csv_tiers_modifications_email') ?>">Email Modifications</a></li>
                </ul>
            </div>
            <br />
            <h3 class="titre_section">Statistiques</h3>
            <div class="contenu_section">
                <ul>
                    <li><a href="<?php echo url_for('@statistiques') ?>">Statistiques</a></li>
                </ul>
            </div>
        </div>
    </div>

