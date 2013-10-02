Madame, Monsieur,<br /><br />
Le contrat de vente saisi le <?php echo strftime('%d/%m/%Y', strtotime($vrac->valide->date_saisie)) ?> a expiré.<br />
<strong>Ce contrat a donc été supprimé et est considéré comme non valable.</strong><br /><br />
Pour mémoire, le contrat engageait les parties suivantes :<br /><br />
<ul>
	<?php if ($vrac->vendeur_identifiant): ?>
	<li>Vendeur : <strong><?php echo $vrac->vendeur->raison_sociale ?></strong></li>
	<?php endif; ?>
	<?php if ($vrac->acheteur_identifiant): ?>
	<li>Acheteur : <strong><?php echo $vrac->acheteur->raison_sociale ?></strong></li>
	<?php endif; ?>
	<?php if ($vrac->mandataire_identifiant): ?>
	<li>Courtier : <strong><?php echo $vrac->mandataire->raison_sociale ?></strong></li>
	<?php endif; ?>
</ul>
<br />
Pour toute information, vous pouvez contacter votre interprofession ou votre interlocuteur commercial.<br /><br />
Cordialement,<br />
Le CIVA