<?php if ($vrac->vendeur_identifiant || $vrac->acheteur_identifiant || $vrac->mandataire_identifiant): ?>

	<div class="soussignes clearfix">

		<?php if ($vrac->vendeur_identifiant): ?>
			<div class="bloc_soussigne">
				<h3>Vendeur<?php if ($vrac->vendeur_identifiant == $vrac->createur_identifiant): ?> *<?php endif; ?></h3>

				<div class="cadre">
					<?php include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur, 'date_validation' => $vrac->valide->date_validation_vendeur)) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($vrac->acheteur_identifiant): ?>
			<div class="bloc_soussigne">
				<h3>Acheteur<?php if ($vrac->acheteur_identifiant == $vrac->createur_identifiant): ?> *<?php endif; ?></h3>
				<div class="cadre">
					<?php include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur, 'date_validation' => $vrac->valide->date_validation_acheteur)) ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($vrac->mandataire_identifiant): ?>
			<div class="bloc_soussigne">
				<h3>Courtier<?php if ($vrac->mandataire_identifiant == $vrac->createur_identifiant): ?> *<?php endif; ?></h3>

				<div class="cadre">
					<?php include_partial('vrac/soussigne', array('tiers' => $vrac->mandataire, 'date_validation' => $vrac->valide->date_validation_mandataire)) ?>
				</div>
			</div>
		<?php endif; ?>

	</div>

<?php endif; ?>