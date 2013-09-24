<?php use_helper('vracExport') ?>
<br/>
<br/>
<table border="0">
    <tr>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;VENDEUR&nbsp;</span><br/>
            <table style="border: 1px solid grey; width: 310px;">
                            <tr>
                                <td>
                                    <table border="0" style="padding: 0; margin: 0;">
                                        <tr>
                                            <td style="text-align: right;">
                                                &nbsp;<?php echo boldFamille($vrac->vendeur, 'Recoltant', 'Récoltant'); ?>&nbsp;<?php if(checkedFamille($vrac->vendeur, 'Recoltant')): ?>☒&nbsp;<?php else: ?>☐&nbsp;<?php endif; ?>
                                            </td> 

                                            <td style="text-align: right;">
                                                &nbsp;<?php echo boldFamille($vrac->vendeur, 'Negociant', 'Négociant'); ?>&nbsp;<?php if(checkedFamille($vrac->vendeur, 'Negociant')): ?>☒&nbsp;<?php else: ?>☐&nbsp;<?php endif; ?>
                                            </td>

                                            <td style="text-align: right;">
                                                &nbsp;<?php echo boldFamille($vrac->vendeur, 'CCV', 'Cave cop'); ?>&nbsp;<?php if(checkedFamille($vrac->vendeur, 'CCV')): ?>☒&nbsp;<?php else: ?>☐&nbsp;<?php endif; ?>
                                            </td>
                                        </tr>            
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;Raison Sociale : <i><?php echo truncate_text($vrac->vendeur->raison_sociale); ?></i></td>
                            </tr>
                            <tr>
                                <td >&nbsp;N° CVI : <i><?php echo $vrac->vendeur->cvi; ?></i></td>
                            </tr>
                            <tr>
                                <td >&nbsp;Adresse : <i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i></td>
                            </tr>
                            <tr style="text-align: left; padding: 0; margin: 0;">
                                <td >
                                <table border="0" style="padding: 0; margin: 0;">
                                    <tr>
                                        <td>&nbsp;Code postal : <i><?php echo $vrac->vendeur->code_postal; ?></i></td>
                                        <td>&nbsp;Localité : <i><?php echo $vrac->vendeur->commune; ?></i></td>
                                    </tr>            
                                </table>
                                </td>
                            </tr>
                <tr>
                    <td >&nbsp;Téléphone : <i><?php if (!$vrac->vendeur->telephone): ?>Tel. :<?php else: ?>Tel. <?php echo $vrac->vendeur->telephone; ?><?php endif; ?></i></td>
                </tr>
                <tr>
                    <td >&nbsp;SIRET : <i><?php echo $vrac->vendeur->siret; ?></i></td>
                </tr>
            </table>
        </td>
        <td>
            <span style="background-color: grey; color: white; font-weight: bold;">&nbsp;ACHETEUR&nbsp;</span><br/>
            <table style="border: 1px solid grey; margin: 0; width: 310px;">
                            <tr>
                                <td>
                                    <table border="0" style="padding: 0; margin: 0;">
                                        <tr>
                                            <td style="text-align: right;">
                                                &nbsp;<?php echo boldFamille($vrac->acheteur, 'Recoltant', 'Récoltant'); ?>&nbsp;<?php if(checkedFamille($vrac->acheteur, 'Recoltant')): ?>☒&nbsp;<?php else: ?>☐&nbsp;<?php endif; ?>
                                            </td> 

                                            <td style="text-align: right;">
                                                &nbsp;<?php echo boldFamille($vrac->acheteur, 'Negociant', 'Négociant'); ?>&nbsp;<?php if(checkedFamille($vrac->acheteur, 'Negociant')): ?>☒&nbsp;<?php else: ?>☐&nbsp;<?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                &nbsp;<?php echo boldFamille($vrac->acheteur, 'CCV', 'Cave cop'); ?>&nbsp;<?php if(checkedFamille($vrac->acheteur, 'CCV')): ?>☒&nbsp;<?php else: ?>☐&nbsp;<?php endif; ?>
                                            </td>
                                        </tr>            
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td >&nbsp;Raison Sociale : <i><?php echo truncate_text($vrac->acheteur->raison_sociale); ?></i></td>
                            </tr>
                            <tr>
                                <td >&nbsp;N° CVI : <i><?php echo $vrac->acheteur->cvi; ?></i></td>
                            </tr>
                            <tr>
                                <td >&nbsp;Adresse : <i><?php echo truncate_text($vrac->acheteur->adresse, 50, "...", false) ?></i></td>
                            </tr>
                            <tr>
                                <td >
                                <table border="0" style="padding: 0; margin: 0;">
                                    <tr>
                                        <td>&nbsp;Code postal : <i><?php echo $vrac->acheteur->code_postal; ?></i></td>
                                        <td>&nbsp;Localité : <i><?php echo $vrac->acheteur->commune; ?></i></td>
                                    </tr>            
                                </table>
                                </td>
                            </tr>
                <tr>
                    <td >&nbsp;Téléphone : <i><?php if (!$vrac->acheteur->telephone): ?>Tel. :<?php else: ?>Tel. <?php echo $vrac->acheteur->telephone; ?><?php endif; ?></i></td>
                </tr>
                <tr>
                    <td >&nbsp;SIRET : <i><?php echo $vrac->acheteur->siret; ?></i></td>
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
                            </tr>
                            
                            <tr>
                                <td >&nbsp;N° Carte Professionnelle : <i><?php echo $vrac->vendeur->cvi; ?></i></td>
                            </tr>
                            <tr>
                                <td >&nbsp;Adresse : <i><?php echo truncate_text($vrac->vendeur->adresse, 50, "...", false) ?></i></td>
                            </tr>
                            <tr>
                                <td >
                                <table border="0" style="padding: 0; margin: 0;">
                                    <tr>
                                        <td>&nbsp;Code postal : <i><?php echo $vrac->vendeur->code_postal; ?></i></td>
                                        <td>&nbsp;Localité : <i><?php echo $vrac->vendeur->commune; ?></i></td>
                                    </tr>            
                                </table>
                                </td>
                            </tr>
                <tr>
                    <td >&nbsp;Téléphone : <i><?php if (!$vrac->vendeur->telephone): ?>Tel. :<?php else: ?>Tel. <?php echo $vrac->vendeur->telephone; ?><?php endif; ?></i></td>
                </tr>
                <tr>
                    <td >&nbsp;SIRET : <i><?php echo $vrac->vendeur->siret; ?></i></td>
                </tr>
            </table>
        </td>
    </tr>
</table>