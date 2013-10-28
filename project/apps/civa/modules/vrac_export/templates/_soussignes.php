<?php use_helper('vracExport') ?>
<br/>
<br/>
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
	<tr>
		<td width="50%" valign="top" style="padding-right: 10px;">
			<span style="display:block; background-color: grey; color: white; font-weight: bold; padding: 5px; text-align: center; width: 120px;">&nbsp;VENDEUR&nbsp;</span>
			<table cellpadding="5" cellspacing="0" border="0" width="100%" style="border: 1px solid grey;">
				<tr>
					<td valign="top" width="100px">Raison Sociale :</td>
					<td><i><?php echo truncate_text($vrac->vendeur->raison_sociale); ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">N° CVI :</td>
					<td><i><?php echo $vrac->vendeur->cvi; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">Adresse :</td>
					<td><i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px"></td>
					<td><i><?php echo $vrac->vendeur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->vendeur->commune; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">Tel. :</td>
					<td><i><?php echo $vrac->vendeur->telephone; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">SIRET :</td>
					<td><i><?php echo $vrac->vendeur->siret; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">N° d'Accise :</td>
					<td><i><?php echo $vrac->vendeur->num_accise; ?></i></td>  
				</tr>
			</table>
		</td>
		<td width="50%" valign="top">
			<span style="display: block; background-color: grey; color: white; font-weight: bold; padding: 5px; text-align: center; width: 120px;">&nbsp;ACHETEUR&nbsp;</span>
			<table cellpadding="5" cellspacing="0" width="100%" style="border: 1px solid grey;">
				<tr>
					<td valign="top" width="100px">Raison Sociale :</td>
					<td><i><?php echo $vrac->acheteur->raison_sociale; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">N° CVI :</td>
					<td><i><?php echo $vrac->acheteur->cvi; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">Adresse :</td>
					<td><i><?php echo $vrac->acheteur->adresse ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px"></td>
					<td><i><?php echo $vrac->acheteur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->acheteur->commune; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">Tel. :</td>
					<td><i><?php echo $vrac->acheteur->telephone; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">SIRET :</td>
					<td><i><?php echo $vrac->acheteur->siret; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">N° d'Accise :</td>
					<td><i><?php echo $vrac->acheteur->num_accise; ?></i></td>  
				</tr>
			</table>
		</td>
	</tr>
</table>
<br/>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100%">
			<span style="display: block; background-color: grey; color: white; font-weight: bold; padding: 5px; text-align: center; width: 120px;">&nbsp;COURTIER&nbsp;</span>
			<table cellpadding="5" cellspacing="0" width="100%" style="border: 1px solid grey;">
				<tr>
					<td valign="top" width="100px">Raison Sociale :</td>
					<td><i><?php echo $vrac->mandataire->raison_sociale; ?></i></td> 
					<td valign="top" width="150px">N° Carte Pro :</td>
					<td><i><?php echo $vrac->mandataire->carte_pro; ?></i></td> 
				</tr>
				<tr>
					<td valign="top" width="100px">Adresse :</td>
					<td><i><?php echo $vrac->mandataire->adresse ?></i></td>
					<td valign="top" width="150px">SIRET :</td>
					<td><i><?php echo $vrac->mandataire->siret ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px"></td>
					<td><i><?php echo $vrac->mandataire->code_postal; ?></i>&nbsp;<i><?php echo $vrac->mandataire->commune; ?></i></td>
					<td valign="top" width="150px">Interlocuteur Comm. :</td>
					<td><i><?php echo $vrac->interlocuteur_commercial; ?></i></td>
				</tr>
				<tr>
					<td valign="top" width="100px">Tel. :</td>
					<td><i><?php echo $vrac->mandataire->telephone; ?></i></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</td>
	</tr>
</table>