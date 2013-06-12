<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php include_partial("ds_export/exploitation", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>

<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle ?></span><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau)) ?>
<?php endforeach; ?>

<?php $totals = array('total_stock' => $ds->getTotalAOCByType('total_stock'),
                      'total_normal' => $ds->getTotalAOCByType('total_normal'),
                      'total_vt' => $ds->getTotalAOCByType('total_vt'),
                      'total_sgn' => $ds->getTotalAOCByType('total_sgn')); ?>

<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <th style="width: 306px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">Total</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">hors VT et SGN</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">VT</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">SGN</th>
</tr>
<tr>
  <?php foreach($totals as $volume): ?>
  <td style="border: 1px solid black; <?php if(!$is_last_page): ?>background-color: #bbb;<?php endif; ?>"><?php $is_last_page ? echoVolume($volume, true) : echoVolume(null, true) ?></td>
  <?php endforeach ?>
</tr>
</table>

<?php if($autres): ?>
  <small><br /></small>
  <span style="background-color: black; color: white; font-weight: bold;">Autres Produits (tous lieux de stockage confondus)</span><br />
  <table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
  <?php foreach($autres as $libelle => $volume): ?>
  <tr>
    <td style="text-align: left; width: 306px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
    <td style="width: 110px; border: 1px solid black;<?php if(!$is_last_page): ?>background-color: #bbb;<?php endif; ?>"><?php $is_last_page ? echoVolume($volume, true) : echoVolume(null, true) ?></td>
  </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php if($is_last_page && $agrega_total): ?>
  <small><br /></small>
  <span style="background-color: black; color: white; font-weight: bold;">Récapitulatif (tous lieux de stockage confondus)</span><br />
  <table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
  <tr>
    <th style="width: 202px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">Appellations</th>
    <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">Total</th>
    <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">hors VT et SGN</th>
    <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">VT</th>
    <th style="width: 110px; font-weight: bold; border: 1px solid black; background-color: black; color: white;">SGN</th>
  </tr>
  <?php foreach($agrega_total as $item): ?>
  <tr>
    <td style="text-align: left; width: 202px; border: 1px solid black; ">&nbsp;<?php echo str_replace("TOTAL ", "", $item->nom) ?></td>
    <td style="width: 110px; border: 1px solid black;<?php if(!$is_last_page): ?>background-color: #bbb;<?php endif; ?>"><?php $is_last_page ? echoVolume($item->volume_total, true) : echoVolume(null, true) ?></td>
    <td style="width: 110px; border: 1px solid black;<?php if(!$is_last_page): ?>background-color: #bbb;<?php endif; ?>"><?php $is_last_page ? echoVolume($item->volume_normal, true) : echoVolume(null, true) ?></td>
    <td style="width: 110px; border: 1px solid black;<?php if(!$is_last_page): ?>background-color: #bbb;<?php endif; ?>"><?php $is_last_page ? echoVolume($item->volume_vt, true) : echoVolume(null, true) ?></td>
    <td style="width: 110px; border: 1px solid black;<?php if(!$is_last_page): ?>background-color: #bbb;<?php endif; ?>"><?php $is_last_page ? echoVolume($item->volume_sgn, true) : echoVolume(null, true) ?></td>
  </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>

