<?php use_helper('vracExport') ?>
<br/>
<br/>
<table border="0">
    <tr>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;VENDEUR&nbsp;</span><br/>
            <table style="border: 1px solid grey; width: 310px;">
                            <tr>
                                <td valign="top" width="90px">Raison Sociale :</td>
                                <td><i><?php echo truncate_text($vrac->vendeur->raison_sociale); ?></i></td>
                            </tr>
                            <tr>
                                <td valign="top" width="90px">N° CVI :</td>
                                <td><i><?php echo $vrac->vendeur->cvi; ?></i></td>
                            </tr>
                            <tr>
                                <td valign="top" width="90px">Adresse :</td>
								<td><i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i></td>
                            </tr>
                            <tr>
                            	<td valign="top" width="90px"></td>
                                <td><i><?php echo $vrac->vendeur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->vendeur->commune; ?></i></td>
                            </tr>
                			<tr>
                            	<td valign="top" width="90px">Tel. :</td>
                    			<td><i><?php echo $vrac->vendeur->telephone; ?></i></td>
                			</tr>
                			<tr>
	                			<td valign="top" width="90px">SIRET :</td>
	                			<td><i><?php echo $vrac->vendeur->siret; ?></i></td>
	                		</tr>
	                		<tr>
	                			<td valign="top" width="90px">N° d'Accise :</td>
	                			<td><i><?php echo $vrac->vendeur->num_accise; ?></i></td>  
	                		</tr>
            </table>
        </td>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;ACHETEUR&nbsp;</span><br/>
            <table style="border: 1px solid grey; width: 310px;">
                            <tr>
                                <td valign="top" width="90px">Raison Sociale :</td>
                                <td><i><?php echo $vrac->acheteur->raison_sociale; ?></i></td>
                            </tr>
                            <tr>
                                <td valign="top" width="90px">N° CVI :</td>
                                <td><i><?php echo $vrac->acheteur->cvi; ?></i></td>
                            </tr>
                            <tr>
                                <td valign="top" width="90px">Adresse :</td>
								<td><i><?php echo $vrac->acheteur->adresse ?></i></td>
                            </tr>
                            <tr>
                            	<td valign="top" width="90px"></td>
                                <td><i><?php echo $vrac->acheteur->code_postal; ?></i>&nbsp;<i><?php echo $vrac->acheteur->commune; ?></i></td>
                            </tr>
                			<tr>
                            	<td valign="top" width="90px">Tel. :</td>
                    			<td><i><?php echo $vrac->acheteur->telephone; ?></i></td>
                			</tr>
                			<tr>
	                			<td valign="top" width="90px">SIRET :</td>
	                			<td><i><?php echo $vrac->acheteur->siret; ?></i></td>
	                		</tr>
	                		<tr>
	                			<td valign="top" width="90px">N° d'Accise :</td>
	                			<td><i><?php echo $vrac->acheteur->num_accise; ?></i></td>  
	                		</tr>
            </table>
        </td>
    </tr>
</table>
<br/>
<table border="0">
    <tr>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;COURTIER&nbsp;</span><br/>
            <table style="border: 1px solid grey; width: 630px;">
            				<tr>
	                			<td valign="top" width="90px">Raison Sociale :</td>
	                			<td><i><?php echo $vrac->mandataire->raison_sociale; ?></i></td> 
	                			<td valign="top" width="90px">N° Carte Pro :</td>
	                			<td><i><?php echo $vrac->mandataire->carte_pro; ?></i></td> 
            				</tr>
                            <tr>
                                <td valign="top" width="90px">Adresse :</td>
								<td><i><?php echo $vrac->mandataire->adresse ?></i></td>
                                <td valign="top" width="90px">SIRET :</td>
								<td><i><?php echo $vrac->mandataire->siret ?></i></td>
                            </tr>
                            <tr>
                            	<td valign="top" width="90px"></td>
                                <td><i><?php echo $vrac->mandataire->code_postal; ?></i>&nbsp;<i><?php echo $vrac->mandataire->commune; ?></i></td>
                            	<td valign="top" width="90px">Interlocuteur Comm. :</td>
                    			<td><i><?php echo $vrac->interlocuteur_commercial; ?></i></td>
                            </tr>
                			<tr>
                            	<td valign="top" width="90px">Tel. :</td>
                    			<td><i><?php echo $vrac->mandataire->telephone; ?></i></td>
                    			<td></td>
                    			<td></td>
                			</tr>
            </table>
        </td>
    </tr>
</table>