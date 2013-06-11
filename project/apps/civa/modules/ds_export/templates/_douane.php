<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php include_partial("ds_export/exploitation", array('ds' => $ds)) ?>

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
  <td style="border: 1px solid black; <?php if(!$total): ?>background-color: #bbb;<?php endif; ?>"><?php $total ? echoVolume($volume, true) : echoVolume(null, true) ?></td>
  <?php endforeach ?>
</tr>
</table>

<?php $autres = array("Moûts concentrés rectifiés" => $ds->mouts, 
                      "Vins sans IG (Vins de table)" => $ds->getTotalVinSansIg(), 
                      "Vins sans IG mousseux" => $ds->getTotalMousseuxSansIg(), 
                      "Rebêches" => $ds->rebeches, 
                      "Dépassements de rendement" => $ds->dplc, 
                      "Lies en stocks" => $ds->lies); ?>

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">Autres Produits</span><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<?php foreach($autres as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 306px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 110px; border: 1px solid black;<?php if(!$total): ?>background-color: #bbb;<?php endif; ?>"><?php $total ? echoVolume($volume, true) : echoVolume(null, true) ?></td>
</tr>
<?php endforeach; ?>
</table>