<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<small><br /></small>
<?php include_partial("ds_export/exploitation", array('ds' => $ds)) ?>
<?php include_partial("ds_export/stockage", array('ds' => $ds)) ?>
<br />
<br />
<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle ?></span><?php if(preg_match('/Alsace blanc/i', $libelle)): ?><span>&nbsp;(hors Lieux-dits et Communales)</span><?php endif; ?><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau, 'empty' => true)) ?>
<?php endforeach; ?>

  <table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<?php foreach($autres as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 318px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 106px; border: 1px solid black;<?php if(!$is_last_page || is_null($volume)): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume(null, true) ?></td>
</tr>
<?php endforeach; ?>
</table>