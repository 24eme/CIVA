<?php use_helper('Date') ?>
<?php use_helper('Text') ?>
<?php use_helper('vrac') ?>
<?php use_helper('Phone') ?>
<div class="informations form_col">
<ul>
	<li><strong><?php echo renderTiersLibelle($tiers) ?></strong></li>

    <?php if (!$fiche && !$tiers->isActif()): ?>
    <fieldset class="message message_erreur">
        <legend class="message_title" style="position: relative;">Point bloquant <a href="" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
        <ul class="messages_log">
            <li>Cet opérateur n'est plus actif</li>
        </ul>
    </fieldset>
	<?php elseif (!$fiche && !count($tiers->emails) && !$vrac->isPapier()): ?>
    <fieldset class="message message_erreur">
    	<legend class="message_title" style="position: relative;">Point bloquant <a href="" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
     	<ul class="messages_log">
            <li>Saisie de contrat impossible avec un opérateur dépourvu d'adresse e-mail.</li>
		</ul>
    </fieldset>
	<?php elseif (!$fiche && !count($tiers->emails) && $vrac->isPapier()): ?>
	<fieldset class="message">
		<legend class="message_title" style="position: relative;">Point de vigilance <a href="" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
		<ul class="messages_log">
			<li>Cet opérateur n'a pas d'adresse e-mail.</li>
		</ul>
	</fieldset>
	<?php elseif (!$fiche && !VracClient::getInstance()->isSoussigneInscrit($tiers->getTiersObject())): ?>
    <fieldset class="message">
    	<legend class="message_title" style="position: relative;">Point de vigilance<a href="" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
     	<ul class="messages_log">
            <li>Cet opérateur n'a pas créé son compte.</li>
		</ul>
    </fieldset>
	<?php endif; ?>
	<?php if (isset($interlocuteur_commercial) && $interlocuteur_commercial->nom): ?>
	<li><strong><?php echo $interlocuteur_commercial->nom ?></strong><?php if ($interlocuteur_commercial->email && !$interlocuteur_commercial->telephone): ?> <?php echo $interlocuteur_commercial->email ?><?php endif; ?><?php if ($interlocuteur_commercial->telephone): ?> Tél. <?php echo formatPhone($interlocuteur_commercial->telephone) ?><?php endif; ?></li>
    <?php elseif(!$tiers->exist('cvi')): ?>
    <li>&nbsp;</li>
	<?php elseif ($vrac->interlocuteur_commercial->nom && !$vrac->hasCourtier()): ?>
	<li>&nbsp;</li>
	<?php endif; ?>
	<?php if ($tiers->exist('cvi')): ?>
        <?php if ($tiers->cvi): ?>
            <li>CVI : <strong><?php echo $tiers->cvi ?></strong></li>
        <?php elseif ($tiers->civaba): ?>
            <li>CIVA : <strong><?php echo $tiers->civaba ?></strong></li>
        <?php else: ?>
            <li>nbsp;</li>
        <?php endif; ?>
	<?php endif; ?>
	<li>Siret : <strong><?php echo $tiers->siret ?></strong></li>
	<?php if ($tiers->exist('carte_pro')): ?>
	<li class="noprint">N° d'inscription au registre national : <strong><?php echo $tiers->carte_pro ?></strong></li>
	<?php endif; ?>
	<?php if ($tiers->exist('num_accise')): ?>
	<li class="noprint">N°Accises : <strong><?php echo $tiers->num_accise ?></strong></li>
	<?php endif; ?>
	<li class="noprint">Adresse : <strong><?php echo $tiers->adresse ?></strong></li>
	<li class="noprint">Code postal : <strong><?php echo $tiers->code_postal ?></strong></li>
	<li class="noprint">Commune : <strong><?php echo $tiers->commune ?></strong></li>
	<li class="noprint">Téléphone : <strong><?php echo formatPhone($tiers->telephone) ?></strong></li>
	<li class="noprint">E-mail : <strong><span style="cursor: help" title="<?php echo implode(", ", $tiers->getRawValue()->emails->toArray(true, false)); ?>"><?php echo truncate_text(implode(", ", $tiers->getRawValue()->emails->toArray(true, false)), 35) ?></strong></span></li>
    <?php if($vrac->exist($tiers->getKey().'_assujetti_tva')&&$fiche): ?>
    <li class="noprint">
        Assujeti à la TVA : <strong><?php if($vrac->get($tiers->getKey().'_assujetti_tva')): ?>Oui<?php else: ?>Non<?php endif; ?></strong>
    </li>
    <?php endif; ?>
    <?php if ($fiche): ?>
	<?php if (isset($date_validation) && $date_validation): ?>
	<li class="noprint">Signé le <strong><?php echo format_date($date_validation, 'p', 'fr') ?></strong></li>
	<?php else: ?>
	<li class="noprint">En attente de signature</li>
	<?php endif; ?>
	<?php endif; ?>
</ul>
</div>
