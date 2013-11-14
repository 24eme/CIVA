<?php use_helper('Date') ?>
<?php use_helper('Text') ?>
<?php use_helper('vrac') ?>
<div class="informations form_col">
<ul>
	<li><strong><?php echo renderTiersLibelle($tiers) ?></strong></li>
	<?php if (!$fiche && !count($tiers->emails	)): ?>
    <fieldset class="message message_erreur">
    	<legend class="message_title" style="position: relative;">Point bloquant <a href="" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
     	<ul class="messages_log">
            <li>Saisie de contrat impossible avec un interlocuteur dépourvu d'adresse e-mail.</li>
		</ul>
    </fieldset>
	<?php endif; ?>
	<?php if (!$fiche && !$tiers->getCompteObject()->isActif()): ?>
    <fieldset class="message">
    	<legend class="message_title" style="position: relative;">Point de vigilance<a href="" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
     	<ul class="messages_log">
            <li>Cet interlocuteur n'est rattaché à aucun compte.</li>
		</ul>
    </fieldset>
	<?php endif; ?>
	<?php if (isset($interlocuteur_commercial) && $interlocuteur_commercial->nom): ?>
	<li><strong><?php echo $interlocuteur_commercial->nom ?></strong><?php if ($interlocuteur_commercial->email): ?> <?php echo $interlocuteur_commercial->email ?><?php endif; ?></li>
	<?php else: ?>
	<li>&nbsp;</li>
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
	<li>E-mail : <strong><?php echo implode(",", $tiers->getRawValue()->emails->toArray(true, false)) ?></strong></li>
	<?php if ($fiche): ?>
	<?php if (isset($date_validation) && $date_validation): ?>
	<li>Signé le <strong><?php echo format_date($date_validation, 'p', 'fr') ?></strong></li>
	<?php else: ?>
	<li>En attente de signature</li>
	<?php endif; ?>
	<?php endif; ?>
</ul>
</div>