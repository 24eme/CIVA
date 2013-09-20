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
								if ($item->valide->statut == Vrac::STATUT_CLOTURE) {
									continue;
								}
								$alt = ($counter%2);
						?>
						<tr<?php if($alt): ?> class="alt"<?php endif; ?>>
							<td><?php echo ($item->valide->date_validation)? format_date($item->valide->date_validation, 'p', 'fr') : format_date($item->valide->date_saisie, 'p', 'fr'); ?></td>
							<td>
								<ul>
									<?php if ($item->vendeur_identifiant): ?>
									<li>Vendeur : <strong><?php echo $item->vendeur->raison_sociale; ?></strong></li>
									<?php endif; ?>
									<?php if ($item->acheteur_identifiant): ?>
									<li>Acheteur : <strong><?php echo $item->acheteur->raison_sociale; ?></strong></li>
									<?php endif; ?>
									<?php if ($item->mandataire_identifiant): ?>
									<li>Courtier : <strong><?php echo $item->mandataire->raison_sociale; ?></strong></li>
									<?php endif; ?>
								</ul>
							</td>
							<td><?php echo VracClient::getInstance()->getStatutLibelle($item->valide->statut) ?></td>
							<td class="actions"><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $item, 'etape' => $item->etape)) ?>">Accéder</a> | <a href="<?php echo url_for('vrac_supprimer', $item) ?>">X</a></td>
						</tr>
							<?php
								$counter++;
							endforeach;
							?>
					</tbody>
				</table>
 				<p class="lien_tout"><a href="#">Tout voir</a></p>
 				<p class="lien_nouveau"><a href="<?php echo url_for('@vrac_nouveau') ?>">Créer un nouveau contrat</a></p>
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