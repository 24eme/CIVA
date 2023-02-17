<?php use_helper('Date') ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Chronologie du contrat</th>
		</tr>
	</thead>
	<tbody>
        <tr>
			<td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
			<td>
				Projet de contrat initié par <?php if ($vrac->hasVersion()): ?>le vendeur<?php else: ?>l'acheteur<?php endif; ?>
			</td>
		</tr>
        <?php if ($vrac->hasVersion()): ?>
        <tr class="alt">
			<td style="text-align: center;">
			</td>
			<td>
				Projet validé par l'acheteur
			</td>
		</tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->hasVersion()): ?>alt<?php endif; ?><?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: center;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer">&nbsp;Signature du vendeur</span>
			</td>
		</tr>
        <tr class="<?php if ($vrac->hasVersion()): ?>alt<?php endif; ?><?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: center;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				Proposition de contrat soumis à l'acheteur
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasVersion()): ?>alt<?php endif; ?><?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: center;">
				<?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer">&nbsp;Signature de l'acheteur</span>
			</td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if ($vrac->hasVersion()): ?>alt<?php endif; ?><?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: center;">
				<?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer">&nbsp;Signature du courtier</span>
			</td>
		</tr>
        <?php endif; ?>
        <tr class="<?php if (($vrac->hasCourtier() && !$vrac->hasVersion())||(!$vrac->hasCourtier() && $vrac->hasVersion())): ?>alt<?php endif; ?><?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
			</td>
			<td>
				<span class="picto_check">&nbsp;Contrat de vente visé</span>
			</td>
		</tr>
	</tbody>
</table>
