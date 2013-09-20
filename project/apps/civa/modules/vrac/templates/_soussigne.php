<?php use_helper('Date') ?>
<ul>
	<li><strong><?php echo $tiers->raison_sociale ?></strong></li>
	<li>Siret : <strong><?php echo $tiers->siret ?></strong></li>
	<?php if ($tiers->exist('cvi')): ?>
	<li>CVI : <strong><?php echo $tiers->cvi ?></strong></li>
	<?php endif; ?>
	<?php if ($tiers->exist('num_accise')): ?>
	<li>N°Accise : <strong><?php echo $tiers->num_accise ?></strong></li>
	<?php endif; ?>
	<?php if ($tiers->exist('carte_pro')): ?>
	<li>N° Carte professionnelle : <strong><?php echo $tiers->carte_pro ?></strong></li>
	<?php endif; ?>
	<li>Adresse : <strong><?php echo $tiers->adresse ?></strong></li>
	<li>Code postal : <strong><?php echo $tiers->code_postal ?></strong></li>
	<li>Commune : <strong><?php echo $tiers->commune ?></strong></li>
	<li>Téléphone : <strong><?php echo $tiers->telephone ?></strong></li>
	<li>E-mail : <strong><?php echo $tiers->email ?></strong></li>
	<?php if (isset($date_validation) && $date_validation): ?>
	<li>Validé le <strong><?php echo format_date($date_validation, 'p', 'fr') ?></strong></li>
	<?php endif; ?>
</ul>