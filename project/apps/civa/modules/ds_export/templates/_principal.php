<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php include_partial("ds_export/exploitation", array('ds' => $ds)) ?>
<?php include_partial("ds_export/stockage", array('ds' => $ds)) ?>
<small><br /></small>
<?php foreach($recap as $libelle => $tableau): ?>
  <span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle ?></span><br />
  <?php include_partial('ds_export/tableau', array('tableau' => $tableau)) ?>
<?php endforeach; ?>

<?php $totals = array('total_stock' => $ds->getTotalAOCByType('total_stock'),
                      'total_normal' => $ds->getTotalAOCByType('total_normal'),
                      'total_vt' => $ds->getTotalAOCByType('total_vt'),
                      'total_sgn' => $ds->getTotalAOCByType('total_sgn')); ?>

<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<tr>
  <th style="width: 306px; font-weight: bold; text-align: center; border: 1px solid black; background-color: black; color: white;">Total</th>
  <th style="width: 110px; font-weight: bold; text-align: center; border: 1px solid black; background-color: black; color: white;">hors VT et SGN</th>
  <th style="width: 110px; font-weight: bold; text-align: center; border: 1px solid black; background-color: black; color: white;">VT</th>
  <th style="width: 110px; font-weight: bold; text-align: center; border: 1px solid black; background-color: black; color: white;">SGN</th>
</tr>
<tr>
  <?php if(!$is_last_page): ?>
    <td style="border: 1px solid black; background-color: #bbb; text-align: center;">&nbsp;<small>(Page suivante)</small>&nbsp;</td>
    <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
    <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
    <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
  <?php else: ?>
    <?php if($ds->hasAOC() && $is_last_page): ?>
      <?php foreach($totals as $key => $volume): ?>
        <?php if(!$is_last_page): ?>
        <td style="border: 1px solid black; background-color: #bbb; <?php if($key == "total_stock"): ?>text-align:center;<?php endif; ?>">&nbsp;</td>
        <?php else: ?>
          <td style="border: 1px solid black; <?php if($key == "total_stock"): ?>text-align:center;<?php endif; ?>"><?php echoVolume($volume, true) ?></td>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <td style="border: 1px solid black; text-align: center; font-weight: bold;" colspan="4"><i>Néant</i></td>
    <?php endif; ?>
  <?php endif; ?>
</tr>
</table>

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">Autres Produits</span><?php if(!$is_last_page): ?><span><small><i>&nbsp;&nbsp;(Page suivante)</i></small></span><?php endif; ?><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<?php foreach($autres as $libelle => $volume): ?>
<tr>
  <td style="text-align: left; width: 306px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $libelle ?></td>
  <td style="width: 110px; border: 1px solid black;<?php if(!$is_last_page || is_null($volume)): ?>background-color: #bbb;<?php endif; ?>"><?php ($is_last_page) ? echoVolume($volume, true) : echoVolume(null, true) ?></td>
</tr>
<?php endforeach; ?>
</table>


