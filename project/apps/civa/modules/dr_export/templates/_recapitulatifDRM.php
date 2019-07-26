<?php use_helper('Float'); ?>
<?php use_helper('drExport'); ?>
<style>
table {
  padding-left: 0px;
}
</style>
<br /><br />
<span style="text-align: center; font-size: 12pt; font-weight:bold;">RÉCAPITULATIF</span>
<br /><br />

<?php if($dr->recolte->canCalculVolumeRevendiqueSurPlace()): ?>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<tr>
  <th style="width: 214px; border: 1px solid black; background-color: black; color: white; font-weight: bold; border: 1px solid black; text-align: left;">&nbsp;Appellations</th>
  <th style="width: 200px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;">Volume revendiqué <br /><small>sur place</small></th>
  <th style="width: 200px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;"><?php if($dr->campagne < "2015"): ?>Usages industriels<?php else: ?>Volume à détruire<?php endif; ?><br /><small>sur place</small></th>
  <?php if($dr->recolte->canHaveVci()): ?>
  <th style="width: 200px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;">VCI sur place<br /><small>à ajouter dans Volume à détruire</small></th>
  <?php endif; ?>
</tr>
<?php foreach($recap_total as $item): ?>
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo str_replace("TOTAL ", "", $item->nom) ?></td>
  <?php if(!is_null($item->revendique_sur_place)): ?>
  <td style="width: 200px; border: 1px solid black;"><?php echoVolume($item->revendique_sur_place, true) ?></td>
  <td style="width: 200px; border: 1px solid black;"><?php echoVolume($item->usages_industriels_sur_place, true) ?></td>
  <?php if($dr->recolte->canHaveVci()): ?>
  <td style="width: 200px; border: 1px solid black;"><?php echoVolume($item->vci_sur_place, true) ?></td>
  <?php endif; ?>
  <?php else: ?>
  <td style="border: 1px solid black; text-align: center;" colspan="<?php if($dr->recolte->canHaveVci()): ?>3<?php else: ?>2<?php endif; ?>"><i>Néant</i></td>
  <?php endif; ?>
</tr>
<?php endforeach; ?>
</table>

<small><br /></small>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold; background-color: black; color: white;">&nbsp;Total Général AOC</td>
  <?php if($total["revendique_sur_place"] > 0 || $total["usages_industriels_sur_place"] > 0): ?>
    <td style="width: 200px; border: 1px solid black;"><?php echoVolume($total["revendique_sur_place"], true) ?></td>
    <td style="width: 200px; border: 1px solid black;"><?php echoVolume($total["usages_industriels_sur_place"], true) ?></td>
    <?php if($dr->recolte->canHaveVci()): ?>
    <td style="width: 200px; border: 1px solid black;"><?php echoVolume($total["vci_sur_place"], true) ?></td>
    <?php endif; ?>
  <?php else: ?>
    <td style="width: 400px; border: 1px solid black; text-align:center;"><i>Néant</i></td>
  <?php endif; ?>
</tr>
</table>
<?php else: ?>
<span>Le tableau du récapitulatif des AOC ne peut pas être affiché : les informations sur les volumes en dépassement des acheteurs sont inconnus</span>
<br />
<?php endif; ?>
<br />
<br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold;">&nbsp;Rebêches
</td>
  <td style="width: 200px; border: 1px solid black;"><?php echoVolume($dr->recolte->getSurPlaceRebeches(), true) ?></td>
</tr>
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold;">&nbsp;Vins Sans IG</td>
  <?php if($dr->recolte->exist('certification/genre/appellation_VINTABLE')): ?>
  <td style="width: 200px; border: 1px solid black;"><?php echoVolume($dr->recolte->certification->genre->appellation_VINTABLE->getTotalCaveParticuliere(), true) ?></td>
  <?php else: ?>
  <td style="width: 200px; border: 1px solid black; text-align:center;"><i>Néant</i></td>
  <?php endif; ?>
</tr>
</table>
