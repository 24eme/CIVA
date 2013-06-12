<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php include_partial("ds_export/exploitation", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>
<?php include_partial("ds_export/stockage", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>
<small><br /></small>
<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white;"><b><?php echo $libelle ?></b> (détail de l'AOC Alsace Blanc)</span><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau)) ?>
<?php endforeach; ?>