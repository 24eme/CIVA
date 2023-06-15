<?php if ($vrac->vendeur_identifiant || $vrac->acheteur_identifiant || $vrac->mandataire_identifiant): ?>

	<div class="soussignes clearfix">

		<?php if ($vrac->vendeur_identifiant): $ic = null; ?>
			<?php if ($vrac->vendeur_identifiant == $vrac->createur_identifiant): $ic = ($vrac->interlocuteur_commercial)? $vrac->interlocuteur_commercial : '&nbsp'; ?>
				<div class="bloc_soussigne responsable<?php if ($user && $user->_id == $vrac->vendeur_identifiant): ?> actif<?php endif; ?><?php if (!$vrac->hasCourtier()): ?> bloc_soussigne_large<?php endif; ?>">
					<h3 class="titre_section<?php if ($fiche && $vrac->hasValide($vrac->vendeur_identifiant)): ?> soussigne_valide<?php elseif($fiche && !$vrac->hasValide($vrac->vendeur_identifiant)): ?> soussigne_attente<?php endif; ?>">Vendeur</h3><?php if($vrac->isValide()): ?><strong class="responsable">Responsable du contrat</strong><?php endif; ?>
					<div class="cadre">
			<?php else: ?>
				<div class="bloc_soussigne<?php if ($user && $user->_id == $vrac->vendeur_identifiant): ?> actif<?php endif; ?><?php if (!$vrac->hasCourtier()): ?> bloc_soussigne_large<?php endif; ?>">
					<h3 class="titre_section<?php if ($fiche && $vrac->hasValide($vrac->vendeur_identifiant)): ?> soussigne_valide<?php elseif($fiche && !$vrac->hasValide($vrac->vendeur_identifiant)): ?> soussigne_attente<?php endif; ?>">Vendeur</h3>
					<div class="cadre">
			<?php endif; ?>
						<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->vendeur, 'date_validation' => $vrac->valide->date_validation_vendeur, 'interlocuteur_commercial' => $ic, 'fiche' => $fiche)) ?>
					</div>
				</div>
		<?php endif; ?>

		<?php if ($vrac->acheteur_identifiant): $ic = null; ?>
			<?php if ($vrac->acheteur_identifiant == $vrac->createur_identifiant): $ic = ($vrac->interlocuteur_commercial)? $vrac->interlocuteur_commercial : '&nbsp'; ?>
				<div class="bloc_soussigne responsable<?php if ($user && $user->_id == $vrac->acheteur_identifiant): ?> actif<?php endif; ?><?php if (!$vrac->hasCourtier()): ?> bloc_soussigne_large<?php endif; ?>">
					<h3 class="titre_section<?php if ($fiche && $vrac->hasValide($vrac->acheteur_identifiant)): ?> soussigne_valide<?php elseif($fiche && !$vrac->hasValide($vrac->acheteur_identifiant)): ?> soussigne_attente<?php endif; ?>">Acheteur</h3><?php if($vrac->isValide()): ?><strong class="responsable">Responsable du contrat</strong><?php endif; ?>
					<div class="cadre">
			<?php else: ?>
				<div class="bloc_soussigne<?php if ($user && $user->_id == $vrac->acheteur_identifiant): ?> actif<?php endif; ?><?php if (!$vrac->hasCourtier()): ?> bloc_soussigne_large<?php endif; ?>">
					<h3 class="titre_section<?php if ($fiche && $vrac->hasValide($vrac->acheteur_identifiant)): ?> soussigne_valide<?php elseif($fiche && !$vrac->hasValide($vrac->acheteur_identifiant)): ?> soussigne_attente<?php endif; ?>">Acheteur</h3>
					<div class="cadre">
			<?php endif; ?>
						<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->acheteur, 'date_validation' => $vrac->valide->date_validation_acheteur, 'interlocuteur_commercial' => $ic, 'fiche' => $fiche)) ?>
					</div>
				</div>
		<?php endif; ?>

		<?php if ($vrac->mandataire_identifiant): $ic = null; ?>

			<?php if ($vrac->mandataire_identifiant == $vrac->createur_identifiant): $ic = ($vrac->interlocuteur_commercial)? $vrac->interlocuteur_commercial : '&nbsp'; ?>
				<div class="bloc_soussigne responsable<?php if ($user && $user->_id == $vrac->mandataire_identifiant): ?> actif<?php endif; ?><?php if (!$vrac->hasCourtier()): ?> bloc_soussigne_large<?php endif; ?>">
					<h3 class="titre_section<?php if ($fiche && $vrac->hasValide($vrac->mandataire_identifiant)): ?> soussigne_valide<?php elseif($fiche && !$vrac->hasValide($vrac->mandataire_identifiant)): ?> soussigne_attente<?php endif; ?>">Courtier</h3><?php if($vrac->isValide()): ?><strong class="responsable">Responsable du contrat</strong><?php endif; ?>

					<div class="cadre">
			<?php else: ?>
				<div class="bloc_soussigne<?php if ($user->_id == $vrac->mandataire_identifiant): ?> actif<?php endif; ?><?php if (!$vrac->hasCourtier()): ?> bloc_soussigne_large<?php endif; ?>">
					<h3 class="titre_section">Courtier</h3>
					<div class="cadre">
			<?php endif; ?>
						<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->mandataire, 'date_validation' => $vrac->valide->date_validation_mandataire, 'interlocuteur_commercial' => $ic, 'fiche' => $fiche)) ?>
					</div>
				</div>
		<?php endif; ?>

	</div>

<?php endif; ?>
