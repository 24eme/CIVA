<?php use_helper('vracExport'); ?>
<?php use_helper('Phone') ?>
<?php

$hasCourtier = $vrac->hasCourtier();
?>
<br/>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="50%" valign="top">
			<span style="background-color: grey; color: white; font-weight: bold;">&nbsp;Vendeur&nbsp;</span><br/>
			<table cellpadding="0" cellspacing="0" border="0" width="99%" style="border: 1px solid #000;">
				<tr>
					<td>&nbsp;<i><?php if($vrac->vendeur->intitule): ?><?php echo $vrac->vendeur->intitule ?>&nbsp;<?php endif; ?><?php echo truncate_text($vrac->vendeur->raison_sociale, 35); ?></i></td>
				</tr>
                                <tr>
                                        <td>&nbsp;<i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i><br/>
                                        <i>&nbsp;<?php echo $vrac->vendeur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->vendeur->commune; ?></i><br/></td>
                                </tr>
				<tr>
					<?php if($vrac->vendeur->cvi): ?>
                    <td>&nbsp;CVI : <i><?php echo $vrac->vendeur->cvi; ?></i></td>
                    <?php elseif($vrac->vendeur->civaba): ?>
                    <td>&nbsp;CIVA : <i><?php echo $vrac->vendeur->civaba; ?></i></td> 
                    <?php else: ?>
                    <td>&nbsp;</td>
                    <?php endif; ?>
				</tr>
				<tr>
					<td>&nbsp;Tel. : <i><?php echo formatPhone($vrac->vendeur->telephone); ?></i></td>
				</tr>
				<tr>
					<td>&nbsp;SIRET : <i><?php echo $vrac->vendeur->siret; ?></i></td>
				</tr>
				<tr>
					<td>&nbsp;N° d'accise : <i><?php echo $vrac->vendeur->num_accise; ?></i></td>  
				</tr>
                <tr>
                    <?php if(count($vrac->vendeur->emails->toArray(true, false)) > 0): ?>
                    <td>&nbsp;Email : <i><?php echo $vrac->vendeur->emails[0]; ?></i></td>  
                    <?php else: ?>
                    <td>&nbsp;</td>
                    <?php endif; ?>
                </tr>
			</table>
		</td>
		<td width="50%" valign="top" >
			<span style="background-color: grey; color: white; font-weight: bold;">&nbsp;Acheteur&nbsp;</span><br/>
			<table cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #000;">
                                <tr>
                                        <td>&nbsp;<i><?php if($vrac->acheteur->intitule): ?><?php echo $vrac->acheteur->intitule ?>&nbsp;<?php endif; ?><?php echo truncate_text($vrac->acheteur->raison_sociale, 35, '...'); ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;<i><?php echo truncate_text($vrac->acheteur->adresse, 50, "...", false) ?></i><br/>
                                        <i>&nbsp;<?php echo $vrac->acheteur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->acheteur->commune; ?></i><br/></td>
                                </tr>
                                <tr>    
                                        <?php if($vrac->acheteur->cvi): ?>
                                        <td>&nbsp;CVI : <i><?php echo $vrac->acheteur->cvi; ?></i></td>
                                        <?php elseif($vrac->acheteur->civaba): ?>
                                        <td>&nbsp;CIVA : <i><?php echo $vrac->acheteur->civaba; ?></i></td> 
                                        <?php else: ?>
                                        <td>&nbsp;</td>
                                        <?php endif; ?>
                                </tr>
                                <tr>
                                        <td>&nbsp;Tel. : <i><?php echo formatPhone($vrac->acheteur->telephone); ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;SIRET : <i><?php echo $vrac->acheteur->siret; ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;N° d'accise : <i><?php echo $vrac->acheteur->num_accise; ?></i></td>
                                </tr>
                                <tr>
                                    <?php if(count($vrac->acheteur->emails->toArray(true, false)) > 0): ?>
                                        <td>&nbsp;Email : <i><?php echo $vrac->acheteur->emails[0]; ?></i></td>  
                                    <?php else: ?>
                                        <td>&nbsp;</td>
                                    <?php endif; ?>
                                </tr>
			</table>
		</td>
	</tr>
</table>
<br/>
<br/>
<span style="background-color: grey; color: white; font-weight: bold;">&nbsp;Courtier&nbsp;</span><br/>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100%">
			<table cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #000;">
				<tr>
					<td>&nbsp;<?php if($hasCourtier): ?><i><?php if($vrac->mandataire->intitule): ?><?php echo $vrac->mandataire->intitule ?>&nbsp;<?php endif; ?><?php echo truncate_text($vrac->mandataire->raison_sociale, 35); ?></i><?php endif; ?></td> 
					<td>N° de Carte Pro : <?php if($hasCourtier): ?><i><?php echo $vrac->mandataire->carte_pro; ?></i><?php endif; ?></td> 
				</tr>
				<tr>
					<td>&nbsp;<?php if($hasCourtier): ?><i><?php echo $vrac->mandataire->adresse ?></i><?php endif; ?></td>

					<td>SIRET : <?php if($hasCourtier): ?><i><?php echo $vrac->mandataire->siret ?></i><?php endif; ?></td>
				</tr>
                <tr>
                    <td>&nbsp;<?php if($hasCourtier): ?><i><?php echo $vrac->mandataire->code_postal; ?></i>&nbsp;<i><?php echo $vrac->mandataire->commune; ?></i><?php endif; ?></td>
                    <td><?php if($hasCourtier && $vrac->interlocuteur_commercial->email && $vrac->interlocuteur_commercial->telephone): ?><i><?php echo $vrac->interlocuteur_commercial->nom; ?></i><?php endif; ?></td>
                </tr>
				<tr>
					<td>&nbsp;Tel. : <?php if($hasCourtier): ?><i><?php echo formatPhone($vrac->mandataire->telephone); ?></i><?php endif; ?></td>
                    <td><?php if($hasCourtier && $vrac->interlocuteur_commercial->email && $vrac->interlocuteur_commercial->telephone): ?><i>(<?php echo $vrac->interlocuteur_commercial->email ?>, Tel. : <?php echo formatPhone($vrac->interlocuteur_commercial->telephone) ?>)</i><?php elseif($hasCourtier && $vrac->interlocuteur_commercial->email && !$vrac->interlocuteur_commercial->telephone): ?><i><?php echo $vrac->interlocuteur_commercial->nom; ?> (<?php echo $vrac->interlocuteur_commercial->email ?>)</i><?php elseif($hasCourtier && !$vrac->interlocuteur_commercial->email && $vrac->interlocuteur_commercial->telephone): ?><i><?php echo $vrac->interlocuteur_commercial->nom; ?> (Tel. <?php echo formatPhone($vrac->interlocuteur_commercial->telephone) ?>)</i><?php elseif($hasCourtier && $vrac->interlocuteur_commercial->nom): ?><i><?php echo $vrac->interlocuteur_commercial->nom; ?></i><?php endif; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
