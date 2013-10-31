<?php use_helper('Date') ?>
<?php use_helper('vrac') ?>
<div class="informations form_col">
<ul>
	<li><strong><?php echo renderTiersLibelle($tiers) ?></strong></li>
	<?php if (isset($interlocuteur_commercial) && !empty($interlocuteur_commercial)): ?>
	<li>Interlocuteur : <strong><?php echo $interlocuteur_commercial ?></strong></li>
	<?php endif; ?>
	<?php if ($tiers->exist('cvi')): ?>
	<li>CVI : <strong><?php echo $tiers->cvi ?></strong></li>
	<?php endif; ?>
	<?php if ($tiers->exist('carte_pro')): ?>
	<li>N° Carte professionnelle : <strong><?php echo $tiers->carte_pro ?></strong></li>
	<?php endif; ?>
	<li>Siret : <strong><?php echo $tiers->siret ?></strong></li>
	<?php if ($tiers->exist('num_accise')): ?>
	<li>N°Accises : <strong><?php echo $tiers->num_accise ?></strong></li>
	<?php endif; ?>
	<li>Adresse : <strong><?php echo $tiers->adresse ?></strong></li>
	<li>Code postal : <strong><?php echo $tiers->code_postal ?></strong></li>
	<li>Commune : <strong><?php echo $tiers->commune ?></strong></li>
	<li>Téléphone : <strong><?php echo $tiers->telephone ?></strong></li>
	<li>E-mail : <strong><?php echo $tiers->email ?></strong></li>
	<?php if (isset($date_validation) && $date_validation): ?>
	<li>Signé le <strong><?php echo format_date($date_validation, 'p', 'fr') ?></strong></li>
	<?php else: ?>
	<li>En attente de signature</li>
	<?php endif; ?>
</ul>
</div>