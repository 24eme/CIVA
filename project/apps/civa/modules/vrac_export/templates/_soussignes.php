<?php use_helper('vracExport') ?>
<br/>
<br/>
<table border="0">
    <tr>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;VENDEUR&nbsp;</span><br/>
            <table style="border: 1px solid grey; width: 310px;">
                            <tr>
                                <td colspan="2">&nbsp;Raison Sociale : <i><?php echo truncate_text($vrac->vendeur->raison_sociale); ?></i></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;N° CVI : <i><?php echo $vrac->vendeur->cvi; ?></i></td>

                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;Adresse : <i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i></td>

                            </tr>
                            <tr style="text-align: left; padding: 0; margin: 0;">
                                <td><i><?php echo $vrac->vendeur->code_postal; ?></i></td>
                                <td><i><?php echo $vrac->vendeur->commune; ?></i></td>
                            </tr>
                <tr>
                    <td colspan="2">&nbsp;<i><?php if (!$vrac->vendeur->telephone): ?>Tel. :<?php else: ?>Tel. <?php echo $vrac->vendeur->telephone; ?><?php endif; ?></i></td>

                </tr>
                <tr style="text-align: left; padding: 0; margin: 0;">
                    <td >SIRET : <i><?php echo $vrac->vendeur->siret; ?></i></td>
                    <td >N° d'Accise : <i><?php echo $vrac->vendeur->siret; ?></i></td>                                    
                </tr>
            </table>
        </td>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;ACHETEUR&nbsp;</span><br/>
            <table style="border: 1px solid grey; margin: 0; width: 310px;">
                            <tr>
                                <td colspan="2">&nbsp;Raison Sociale : <i><?php echo truncate_text($vrac->acheteur->raison_sociale); ?></i></td> 
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;N° CVI : <i><?php echo $vrac->acheteur->cvi; ?></i></td> 

                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;Adresse : <i><?php echo truncate_text($vrac->acheteur->adresse, 50, "...", false) ?></i></td>

                            </tr>
                            <tr>
                                <td ><i><?php echo $vrac->acheteur->code_postal; ?></i></td>
                                <td><i><?php echo $vrac->acheteur->commune; ?></i></td>
                            </tr>
                <tr>
                    <td colspan="2">&nbsp;<i><?php if (!$vrac->acheteur->telephone): ?>Tel. :<?php else: ?>Tel. <?php echo $vrac->acheteur->telephone; ?><?php endif; ?></i></td>

                </tr>
                <tr style="text-align: left; padding: 0; margin: 0;">
                    <td >&nbsp;SIRET : <i><?php echo $vrac->acheteur->siret; ?></i></td>
                    <td >&nbsp;N° d'Accise : <i><?php echo $vrac->acheteur->siret; ?></i></td>
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
                                <td>&nbsp;Raison Sociale : <i><?php echo truncate_text($vrac->vendeur->raison_sociale); ?></i></td>
                                <td>&nbsp;Interlocuteur Comm : <i><?php echo truncate_text("MONSIEUR MICHMUCH"); ?></i></td>
                            </tr>
                            
                            <tr>
                                <td >&nbsp;N° Carte Pro : <i><?php echo $vrac->vendeur->cvi; ?></i></td>
                                <td >&nbsp;Adresse : <i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i></td>
                            </tr>
                            <tr>
                                <td ><i>&nbsp;<?php if (!$vrac->vendeur->telephone): ?>Tel. :<?php else: ?>Tel. <?php echo $vrac->vendeur->telephone; ?><?php endif; ?></i></td>
                                <td>
                                    <table border="0" style="padding: 0; margin: 0;">
                                        <tr>
                                            <td><i><?php echo $vrac->vendeur->code_postal; ?></i></td>
                                            <td><i><?php echo $vrac->vendeur->commune; ?></i></td>
                                        </tr>            
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td >&nbsp;SIRET : <i><?php echo $vrac->vendeur->siret; ?></i></td>
                                <td>&nbsp;</td>
                            </tr>
            </table>
        </td>
    </tr>
</table>