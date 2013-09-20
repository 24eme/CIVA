<table>
	<tr>
		<?php if ($vrac->vendeur_identifiant): ?>
		<td>
			<h1>Vendeur<?php if ($vrac->vendeur_identifiant == $vrac->createur_identifiant): ?> *<?php endif; ?></h1>
			<?php include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur, 'date_validation' => $vrac->valide->date_validation_vendeur)) ?>
		</td>
		<?php endif; ?>
		<?php if ($vrac->acheteur_identifiant): ?>
		<td>
			<h1>Acheteur<?php if ($vrac->acheteur_identifiant == $vrac->createur_identifiant): ?> *<?php endif; ?></h1>
			<?php include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur, 'date_validation' => $vrac->valide->date_validation_acheteur)) ?>
		</td>
		<?php endif; ?>
		<?php if ($vrac->mandataire_identifiant): ?>
		<td>
			<h1>Courtier<?php if ($vrac->mandataire_identifiant == $vrac->createur_identifiant): ?> *<?php endif; ?></h1>
			<?php include_partial('vrac/soussigne', array('tiers' => $vrac->mandataire, 'date_validation' => $vrac->valide->date_validation_mandataire)) ?>
		</td>
		<?php endif; ?>
	</tr>
</table>