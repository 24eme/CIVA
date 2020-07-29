<?php use_helper('ds'); ?>
<div id="precedentes_declarations">
    <h3 class="titre_section">Visualiser mes DStocks<a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_visualiser_ds" title="Message aide"></a></h3>
    <div class="contenu_section">
        <ul class="bloc_vert">
            <li>
                <a href="#">Années précédentes</a>
                <?php if (count($dsByperiodes) > 0): ?>
                    <ul class="declarations">
                        <?php foreach($dsByperiodes as $periode => $ds): ?>
                            <li><?php echo link_to(getPeriodeFr($ds->getPeriode()), 'ds_visualisation', $ds); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else :
			echo "<p style='margin-top: 3px;'><i>Pas d'historique disponible</i></p>";
		endif; ?>
            </li>
        </ul>
    </div>
</div>
<div id="documents_aide">
	<h3 class="titre_section">Documents d'aide</h3>
	<div class="contenu_section">
	    <p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_doc_aide'); ?></p>
	    <ul>
	        <li><a href="<?php echo url_for('telecharger_la_notice_ds', array('type' => $type_ds)) ?>" class="pdf"> Télécharger la notice</a></li>
	    </ul>
	    <!--<p class="intro pdf_link"><?php echo acCouchdbManager::getClient('Messages')->getMessage('telecharger_pdf_mon_espace'); ?></p>
            <ul>
	        <li><a href="<?php echo url_for('telecharger_la_dai') ?>" class="pdf"> Télécharger la DAI</a></li>-->
	    </ul>
	</div>
</div>
