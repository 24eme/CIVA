<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<style>
table {
  padding-left: 0px;
}
</style>
<small><br /></small>
<?php include_partial("ds_export/exploitation", array('ds' => $ds)) ?>
<?php include_partial("ds_export/stockage", array('ds' => $ds)) ; ?>
<br />
<br />
<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle ?></span><?php if(preg_match('/Alsace blanc/i', $libelle)): ?><span>&nbsp;(hors Lieux-dits et Communales)</span><?php endif; ?><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau, 'empty' => true)) ?>
<?php endforeach; ?>
