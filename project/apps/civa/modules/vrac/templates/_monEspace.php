<div id="espace_alsace_contrats">
    <h2>Alsace Contrats</h2>
    <div class="contenu clearfix">  
 		<div id="liste_contrats" class="listing">
 			<h3 class="titre_section">Contrats vrac <a doc="/vrac/telecharger_la_notice" title="Message aide" rel="help_popup_mon_espace_civa_contrats" class="msg_aide" href=""></a></h3>
 			<div class="contenu_section">
 				<?php include_partial('vrac/liste', array('limite' => 4, 'archive' => false, 'vracs' => $vracs, 'user' => $user)); ?>
 				<ul id="actions_contrat">
 					<?php if($user->type == 'Courtier' || $user->type == 'Acheteur'): ?>
 					<li class="nouveau_contrat"><a href="<?php echo url_for('@vrac_nouveau') ?>"><img src="/images/boutons/btn_nouveau_contrat.png" alt="" /></a></li>
 					<li><a href="<?php echo url_for('@annuaire') ?>">Gérer son annuaire</a></li>
 					<?php endif; ?>
 					<li><a href="<?php echo url_for('vrac_historique', array('campagne' => $campagne)) ?>">Voir tout</a></li>
 				</ul>
 			</div>
 		</div>
 		<div id="documents_aide">
			<h3 class="titre_section">Documents d'aide</h3>
			<div class="contenu_section">
				<p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_doc_aide'); ?></p>
				<ul>
					<li><a href="<?php echo url_for('@telecharger_la_notice') ?>" class="pdf"> Télécharger la notice</a></li>
				</ul>
				<p class="intro pdf_link"><?php echo acCouchdbManager::getClient('Messages')->getMessage('telecharger_pdf_mon_espace'); ?></p>
			</div>
		</div>
    </div>
</div>