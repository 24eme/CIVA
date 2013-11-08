<?php use_helper('vracExport') ?>
<br/>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="50%" valign="top">
			<span style="background-color: grey; color: white; font-weight: bold;">&nbsp;Vendeur&nbsp;</span><br/>
			<table cellpadding="0" cellspacing="0" border="0" width="99%" style="border: 1px solid #000;">
				<tr>
					<td>&nbsp;<i><?php echo truncate_text($vrac->vendeur->raison_sociale, 35); ?></i></td>
				</tr>
                                <tr>
                                        <td>&nbsp;<i><?php if($vrac->vendeur->intitule): ?><?php echo $vrac->vendeur->intitule ?>&nbsp;<?php endif; ?><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i><br/>
                                        <i>&nbsp;<?php echo $vrac->vendeur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->vendeur->commune; ?></i><br/></td>
                                </tr>
				<tr>
					<td>&nbsp;CVI : <i><?php echo $vrac->vendeur->cvi; ?></i></td>
				</tr>
				<tr>
					<td>&nbsp;Tel. : <i><?php echo $vrac->vendeur->telephone; ?></i></td>
				</tr>
				<tr>
					<td>&nbsp;SIRET : <i><?php echo $vrac->vendeur->siret; ?></i></td>
				</tr>
				<tr>
					<td>&nbsp;N° d'accise : <i><?php echo $vrac->vendeur->num_accise; ?></i></td>  
				</tr>
			</table>
		</td>
		<td width="50%" valign="top" >
			<span style="background-color: grey; color: white; font-weight: bold;">&nbsp;Acheteur&nbsp;</span><br/>
			<table cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #000;">
                                <tr>
                                        <td>&nbsp;<i><?php if($vrac->acheteur->intitule): ?><?php echo $vrac->acheteur->intitule ?>&nbsp;<?php endif; ?><?php echo truncate_text($vrac->acheteur->raison_sociale, 35); ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;<i><?php echo truncate_text($vrac->acheteur->adresse, 50, "...", false) ?></i><br/>
                                        <i>&nbsp;<?php echo $vrac->acheteur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->acheteur->commune; ?></i><br/></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;CVI : <i><?php echo $vrac->acheteur->cvi; ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;Tel. : <i><?php echo $vrac->acheteur->telephone; ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;SIRET : <i><?php echo $vrac->acheteur->siret; ?></i></td>
                                </tr>
                                <tr>
                                        <td>&nbsp;N° d'accise : <i><?php echo $vrac->acheteur->num_accise; ?></i></td>
                                </tr>
			</table>
		</td>
	</tr>
</table>
<br/>
<span style="background-color: grey; color: white; font-weight: bold;">&nbsp;Courtier&nbsp;</span><br/>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100%">
			<table cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #000;">
				<tr>
					<td>&nbsp;<i><?php if($vrac->mandataire->intitule): ?><?php echo $vrac->mandataire->intitule ?>&nbsp;<?php endif; ?><?php echo truncate_text($vrac->mandataire->raison_sociale, 35); ?></i></td> 
					<td>N° de Carte Pro : <i><?php echo $vrac->mandataire->carte_pro; ?></i></td> 
				</tr>
				<tr>
					<td>&nbsp;<i><?php echo $vrac->mandataire->adresse ?></i><br/>
                                        &nbsp;<i><?php echo $vrac->mandataire->code_postal; ?></i>&nbsp;<i><?php echo $vrac->mandataire->commune; ?></i></td>

					<td>SIRET : <i><?php echo $vrac->mandataire->siret ?></i></td>
				</tr>
				<tr>
					<td>&nbsp;Tel : <i><?php echo $vrac->mandataire->telephone; ?></i></td>
                    <td><i><?php echo $vrac->interlocuteur_commercial->nom; ?><?php if ($vrac->interlocuteur_commercial->email): ?>&nbsp;&nbsp;(<?php echo $vrac->interlocuteur_commercial->email ?>)<?php endif; ?></i></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
