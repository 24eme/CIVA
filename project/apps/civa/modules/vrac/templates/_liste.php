<?php use_helper('Date') ?>
<table id="soussignes_listing" cellspacing="0" cellpadding="0" class="table_listing">
	<thead>
		<tr>
			<th class="col_date">Date</th>
			<th class="col_soussignes">Soussignés</th>
			<th class="col_statut">Statut</th>
			<th class="col_actions">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$counter = 0;
			foreach ($vracs as $vrac):
				$item = $vrac->value;
				if (!$archive && ($item->statut == Vrac::STATUT_CLOTURE || $item->statut == Vrac::STATUT_ANNULE)) {
					continue;
				}
				$alt = ($counter%2);
				$hasValidated = false;
		?>
		<tr>
			<td><?php echo format_date($item->date, 'p', 'fr'); ?></td>
			<td class="alt">
				<ul class="liste_soussignes">
					<?php 
						if ($item->soussignes->vendeur->identifiant): 
							if ($item->soussignes->vendeur->identifiant == $user->_id && $item->soussignes->vendeur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if ($item->soussignes->vendeur->date_validation): ?>soussigne_valide<?php else: ?>soussigne_attente<?php endif; ?>">Vendeur : <strong><?php echo $item->soussignes->vendeur->raison_sociale; ?></strong><?php if ($item->soussignes->vendeur->date_validation): ?> <img src="" alt="" /><?php endif; ?></li>
					<?php endif; ?>
					<?php 
						if ($item->soussignes->acheteur->identifiant):
							if ($item->soussignes->acheteur->identifiant == $user->_id && $item->soussignes->acheteur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if ($item->soussignes->acheteur->date_validation): ?>soussigne_valide<?php else: ?>soussigne_attente<?php endif; ?>">Acheteur : <strong><?php echo $item->soussignes->acheteur->raison_sociale; ?></strong></li>
					<?php endif; ?>
					<?php 
						if ($item->soussignes->mandataire->identifiant):
							if ($item->soussignes->mandataire->identifiant == $user->_id && $item->soussignes->mandataire->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if ($item->soussignes->mandataire->date_validation): ?>soussigne_valide<?php else: ?>soussigne_attente<?php endif; ?>">Courtier : <strong><?php echo $item->soussignes->mandataire->raison_sociale; ?></strong></li>
					<?php endif; ?>
				</ul>
			</td>
			<td><?php if (!$hasValidated && $item->statut == Vrac::STATUT_VALIDE_PARTIELLEMENT): ?>En attente de signature<?php else: ?><?php echo VracClient::getInstance()->getStatutLibelle($item->statut) ?><?php endif; ?></td>
			<td class="alt">
				<ul class="liste_actions">
					<?php if ($item->statut == Vrac::STATUT_CREE): ?>
					<li class="action_<?php echo strtolower(VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire))?>"><a href="<?php echo url_for('vrac_etape', array('numero_contrat' => $item->numero, 'etape' => $item->etape)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire) ?></a></li>
					<?php else: ?>
					<li class="action_<?php echo strtolower(VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire))?>"><a href="<?php echo url_for('vrac_fiche', array('numero_contrat' => $item->numero)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire, $hasValidated) ?></a></li>
					<?php endif; ?>
					<?php if ($item->is_proprietaire && $item->statut != Vrac::STATUT_ANNULE): ?>
					<li class="action_supprimer"><a href="<?php echo url_for('vrac_supprimer', array('numero_contrat' => $item->numero)) ?>" onclick="return confirm('Confirmez-vous la suppression du contrat?')">Supprimer</a></li>
					<?php endif; ?>
				</ul>
			</td>
		</tr>
			<?php
				$counter++;
				if ($limite && $counter == $limite) {
					break;
				}
			endforeach;
			?>
	</tbody>
</table>