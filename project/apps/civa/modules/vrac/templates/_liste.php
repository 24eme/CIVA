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
			<td class="col_type" style="text-align: left;">
				<?php if($item->type_contrat): ?>
					<img src="/images/pictos/pi_<?php echo strtolower($item->type_contrat); ?><?php echo ($item->papier) ? '_orange' : null ?>.png" title="Type <?php echo strtolower($item->type_contrat); ?>" alt="<?php echo strtolower($item->type_contrat); ?>" />
				<?php endif ?>
                <?php if(isset(VracClient::getInstance()->find($vrac->id, acCouchdbClient::HYDRATE_JSON)->contrat_pluriannuel)): ?>
                    <svg style="color: #7e8601; margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-collection-fill" viewBox="0 0 16 16">
                      <path d="M0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6v7zM2 3a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 0-1h-11A.5.5 0 0 0 2 3zm2-2a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7A.5.5 0 0 0 4 1z"/>
                    </svg>
                <?php endif; ?>
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
					<li class="<?php if (!$item->papier && array_key_exists($item->soussignes->vendeur->identifiant, $tiers->getRawValue())): ?>soussigne_moi <?php endif; ?><?php if (!$item->papier && $item->soussignes->vendeur->date_validation): ?>soussigne_valide<?php elseif(!$item->papier): ?>soussigne_attente<?php endif; ?><?php if (!$item->papier && !array_key_exists($item->soussignes->vendeur->identifiant, $tiers->getRawValue())): ?>_grey<?php endif; ?>">Vendeur : <strong>
					<?php $rs = ($item->soussignes->vendeur->intitule)? $item->soussignes->vendeur->intitule.' '.$item->soussignes->vendeur->raison_sociale : $item->soussignes->vendeur->raison_sociale; echo truncate_text($rs, 35, '...', true); ?>
					</strong><?php if ($item->soussignes->vendeur->date_validation): ?> <img src="" alt="" /><?php endif; ?></li>
					<?php endif; ?>
					<?php
						if ($item->soussignes->acheteur->identifiant):
							if (array_key_exists($item->soussignes->acheteur->identifiant, $tiers->getRawValue()) && $item->soussignes->acheteur->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if (!$item->papier && array_key_exists($item->soussignes->acheteur->identifiant, $tiers->getRawValue())): ?>soussigne_moi <?php endif; ?><?php if (!$item->papier && $item->soussignes->acheteur->date_validation): ?>soussigne_valide<?php elseif(!$item->papier): ?>soussigne_attente<?php endif; ?><?php if (!$item->papier && !array_key_exists($item->soussignes->acheteur->identifiant, $tiers->getRawValue())): ?>_grey<?php endif; ?>">Acheteur : <strong>
						<?php $rs = ($item->soussignes->acheteur->intitule)? $item->soussignes->acheteur->intitule.' '.$item->soussignes->acheteur->raison_sociale : $item->soussignes->acheteur->raison_sociale; echo truncate_text($rs, 35, '...', true); ?>
					</strong></li>
					<?php endif; ?>
					<?php
						if ($item->soussignes->mandataire->identifiant):
							if (array_key_exists($item->soussignes->mandataire->identifiant, $tiers->getRawValue()) && $item->soussignes->mandataire->date_validation) {
								$hasValidated = true;
							}
					?>
					<li class="<?php if (!$item->papier && array_key_exists($item->soussignes->mandataire->identifiant, $tiers->getRawValue())): ?>soussigne_moi <?php endif; ?><?php if (!$item->papier  && $item->soussignes->mandataire->date_validation): ?>soussigne_valide<?php elseif(!$item->papier): ?>soussigne_attente<?php endif; ?><?php if (!$item->papier && !array_key_exists($item->soussignes->mandataire->identifiant, $tiers->getRawValue())): ?>_grey<?php endif; ?>">Courtier :  <strong>
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
