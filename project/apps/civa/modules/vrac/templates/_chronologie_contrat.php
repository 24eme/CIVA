<?php use_helper('Date') ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 240px;">Chronologie du contrat</th>
		</tr>
	</thead>
	<tbody>
        <?php if ($vrac->isPremiereApplication() && (!$vrac->getContratPluriannuelCadre()->isInModeSurface()||$vrac->type_contrat == VracClient::TYPE_RAISIN)): ?>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat d'application généré à partir du contrat cadre</span>
			</td>
            <td style="text-align: left;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
        <?php elseif($vrac->isApplicationPluriannuel()): ?>
        <tr>
			<td>
				<span class="no_picto" style="padding-left:15px; margin-left: 2px;">&nbsp;Proposition de contrat d'application initié par <?php if ($vrac->hasCourtier()): ?>le courtier<?php else: ?>l'acheteur<?php endif; ?></span>
			</td>
            <td style="text-align: left;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du courtier</span>
			</td>
            <td style="text-align: left;">
				<?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du vendeur</span>
			</td>
            <td style="text-align: left;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
            <td>
                <span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature de l'acheteur</span>
            </td>
            <td style="text-align: left;">
                <?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
            </td>
        </tr>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
            <td>
                <span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat d'application validé</span>
            </td>
            <td style="text-align: left;">
                <?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
            </td>
        </tr>
        <?php else: ?>
        <tr>
			<td>
				<span class="picto_crayon" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet de contrat initié par <?php if ($vrac->hasCourtier()): ?>le courtier<?php elseif($vrac->hasVersion() || $vrac->isVendeurProprietaire()): ?>le vendeur<?php else: ?>l'acheteur<?php endif; ?></span>
			</td>
            <td style="text-align: left;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
        <?php if ($vrac->hasVersion() || $vrac->isVendeurProprietaire()): ?>
        <tr>
			<td>
				<span class="picto_sablier" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet soumis à l'acheteur pour validation</span>
			</td>
            <td style="text-align: left;"><?php if(!$vrac->isVendeurProprietaire()): ?><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?><?php endif; ?></td>
		</tr>
        <tr>
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet validé par l'acheteur et soumis au vendeur</span>
			</td>
            <td style="text-align: left;"><?php if(!in_array($vrac->valide->statut, array(Vrac::STATUT_CREE, Vrac::STATUT_PROJET_VENDEUR))): ?><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?><?php endif; ?></td>
		</tr>
        <?php endif; ?>
        <tr>
			<td>
				<span class="picto_sablier" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet soumis au vendeur pour signature</span>
			</td>
            <td style="text-align: left;"><?php if(!in_array($vrac->valide->statut, array(Vrac::STATUT_CREE, Vrac::STATUT_PROJET_VENDEUR))): ?><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?><?php endif; ?></td>
		</tr>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du vendeur</span>
			</td>
            <td style="text-align: left;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_sablier" style="padding-left:15px; margin-left: 2px;">&nbsp;Proposition de contrat soumise à l'acheteur</span>
			</td>
            <td style="text-align: left;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature de l'acheteur</span>
			</td>
            <td style="text-align: left;">
				<?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
            <td>
                <span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du courtier</span>
            </td>
            <td style="text-align: left;">
                <?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat de vente visé</span>
			</td>
            <td style="text-align: left;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
    <?php endif; ?>
	</tbody>
</table>
