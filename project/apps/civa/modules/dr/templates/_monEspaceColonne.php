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
	    <ul>
	        <li><a href="<?php echo url_for('dr_telecharger_la_notice') ?>" class="pdf">Télécharger&nbsp;la&nbsp;notice&nbsp;Récolte</a></li>
            <li><a href="/drm/doc/docs/Fonctionnement_VCI_2018_2019.pdf" class="pdf" download="download">Fonctionnement VCI 2018-2019</a></li>
	        <li><a href="<?php echo url_for('dr_telecharger_guide_vci') ?>" class="pdf">Télécharger le guide du VCI</a></li>
	    </ul>
	    <p class="intro pdf_link">Ces notices sont au format PDF. Pour les visualiser, veuillez utiliser un <a href='http://pdfreaders.org/'>lecteur PDF</a>.</p>
	</div>
</div>
