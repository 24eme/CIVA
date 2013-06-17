<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php include_partial("ds_export/exploitation", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>
<h2 style="text-align: center;">ANNEXE : Récapitulatif DRM <small><br />(tous lieux de stockage confondus)</small></h2>


<small><br /></small>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <th style="width: 198px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">Appellations</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">Total</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">hors VT et SGN</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">VT</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">SGN</th>
</tr>
<?php foreach($recap_total as $item): ?>
<tr>
  <td style="text-align: left; width: 198px; border: 1px solid black; ">&nbsp;<?php echo str_replace("TOTAL ", "", $item->nom) ?></td>
  <td style="width: 110px; border: 1px solid black;"><?php echoVolume($item->volume_total, true) ?></td>
  <td style="width: 110px; border: 1px solid black;"><?php echoVolume($item->volume_normal, true) ?></td>
  <td style="width: 110px; border: 1px solid black;"><?php echoVolume($item->volume_vt, true) ?></td>
  <td style="width: 110px; border: 1px solid black;"><?php echoVolume($item->volume_sgn, true) ?></td>
</tr>
<?php endforeach; ?>
</table>

<small><br /></small>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<?php foreach($recap_autres as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 306px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 110px; border: 1px solid black;"><?php echoVolume($volume, true) ?></td>
</tr>
<?php endforeach; ?>
</table>

<small><br /></small>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<?php foreach($recap_vins_sans_ig as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 306px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 110px; border: 1px solid black;"><?php echoVolume($volume, true) ?></td>
</tr>
<?php endforeach; ?>
</table>
