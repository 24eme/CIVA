<?php use_helper('Date') ?>
<div id="espace_alsace_contrats">
    <h2>Alsace Contrats</h2>
    <div class="contenu clearfix">  
 		<div id="liste_contrats" class="listing">
 			<h3 class="titre_section">Contrats vrac <a doc="/vrac/telecharger_la_notice" title="Message aide" rel="help_popup_mon_espace_civa_contrats" class="msg_aide" href=""></a></h3>
 			<div class="contenu_section">
 				<table cellspacing="0" cellpadding="0" class="table_listing">
					<thead>
						<tr>
							<th><span>Date</span></th>
							<th><span>Soussignés</span></th>
							<th><span>Statut</span></th>
							<th class="actions"><span>Actions</span></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$counter = 0;
							foreach ($vracs as $item) :
								$item = $item->value;
								if ($item->statut == Vrac::STATUT_CLOTURE) {
									continue;
								}
								$alt = ($counter%2);
								$validation = null;
						?>
						<tr<?php if($alt): ?> class="alt"<?php endif; ?>>
							<td><?php echo format_date($item->date, 'p', 'fr'); ?></td>
							<td>
								<ul>
									<?php 
										if ($item->soussignes->vendeur->identifiant): 
											if ($item->soussignes->vendeur->identifiant == $user->_id) {
												if (!$item->soussignes->vendeur->date_validation) {
													$validation = 'validation';
												}
											}
									?>
									<li>Vendeur : <strong><?php echo $item->soussignes->vendeur->raison_sociale; ?></strong><?php if ($item->soussignes->vendeur->date_validation): ?> V<?php endif; ?></li>
									<?php endif; ?>
									<?php 
										if ($item->soussignes->acheteur->identifiant):
											if ($item->soussignes->acheteur->identifiant == $user->_id) {
												if (!$item->soussignes->acheteur->date_validation) {
													$validation = 'validation';
												}
											}
									 ?>
									<li>Acheteur : <strong><?php echo $item->soussignes->acheteur->raison_sociale; ?></strong><?php if ($item->soussignes->acheteur->date_validation): ?> V<?php endif; ?></li>
									<?php endif; ?>
									<?php 
										if ($item->soussignes->mandataire->identifiant): 
											if ($item->soussignes->mandataire->identifiant == $user->_id) {
												if (!$item->soussignes->mandataire->date_validation) {
													$validation = 'validation';
												}
											}
									?>
									<li>Courtier : <strong><?php echo $item->soussignes->mandataire->raison_sociale; ?></strong><?php if ($item->soussignes->mandataire->date_validation): ?> V<?php endif; ?></li>
									<?php endif; ?>
								</ul>
							</td>
							<td><?php echo VracClient::getInstance()->getStatutLibelle($item->statut) ?></td>
							<td class="actions">
								<?php if ($item->statut == Vrac::STATUT_CREE): ?>
								<a href="<?php echo url_for('vrac_etape', array('numero_contrat' => $item->numero, 'etape' => $item->etape)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire) ?></a> 
								<?php 
									else:
								?>
								<a href="<?php echo url_for('vrac_fiche', array('numero_contrat' => $item->numero, 'validation' => $validation)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire) ?></a>
								<?php endif; ?>
								| <a href="<?php echo url_for('vrac_supprimer', array('numero_contrat' => $item->numero)) ?>">X</a></td>
						</tr>
							<?php
								$counter++;
							endforeach;
							?>
					</tbody>
				</table>
 				<p class="lien_tout"><a href="#">Tout voir</a></p>
 				<p class="lien_nouveau"><a href="<?php echo url_for('vrac_etape', array('sf_subject' => new Vrac(), 'etape' => $etapes->getFirst())) ?>">Créer un nouveau contrat</a> | <a href="<?php echo url_for('@annuaire') ?>">Annuaire</a></p>
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