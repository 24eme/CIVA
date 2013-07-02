<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php use_helper('Text') ?>

<?php $tableau = $tableau->getRawValue() ?>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
<?php if(!array_key_exists('no_header', $tableau) || !$tableau['no_header']): ?>
  <tr>
    <?php foreach($tableau['colonnes'] as $libelle): ?>
    <th style="text-align: left; font-weight: bold; width: <?php echo round(306/count($tableau['colonnes'])) ?>px; border: 1px solid black;">&nbsp;<?php echo $libelle ?></th>
    <?php endforeach; ?>
    <th style="font-weight: bold; width: 110px; text-align: center;  border: 1px solid black;">hors VT et SGN</th>
    <th style="font-weight: bold; width: 110px; text-align: center;  border: 1px solid black;">VT</th>
    <th style="font-weight: bold; width: 110px; text-align: center;  border: 1px solid black;">SGN</th>
  </tr>
<?php endif; ?>
<?php foreach($tableau['produits'] as $produit): ?>
  <tr>
    <?php foreach($produit['colonnes'] as $colonne): ?>
    <?php if($colonne["rowspan"] > 0): ?>
      <td style="text-align: left; border: 1px solid black; width: <?php echo round(306/count($tableau['colonnes'])) ?>px; <?php if(is_null($colonne['libelle'])): ?>background-color: #bbb;<?php endif; ?>" rowspan="<?php echo $colonne["rowspan"] ?>">&nbsp;<?php echo truncate_text($colonne['libelle'], round(56 / count($tableau['colonnes'])), "...", false) ?></td>
    <?php endif; ?>
    <?php endforeach; ?>
    <td style="border: 1px solid black; width: 110px; <?php if(is_null($produit["normal"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($produit["normal"]) ?></td>
    <td style="border: 1px solid black; width: 110px;<?php if(is_null($produit["vt"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($produit["vt"]) ?></td>
    <td style="border: 1px solid black; width: 110px; <?php if(is_null($produit["sgn"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($produit["sgn"]) ?></td>
  </tr>
<?php endforeach; ?>
<tr>
  <td style="text-align: left; border: 1px solid black;" colspan="<?php echo count($tableau['colonnes']) ?>">&nbsp;<b>Total</b><?php if($tableau['total_suivante']): ?><small><i>&nbsp;&nbsp;(Page suivante)</i></small><?php endif; ?>
  </td>
 
  <?php if(!isset($tableau['total']) || $tableau['total_suivante']): ?>
    <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
    <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
    <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
  <?php elseif(isset($tableau['total']) && !$tableau['total_suivante'] && is_null($tableau["total"]["normal"]) && is_null($tableau["total"]["vt"]) && is_null($tableau["total"]["sgn"])): ?>
    <td colspan="3" style="border: 1px solid black; text-align:center;"><i>Néant</i></td>
  <?php else: ?>
    <td style="border: 1px solid black; <?php if(is_null($tableau["total"]["normal"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($tableau["total"]["normal"], true) ?></td>
    <td style="border: 1px solid black; <?php if(is_null($tableau["total"]["vt"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($tableau["total"]["vt"], true) ?></td>
    <td style="border: 1px solid black; <?php if(is_null($tableau["total"]["sgn"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($tableau["total"]["sgn"], true) ?></td>
  <?php endif; ?>
</tr>
</table>
<small><br /></small>