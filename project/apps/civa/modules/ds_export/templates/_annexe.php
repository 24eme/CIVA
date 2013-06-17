<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php include_partial("ds_export/exploitation", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>
<?php include_partial("ds_export/stockage", array('ds' => $ds, 'tiers' => $ds->getEtablissement())) ?>

<h2 style="text-align: center;">ANNEXE : Détail de l'AOC Alsace</h2>

<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle ?></span><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau)) ?>
<?php endforeach; ?>