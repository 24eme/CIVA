<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<style>
table {
  padding-left: 0px;
}
</style>
<br /><br />
<span style="text-align: center; font-size: 12pt; font-weight:bold;">RÉCAPITULATIF<small><br />(tous lieux de stockage confondus)</small></span>
<br /><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<tr>
  <th style="width: 214px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: left;">Appellations</th>
  <th style="width: 106px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;">Total</th>
  <th style="width: 106px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;">hors VT et SGN</th>
  <th style="width: 106px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;">VT</th>
  <th style="width: 106px; font-weight: bold; border: 1px solid black; background-color: black; color: white; text-align: center;">SGN</th>
</tr>
<?php foreach($recap_total as $item): ?>
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo str_replace("TOTAL ", "", $item->nom) ?></td>
  <?php if(!is_null($item->volume_total)): ?>
  <td style="width: 106px; border: 1px solid black;"><?php echoVolume($item->volume_total, true) ?></td>
  <td style="width: 106px; border: 1px solid black; <?php if(is_null($item->volume_normal)): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($item->volume_normal, true) ?></td>
  <td style="width: 106px; border: 1px solid black; <?php if(is_null($item->volume_vt)): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($item->volume_vt, true) ?></td>
  <td style="width: 106px; border: 1px solid black; <?php if(is_null($item->volume_sgn)): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($item->volume_sgn, true) ?></td>
  <?php else: ?>
  <td style="border: 1px solid black; text-align: center;" colspan="4"><i>Néant</i></td>
  <?php endif; ?>
</tr>
<?php endforeach; ?>
</table>

<small><br /></small>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold; background-color: black; color: white;">&nbsp;Total Général AOC</td>
  <?php $volume_total_aoc = DSCivaClient::getInstance()->getTotalAOC($ds); ?>
  <?php if($volume_total_aoc > 0): ?>
    <td style="width: 106px; border: 1px solid black;"><?php echoVolume(DSCivaClient::getInstance()->getTotalAOC($ds), true) ?></td>
  <?php else: ?>
    <td style="width: 106px; border: 1px solid black; text-align:center; font-weight: bold;"><i>Néant</i></td>
  <?php endif; ?>
</tr>
</table>
<br />
<br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<?php foreach($recap_autres as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 106px; border: 1px solid black;"><?php echoVolume($volume, true) ?></td>
</tr>
<?php endforeach; ?>
</table>

<small><br /></small>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<?php foreach($recap_vins_sans_ig as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 214px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 106px; border: 1px solid black;"><?php echoVolume($volume, true) ?></td>
</tr>
<?php endforeach; ?>
</table>
