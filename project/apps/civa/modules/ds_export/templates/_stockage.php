<?php use_helper('dsExport') ?>
<span style="background-color: grey; color: white; font-weight: bold;">Lieu de stockage</span><br />
<table style="border: 1px solid grey; margin: 0;"><tr><td>
<table style="margin: 0" border="0">
  <tr>
    <td style="width: 420px;">&nbsp;Numéro : <i><?php echo (!$ds->isAjoutLieuxDeStockage())? substr($ds->stockage->numero, 0, 10) : ""; ?>&nbsp;<b><?php echo substr($ds->stockage->numero, -3) ?></b></i></td>
    <td><?php if($ds->isDSPrincipale()): ?><span style="font-family: Dejavusans">☒</span>&nbsp;<b>Principal</b><?php else: ?><span style="font-family: Dejavusans">☐</span> &nbsp;Principal<?php endif; ?>&nbsp;&nbsp;<?php if(!$ds->isDSPrincipale()): ?><span style="font-family: Dejavusans">☒</span>&nbsp;<b>Secondaire</b><?php else: ?><span style="font-family: Dejavusans">☐</span>&nbsp;Secondaire<?php endif; ?></td>
</tr>
  <tr><td colspan="2">&nbsp;Adresse : <i><?php echo truncate_text($ds->stockage->adresse. ", ".$ds->stockage->code_postal." ".$ds->stockage->commune, 90) ?></i></td></tr>
</table>
</td></tr></table>