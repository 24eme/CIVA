<?php $tableau = $tableau->getRawValue() ?>
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <?php foreach($tableau['colonnes'] as $libelle): ?>
  <th style="text-align: left; font-weight: bold; width: <?php echo round(306/count($tableau['colonnes'])) ?>px; border: 1px solid black;">&nbsp;<?php echo $libelle ?></th>
  <?php endforeach; ?>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">hors VT et SGN</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">VT</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">SGN</th>
</tr>
<?php foreach($tableau['produits'] as $produit): ?>
  <tr>
    <?php foreach($produit['colonnes'] as $colonne): ?>
    <?php if($colonne["rowspan"] > 0): ?>
      <td style="text-align: left; border: 1px solid black;" rowspan="<?php echo $colonne["rowspan"] ?>">&nbsp;<?php echo $colonne['libelle'] ?></td>
    <?php endif; ?>
    <?php endforeach; ?>
    <td style="text-align: left; border: 1px solid black;"><?php echoVolume($produit["normal"]) ?></td>
    <td style="text-align: left; border: 1px solid black;"><?php echoVolume($produit["vt"]) ?></td>
    <td style="text-align: left; border: 1px solid black;"><?php echoVolume($produit["sgn"]) ?></td>
  </tr>
<?php endforeach; ?>
<tr>
  <td style="text-align: left; border: 1px solid black; font-weight: bold;" colspan="<?php echo count($tableau['colonnes']) ?>">&nbsp;Total</td>
  <td style="text-align: left; border: 1px solid black;"><?php echoVolume($tableau["total"]["normal"]) ?></td>
  <td style="text-align: left; border: 1px solid black;"><?php echoVolume($tableau["total"]["vt"]) ?></td>
  <td style="text-align: left; border: 1px solid black;"><?php echoVolume($tableau["total"]["sgn"]) ?></td>
</tr>
</table>
<small><br /></small>