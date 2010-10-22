<h2 class="titre_principal">Mon espace CIVA</h2>

<div id="application_dr" class="clearfix">
    <div id="nouvelle_declaration">
        <?php if($has_no_assices): ?>
        <h3 class="titre_section">Gamma <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_gamma" title="Message aide"></a></h3>
        <div class="contenu_section">
            <p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_gamma'); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>