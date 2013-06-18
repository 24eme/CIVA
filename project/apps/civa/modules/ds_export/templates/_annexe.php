<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<small><br /></small>
<?php include_partial("ds_export/stockage", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>
<br />
<span style="text-align: center; font-size: 12pt; font-weight:bold;">ANNEXE : Détail de l'AOC Alsace</span>
<br /><br />
<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle ?></span><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau)) ?>
<?php endforeach; ?>