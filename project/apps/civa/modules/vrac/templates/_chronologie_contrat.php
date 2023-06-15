<?php use_helper('Date') ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Chronologie du contrat</th>
		</tr>
	</thead>
	<tbody>
        <?php if ($vrac->isPremiereApplication() && (!$vrac->getContratPluriannuelCadre()->isInModeSurface()||$vrac->type_contrat == VracClient::TYPE_RAISIN)): ?>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
			</td>
			<td>
				<span class="picto_check" style="padding-left:15px;">&nbsp;Contrat d'application généré à partir du contrat cadre</span>
			</td>
		</tr>
        <?php elseif($vrac->isApplicationPluriannuel()): ?>
        <tr>
			<td style="text-align: right;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
			<td>
				<span class="no_picto" style="padding-left:15px;">&nbsp;Proposition de contrat d'application initié par <?php if ($vrac->hasCourtier()): ?>le courtier<?php else: ?>l'acheteur<?php endif; ?></span>
			</td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer" style="padding-left:15px;">&nbsp;Signature du courtier</span>
			</td>
		</tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer" style="padding-left:15px;">&nbsp;Signature du vendeur</span>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
            <td style="text-align: right;">
                <?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
            </td>
            <td>
                <span class="picto_signer" style="padding-left:15px;">&nbsp;Signature de l'acheteur</span>
            </td>
        </tr>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
            <td style="text-align: right;">
                <?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
            </td>
            <td>
                <span class="picto_check" style="padding-left:15px;">&nbsp;Contrat d'application validé</span>
            </td>
        </tr>
        <?php else: ?>
        <tr>
			<td style="text-align: right;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
			<td>
				<span class="picto_crayon" style="padding-left:15px;">&nbsp;Projet de contrat initié par <?php if ($vrac->hasVersion() && $vrac->hasCourtier()): ?>le courtier<?php elseif($vrac->hasVersion()): ?>le vendeur<?php else: ?>l'acheteur<?php endif; ?></span>
			</td>
		</tr>
        <?php if ($vrac->hasVersion()): ?>
        <tr>
			<td style="text-align: right;"></td>
			<td>
				<span class="picto_check" style="padding-left:15px;">&nbsp;Projet validé par l'acheteur et soumis à l'acheteur</span>
			</td>
		</tr>
        <?php endif; ?>
        <tr>
			<td style="text-align: right;"></td>
			<td>
				<span class="picto_sablier" style="padding-left:15px;">&nbsp;Projet soumis au vendeur pour signature</span>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer" style="padding-left:15px;">&nbsp;Signature du vendeur</span>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_sablier" style="padding-left:15px;">&nbsp;Proposition de contrat soumise à l'acheteur</span>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
			</td>
			<td>
				<span class="picto_signer" style="padding-left:15px;">&nbsp;Signature de l'acheteur</span>
			</td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
            <td style="text-align: right;">
                <?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
            </td>
            <td>
                <span class="picto_signer" style="padding-left:15px;">&nbsp;Signature du courtier</span>
            </td>
        </tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td style="text-align: right;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
			</td>
			<td>
				<span class="picto_check" style="padding-left:15px;">&nbsp;Contrat de vente visé</span>
			</td>
		</tr>
    <?php endif; ?>
	</tbody>
</table>
