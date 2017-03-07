<div id="precedentes_declarations">
    <h3 class="titre_section">Visualiser mes DRécolte <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_visualiser" title="Message aide"></a></h3>
    <div class="contenu_section">
        <ul class="bloc_vert">
            <li>
                <a href="#">Années précédentes</a>
                <?php if (count($campagnes) > 0): ?>
                    <ul class="declarations">
                        <?php foreach ($campagnes as $id => $campagne): ?>
                            <li><?php echo link_to($campagne, 'dr_visualisation', array('id' => $id)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</div>
<div id="documents_aide">
	<h3 class="titre_section">Documents d'aide</h3>
	<div class="contenu_section">
	    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_doc_aide'); ?></p>
	    <ul>
	        <li><a href="<?php echo url_for('dr_telecharger_la_notice') ?>" class="pdf"> Télécharger la notice</a></li>
	    </ul>
	    <p class="intro pdf_link"><?php echo acCouchdbManager::getClient('Messages')->getMessage('telecharger_pdf_mon_espace'); ?></p>
	</div>
</div>
