<?php use_helper('Date') ?>
<?php use_helper('Text') ?>
<table id="soussignes_listing" cellspacing="0" cellpadding="0" class="table_listing">
	<thead>
		<tr>
			<th class="col_type">Type</th>
			<th class="col_numero">N°</th>
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
				if ($item->statut == Vrac::STATUT_CREE && !$item->is_proprietaire) {
					continue;
				}
				if($item->papier && $item->statut == Vrac::STATUT_CREE && !$sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
					continue;
				}
				$alt = ($counter%2);
				$hasValidated = false;
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td class="col_type">
				<?php if($item->type_contrat): ?>
					<img src="/images/pictos/pi_<?php echo strtolower($item->type_contrat); ?>.png" title="Type <?php echo strtolower($item->type_contrat); ?>" alt="<?php echo strtolower($item->type_contrat); ?>" />
				<?php endif ?>
			</td>
			<td class="col_numero"><?php echo ($item->numero_visa) ? $item->numero_visa : "" ?></td>
			<td><?php echo format_date($item->date, 'p', 'fr'); ?></td>
			<td>
				<ul class="liste_soussignes">
					<?php
						if ($item->soussignes->vendeur->identifiant):
							if (array_key_exists($item->soussignes->vendeur->identifiant, $tiers->getRawValue()) && $item->soussignes->vendeur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if (array_key_exists($item->soussignes->vendeur->identifiant, $tiers->getRawValue())): ?>soussigne_moi <?php endif; ?><?php if ($item->soussignes->vendeur->date_validation): ?>soussigne_valide<?php else: ?>soussigne_attente<?php endif; ?><?php if (!array_key_exists($item->soussignes->vendeur->identifiant, $tiers->getRawValue())): ?>_grey<?php endif; ?>">Vendeur : <strong>
					<?php $rs = ($item->soussignes->vendeur->intitule)? $item->soussignes->vendeur->intitule.' '.$item->soussignes->vendeur->raison_sociale : $item->soussignes->vendeur->raison_sociale; echo truncate_text($rs, 35, '...', true); ?>
					</strong><?php if ($item->soussignes->vendeur->date_validation): ?> <img src="" alt="" /><?php endif; ?></li>
					<?php endif; ?>
					<?php
						if ($item->soussignes->acheteur->identifiant):
							if (array_key_exists($item->soussignes->acheteur->identifiant, $tiers->getRawValue()) && $item->soussignes->acheteur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if (array_key_exists($item->soussignes->acheteur->identifiant, $tiers->getRawValue())): ?>soussigne_moi <?php endif; ?><?php if ($item->soussignes->acheteur->date_validation): ?>soussigne_valide<?php else: ?>soussigne_attente<?php endif; ?><?php if (!array_key_exists($item->soussignes->acheteur->identifiant, $tiers->getRawValue())): ?>_grey<?php endif; ?>">Acheteur : <strong>
						<?php $rs = ($item->soussignes->acheteur->intitule)? $item->soussignes->acheteur->intitule.' '.$item->soussignes->acheteur->raison_sociale : $item->soussignes->acheteur->raison_sociale; echo truncate_text($rs, 35, '...', true); ?>
					</strong></li>
					<?php endif; ?>
					<?php
						if ($item->soussignes->mandataire->identifiant):
							if (array_key_exists($item->soussignes->mandataire->identifiant, $tiers->getRawValue()) && $item->soussignes->mandataire->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if (array_key_exists($item->soussignes->mandataire->identifiant, $tiers->getRawValue())): ?>soussigne_moi <?php endif; ?><?php if ($item->soussignes->mandataire->date_validation): ?>soussigne_valide<?php else: ?>soussigne_attente<?php endif; ?><?php if (!array_key_exists($item->soussignes->mandataire->identifiant, $tiers->getRawValue())): ?>_grey<?php endif; ?>">Courtier :  <strong>
							<?php $rs = ($item->soussignes->mandataire->intitule)? $item->soussignes->mandataire->intitule.' '.$item->soussignes->mandataire->raison_sociale : $item->soussignes->mandataire->raison_sociale; echo truncate_text($rs, 35, '...', true); ?>
					</strong></li>
					<?php endif; ?>
				</ul>
			</td>
			<td><?php if ($item->papier): ?>Papier<?php elseif (!$hasValidated && $item->statut == Vrac::STATUT_VALIDE_PARTIELLEMENT): ?>En attente de signature<?php else: ?><?php echo VracClient::getInstance()->getStatutLibelle($item->statut) ?><?php endif; ?></td>
			<td>
				<ul class="liste_actions">
					<?php if ($item->statut == Vrac::STATUT_CREE): ?>
					<li class="action_<?php echo strtolower(VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire))?>"><a href="<?php echo url_for('vrac_etape', array('numero_contrat' => $item->numero, 'etape' => $item->etape)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire) ?></a></li>
					<?php else: ?>
					<li class="action_<?php echo strtolower(VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire, $hasValidated))?>"><a href="<?php echo url_for('vrac_fiche', array('numero_contrat' => $item->numero)) ?>"><?php echo VracClient::getInstance()->getStatutLibelleAction($item->statut, (boolean)$item->is_proprietaire, $hasValidated) ?></a></li>
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
