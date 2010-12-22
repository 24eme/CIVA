<h3 class="titre_section">Documents d'aide</h3>
<div class="contenu_section">
    <p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_doc_aide'); ?></p>
    <ul>
        <li><a href="<?php echo url_for('@telecharger_la_notice_gamma') ?>" class="pdf">Télécharger la notice Alsace Gamm@</a></li>
    </ul>
    <p class="intro pdf_link"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('telecharger_pdf_mon_espace'); ?></p>
</div>