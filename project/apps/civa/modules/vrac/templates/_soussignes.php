<?php if ($vrac->vendeur_identifiant || $vrac->acheteur_identifiant || $vrac->mandataire_identifiant): ?>

	<div class="soussignes clearfix">

		<?php if ($vrac->vendeur_identifiant): ?>
			<?php if ($vrac->vendeur_identifiant == $vrac->createur_identifiant): ?>
				<div class="bloc_soussigne responsable">
					<h3 class="titre_section">Vendeur *</h3>
					<div class="cadre">
						<strong class="responsable">Responsable du contrat</strong>
			<?php else: ?>
				<div class="bloc_soussigne">
					<h3 class="titre_section">Vendeur</h3>
					<div class="cadre">
			<?php endif; ?>	
						<?php include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur, 'date_validation' => $vrac->valide->date_validation_vendeur)) ?>
					</div>
				</div>
		<?php endif; ?>

		<?php if ($vrac->acheteur_identifiant): ?>
			<?php if ($vrac->acheteur_identifiant == $vrac->createur_identifiant): ?>
				<div class="bloc_soussigne responsable">
					<h3 class="titre_section">Acheteur *</h3>
					<div class="cadre">
						<strong class="responsable">Responsable du contrat</strong>
			<?php else: ?>
				<div class="bloc_soussigne">
					<h3 class="titre_section">Acheteur</h3>
					<div class="cadre">
			<?php endif; ?>
						<?php include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur, 'date_validation' => $vrac->valide->date_validation_acheteur)) ?>
					</div>
				</div>
		<?php endif; ?>

		<?php if ($vrac->mandataire_identifiant): ?>

			<?php if ($vrac->mandataire_identifiant == $vrac->createur_identifiant): ?>
				<div class="bloc_soussigne responsable">
					<h3 class="titre_section">Courtier *</h3>

					<div class="cadre">
						<strong class="responsable">Responsable du contrat</strong>
			<?php else: ?>
				<div class="bloc_soussigne">
					<h3 class="titre_section">Courtier</h3>
					<div class="cadre">
			<?php endif; ?>
						<?php include_partial('vrac/soussigne', array('tiers' => $vrac->mandataire, 'date_validation' => $vrac->valide->date_validation_mandataire)) ?>
					</div>
				</div>
		<?php endif; ?>

	</div>

<?php endif; ?>