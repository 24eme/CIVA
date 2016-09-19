<div id="liste_contrats" class="listing">
	<h3 class="titre_section">Contrats de vente</h3>
	<div class="contenu_section">
		<?php include_partial('vrac/liste', array('limite' => 4, 'archive' => false, 'vracs' => $vracs, 'tiers' => $tiers)); ?>
		<ul id="actions_contrat">
			<?php if(VracSecurity::getInstance($compte, null)->isAuthorized(VracSecurity::CREATION)): ?>
			<li class="nouveau_contrat"><a href="<?php echo ($hasDoubt)? null : url_for('vrac_nouveau'); ?>"<?php if($hasDoubt): ?> id="choixType"<?php endif; ?>><img src="/images/boutons/btn_nouveau_contrat.png" alt="" /></a></li>
			<li><a href="<?php echo url_for('annuaire') ?>">Gérer son annuaire</a></li>
			<?php endif; ?>
			<li><a href="<?php echo url_for('vrac_historique', $compte) ?>">Voir tout</a></li>
		</ul>
	</div>
</div>
<div id="documents_aide">
	<h3 class="titre_section">Documents d'aide</h3>
	<div class="contenu_section">
		<p class="intro"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_doc_aide'); ?></p>
		<ul>
			<li><a href="<?php echo url_for('telecharger_la_notice_vrac') ?>" class="pdf">Télécharger la notice</a></li>
		</ul>
		<p class="intro pdf_link"><?php echo acCouchdbManager::getClient('Messages')->getMessage('telecharger_pdf_mon_espace'); ?></p>
	</div>
</div>
<?php include_partial('vrac/popupChoixType'); ?>
