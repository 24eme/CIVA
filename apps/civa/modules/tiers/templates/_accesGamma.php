 <form id="principal" action="<?php echo url_for('@gamma') ?>" method="post">
    <h3 class="titre_section">Alsace Gamm@ <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_gamma" title="Message aide"></a></h3>
    <div class="contenu_section">
        <!--<p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_gamma'); ?></p>-->
        <div class="ligne_form">
            <input type="radio" id="gamma_type_acces_test" name="gamma[type_acces]" value="test" checked="checked" />
            <label for="type_declaration_brouillon">Plateforme de test</label>
        </div>
        <div class="ligne_form ligne_btn">
            <input type="image" name="gamma_bouton" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
        </div>
    </div>
</form>