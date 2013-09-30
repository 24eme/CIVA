<?php use_helper('Date') ?>
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
			$nb = count($vracs) - 1;
			for ($i=$nb; $i>=0; $i--):
				$item = $vracs[$i];
				$item = $item->value;
				if (!$archive && $item->statut == Vrac::STATUT_CLOTURE) {
					continue;
				}
				$alt = ($counter%2);
				$hasValidated = false;
		?>
		<tr<?php if($alt): ?> class="alt"<?php endif; ?>>
			<td><?php echo format_date($item->date, 'p', 'fr'); ?></td>
			<td>
				<ul>
					<?php 
						if ($item->soussignes->vendeur->identifiant): 
							if ($item->soussignes->vendeur->identifiant == $user->_id && $item->soussignes->vendeur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li>Vendeur : <strong><?php echo $item->soussignes->vendeur->raison_sociale; ?></strong><?php if ($item->soussignes->vendeur->date_validation): ?> V<?php endif; ?></li>
					<?php endif; ?>
					<?php 
						if ($item->soussignes->acheteur->identifiant):
							if ($item->soussignes->acheteur->identifiant == $user->_id && $item->soussignes->acheteur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li>Acheteur : <strong><?php echo $item->soussignes->acheteur->raison_sociale; ?></strong><?php if ($item->soussignes->acheteur->date_validation): ?> V<?php endif; ?></li>
					<?php endif; ?>
					<?php 
						if ($item->soussignes->mandataire->identifiant):
							if ($item->soussignes->mandataire->identifiant == $user->_id && $item->soussignes->mandataire->date_validation) {
								$hasValidated = true;
							}
					?>
					<li>Courtier : <strong><?php echo $item->soussignes->mandataire->raison_sociale; ?></strong><?php if ($item->soussignes->mandataire->date_validation): ?> V<?php endif; ?></li>
					<?php endif; ?>
				</ul>
			</td>
			<td><?php if (!$hasValidated && $item->statut == Vrac::STATUT_VALIDE_PARTIELLEMENT): ?>En attente de signature<?php else: ?><?php echo VracClient::getInstance()->getStatutLibelle($item->statut) ?><?php endif; ?></td>
			<td class="actions">
				<?php if ($item->statut == Vrac::STATUT_CREE): ?>
				<a href="<?php echo url_for('vrac_etape', array('numero_contrat' => $item->numero, 'etape' => $item->etape)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire) ?></a> 
				<?php 
					else:
				?>
				<a href="<?php echo url_for('vrac_fiche', array('numero_contrat' => $item->numero)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire, $hasValidated) ?></a>
				<?php endif; ?>
				| <a href="<?php echo url_for('vrac_supprimer', array('numero_contrat' => $item->numero)) ?>">X</a></td>
		</tr>
			<?php
				$counter++;
				if ($limite && $counter == $limite) {
					break;
				}
			endfor;
			?>
	</tbody>
</table>