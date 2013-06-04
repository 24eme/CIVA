<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>

<span style="background-color: grey; color: white; font-weight: bold;">Exploitation</span><br/>
<table style="border: 1px solid grey;"><tr><td>
<table border="0">
  <tr><td>&nbsp;N° CVI : <i>7523700100</i></td><td>Nom : <i>GAEC ACTUALYS JEAN</i></td></tr>
  <tr><td>&nbsp;SIRET : <i>34093842600019</i></td><td>Adresse : <i>15 RUE DES TROIS EPIS</i></td></tr>
  <tr><td>&nbsp;Tel. : <i></i></td><td>Commune : <i>75230 PARIS</i></td></tr>
  <tr><td>&nbsp;Fax : <i></i></td><td>&nbsp;</td></tr>
</table>
</td></tr></table>

<span style="background-color: grey; color: white; font-weight: bold;">Gestionnaire de l'exploitation</span><br/>
<table style="border: 1px solid grey; margin: 0;"><tr><td>
<table border="0">
  <tr><td>&nbsp;Nom et prénom : <i>ACTUALYS JEAN</i></td><td>Né(e) le <i>30/11/1933</i></td></tr>
  <tr><td>&nbsp;Adresse complete : <i>15 RUE DES TROIS EPIS, 75230 PARIS</i></td><td>Tel. <i></i></td></tr>
</table>
</td></tr></table>
<span style="background-color: grey; color: white; font-weight: bold;">Lieu de stockage</span><table style="text-align:left; width: 300px;" border="0" cellspacing=0 cellpadding=0><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;☒ Principal&nbsp;&nbsp;☐ Secondaire</td></tr></table>
<small><br /></small>
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
  <td style= border: 1px solid black; <?php if(!$total): ?>background-color: #bbb;<?php endif; ?>"><?php $total ? echoVolume($volume, true) : echoVolume(null, true) ?></td>
  <?php endforeach ?>
</tr>
</table>

<?php $autres = array("Moûts concentrés rectifiés" => $ds->mouts, 
                      "Vins de table - Vins sans IG" => $ds->getTotalVinSansIg(), 
                      "Vins de table mousseux" => $ds->getTotalMousseuxSansIg(), 
                      "Rebêches" => $ds->rebeches, 
                      "Dépassement de PLC" => $ds->dplc, 
                      "Lie en stocks" => $ds->lies); ?>

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
