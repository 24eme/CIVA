<?php use_helper('Text') ?>
<span style="background-color: grey; color: white; font-weight: bold;">Lieu de stockage</span><br />
<table style="border: 1px solid grey; margin: 0;"><tr><td>
<table style="margin: 0" border="0">
  <tr>
    <td style="width: 430px;">&nbsp;Numéro : <i><?php echo substr($ds->stockage->numero, 0, 10) ?>&nbsp;<b><?php echo substr($ds->stockage->numero, -3) ?></b></i></td>
    <td><?php if($ds->isDSPrincipale()): ?>☒&nbsp;<b>Principal</b><?php else: ?>☐ &nbsp;Principal<?php endif; ?>&nbsp;&nbsp;<?php if(!$ds->isDSPrincipale()): ?>☒&nbsp;<b>Secondaire</b><?php else: ?>☐&nbsp;Secondaire<?php endif; ?></td>
</tr>
  <tr><td colspan="2">&nbsp;Adresse complète : <i><?php echo truncate_text($ds->stockage->nom.', '.$ds->stockage->adresse. ", ".$ds->stockage->code_postal." ".$ds->stockage->commune, 90, "...", false) ?></i></td></tr>
</table>
</td></tr></table>