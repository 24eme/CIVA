<?php use_helper('Date') ?>
<table class="validation table_donnees" style="width: 400px;">
	<thead>
		<tr>
			<th style="width: 260px;">Chronologie du contrat</th>
		</tr>
	</thead>
	<tbody>
        <?php if ($vrac->isPremiereApplication() && (!$vrac->getContratPluriannuelCadre()->isInModeSurface()||$vrac->type_contrat == VracClient::TYPE_RAISIN)): ?>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat d'application généré à partir du contrat cadre</span>
			</td>
            <td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
        <?php elseif($vrac->isApplicationPluriannuel()): ?>
        <tr>
			<td>
				<span class="no_picto" style="padding-left:15px; margin-left: 2px;">&nbsp;Proposition de contrat d'application initié par <?php if ($vrac->hasCourtier()): ?>le courtier<?php else: ?>l'acheteur<?php endif; ?></span>
			</td>
            <td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du courtier</span>
			</td>
            <td style="text-align: center;">
				<?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du vendeur</span>
			</td>
            <td style="text-align: center;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
			</td>
		</tr>
        <tr class="<?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
            <td>
                <span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature de l'acheteur</span>
            </td>
            <td style="text-align: center;">
                <?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
            </td>
        </tr>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
            <td>
                <span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat d'application validé</span>
            </td>
            <td style="text-align: center;">
                <?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
            </td>
        </tr>
        <?php else: ?>
        <tr>
			<td>
				<span class="picto_crayon" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet de contrat initié par <?php if ($vrac->hasCourtier()): ?>le courtier<?php elseif($vrac->hasVersion() || $vrac->isVendeurProprietaire()): ?>le vendeur<?php else: ?>l'acheteur<?php endif; ?></span>
			</td>
            <td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?>
			</td>
		</tr>
        <?php if ($vrac->hasVersion() || $vrac->isVendeurProprietaire()): ?>
        <tr>
			<td>
				<span class="picto_sablier" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet soumis à l'acheteur pour validation</span>
			</td>
            <td style="text-align: center;"><?php if(!$vrac->isVendeurProprietaire()): ?><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?><?php endif; ?>
            <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
            <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', VracMailer::getInstance()->demandeValidationAcheteurCourtier($vrac->getRawValue())); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu du mail]</a>
            <?php endif; ?>
            </td>
		</tr>
        <tr>
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet validé par l'acheteur</span>
			</td>
            <td style="text-align: center;"><?php if(!in_array($vrac->valide->statut, array(Vrac::STATUT_CREE, Vrac::STATUT_PROJET_VENDEUR))): ?><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?><?php endif; ?></td>
		</tr>
        <?php endif; ?>
        <tr>
			<td>
				<span class="picto_sablier" style="padding-left:15px; margin-left: 2px;">&nbsp;Projet soumis au vendeur pour signature</span>
			</td>
            <td style="text-align: center;"><?php if(!in_array($vrac->valide->statut, array(Vrac::STATUT_CREE, Vrac::STATUT_PROJET_VENDEUR))): ?><?php echo format_date($vrac->valide->date_saisie, 'dd/MM/yyyy'); ?><?php endif; ?>
            <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
            <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', VracMailer::getInstance()->demandeSignatureVendeur($vrac->getRawValue())); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu du mail]</a>
            <?php endif; ?>
            </td>
		</tr>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du vendeur</span>
			</td>
            <td style="text-align: center;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
                <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', VracMailer::getInstance()->confirmationSignature($vrac->getRawValue(), $vrac->vendeur_identifiant)); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu du mail]</a>
                <?php endif; ?>
            </td>
		</tr>
        <tr class="<?php if (!$vrac->hasVendeurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_sablier" style="padding-left:15px; margin-left: 2px;">&nbsp;Proposition de contrat soumise à l'acheteur<?php if($vrac->hasCourtier()): ?> et au courtier<?php endif; ?></span>
			</td>
            <td style="text-align: center;">
				<?php echo ($vrac->hasVendeurSigne())? format_date($vrac->valide->date_validation_vendeur, 'dd/MM/yyyy') : ''; ?>
                <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', implode("\n\n--------------\n\n", VracMailer::getInstance()->demandeSignature($vrac->getRawValue(), $vrac->vendeur_identifiant))); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu du mail]</a>
                <?php endif; ?>
            </td>
		</tr>
        <tr class="<?php if (!$vrac->hasAcheteurSigne()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature de l'acheteur</span>
			</td>
            <td style="text-align: center;">
				<?php echo ($vrac->hasAcheteurSigne())? format_date($vrac->valide->date_validation_acheteur, 'dd/MM/yyyy') : ''; ?>
                <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', VracMailer::getInstance()->confirmationSignature($vrac->getRawValue(), $vrac->acheteur_identifiant)); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu du mail]</a>
                <?php endif; ?>
            </td>
		</tr>
        <?php if ($vrac->hasCourtier()): ?>
        <tr class="<?php if (!$vrac->hasCourtierSigne()): ?> text-muted<?php endif; ?>">
            <td>
                <span class="picto_signer" style="padding-left:15px; margin-left: 2px;">&nbsp;Signature du courtier</span>
            </td>
            <td style="text-align: center;">
                <?php echo ($vrac->hasCourtierSigne())? format_date($vrac->valide->date_validation_mandataire, 'dd/MM/yyyy') : ''; ?>
                <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', VracMailer::getInstance()->confirmationSignature($vrac->getRawValue(), $vrac->mandataire_identifiant)); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu du mail]</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <tr class="<?php if (!$vrac->isValide()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat de vente visé</span>
			</td>
            <td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
                <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', implode("\n\n--------------\n\n", VracMailer::getInstance()->validationContrat($vrac->getRawValue(), false))); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu des mails]</a>
                <?php endif; ?>
            </td>
		</tr>
        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC): ?>
		<tr class="<?php if (!$vrac->isCloture()): ?> text-muted<?php endif; ?>">
			<td>
				<span class="picto_check" style="padding-left:15px; margin-left: 2px;">&nbsp;Contrat de vente clôturé</span>
			</td>
            <td style="text-align: center;">
				<?php echo format_date($vrac->valide->date_validation, 'dd/MM/yyyy'); ?>
                <?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo str_replace('"', '\"', implode("\n\n--------------\n\n", VracMailer::getInstance()->clotureContrat($vrac->getRawValue(), false))); ?>" style="position: absolute; left: 430px; opacity: 0.5;">[voir le contenu des mails]</a>
                <?php endif; ?>
            </td>
		</tr>
        <?php endif; ?>
    <?php endif; ?>
	</tbody>
</table>
<pre style="border: 1px solid #555; background: white; padding: 5px; display: none;" id="contenu_mail">
</pre>
