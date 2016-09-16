<div id="documents_aide">
<h3 class="titre_section">Documents d'aide</h3>
    <div class="contenu_section">
        <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_doc_aide'); ?></p>
        <ul>
            <li><a href="<?php echo url_for('gamma_telecharger_la_notice') ?>" class="pdf">Télécharger la notice Alsace Gamm@</a></li>
            <li><a href="<?php echo url_for('gamma_telecharger_la_liste_nomenclature') ?>" class="pdf">Télécharger la liste des nomenclatures douanières</a></li>
            <li><a href="<?php echo url_for('gamma_telecharger_la_procedure_enlevement_propriete') ?>" class="pdf">Télécharger la procédure pour les enlèvements à la Propriété</a></li>


        </ul>
        <p class="intro pdf_link"><?php echo acCouchdbManager::getClient('Messages')->getMessage('telecharger_pdf_mon_espace'); ?></p>
    </div>
</div>
