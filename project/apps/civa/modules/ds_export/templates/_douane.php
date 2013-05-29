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

<?php $blanc_cepages = array("Chasselas", "Sylvaner", "Pinot Blanc", "Assemblages", "Riesling", "Pinot Gris", "Muscat d'Alsace", "Gewurztraminer", "Klevener de heiligenstein", "Pinot Noir Rosé", "Pinot Noir Rouge"); ?>

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">AOC Alsace blanc</span><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <th style="text-align: left; font-weight: bold; width: 306px; border: 1px solid black;">&nbsp;Cépages</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">hors VT et SGN</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">VT</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">SGN</th>
</tr>
<?php foreach($blanc_cepages as $cepage): ?>
<tr>
  <td style="text-align: left; border: 1px solid black;">&nbsp;<?php echo $cepage ?></td>
  <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
</tr>
<?php endforeach; ?>
<tr>
  <td style="text-align: left; border: 1px solid black; font-weight: bold;">&nbsp;Total</td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
</tr>
</table>

<?php $grd_cru = array("Riesling" => array("Brand", "Eichberg", "Sporen"), "Pinot Gris" => array("Frankstein", "Brand", "Eichberg"), "Muscat d'Alsace" => array("Kessler", "Sporen"), "Gewurztraminer" => array("Brand", "Eichberg", "Sporen"), "Muscat Ottonel" => array("Brand", "Eichberg")); ?>

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">AOC Alsace Grands Crus</span><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <th style="font-weight: bold; text-align: left; width: 156px; border: 1px solid black;">&nbsp;Lieu-dit</th>
  <th style="font-weight: bold; text-align: left;  width: 150px; border: 1px solid black;">&nbsp;Cépages</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">hors VT et SGN</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">VT</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">SGN</th>
</tr>
<?php foreach($grd_cru as $cepage => $lieux): ?>
    <?php foreach($lieux as $key => $lieu): ?>
        <tr>
          <td style="text-align: left; border: 1px solid black;">&nbsp;<?php echo $lieu ?></td>
          <?php if($key == 0): ?>
          <td style="text-align: left; border: 1px solid black;" rowspan="<?php echo count($lieux) ?>">&nbsp;<?php echo $cepage ?></td>
          <?php endif; ?>
          <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
          <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
          <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>
<tr>
  <td style="text-align: left; border: 1px solid black; font-weight: bold;" colspan="2">&nbsp;Total</td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
</tr>
</table>

<?php $cremant = array("Blanc", "Rosé"); ?>

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">Crémant d'Alsace</span><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <th style="font-weight: bold; text-align: left; width: 306px; border: 1px solid black;">&nbsp;Couleur</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">hors VT et SGN</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">VT</th>
  <th style="font-weight: bold; width: 110px; border: 1px solid black;">SGN</th>
</tr>
<?php foreach($cremant as $couleur): ?>
        <tr>
          <td style="border: 1px solid black; text-align: left;">&nbsp;<?php echo $couleur ?></td>
          <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
          <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
          <td style="border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
        </tr>
<?php endforeach; ?>
<tr>
  <td style="border: 1px solid black; text-align: left; font-weight: bold;">&nbsp;Total</td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
</tr>
</table>

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">Récapitulatif</span><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<tr>
  <th style="width: 306px; font-weight: bold; border: 1px solid black;">Total</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black;">hors VT et SGN</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black;">VT</th>
  <th style="width: 110px; font-weight: bold; border: 1px solid black;">SGN</th>
</tr>
<tr>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
  <td style="border: 1px solid black;"><b>45,00</b>&nbsp;<small>hl</small></td>
</tr>
</table>

<?php $autres = array("Moûts concentrés rectifiés", "Vins de table - Vins sans IG", "Vins de table mousseux", "Rebêches", "Dépassement de PLC", "Lie en stocks"); ?> 

<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">Autres Produits</span><br />
<table border="1" cellspacing=0 cellpadding=0 style="text-align: center; border: 1px solid black;">
<?php foreach($autres as $autre): ?>
<tr>
  <td style="text-align: left; width: 306px; border: 1px solid black; font-weight: bold;">&nbsp;<?php echo $autre ?></td>
  <td style="width: 110px; border: 1px solid black;">45,00&nbsp;<small>hl</small></td>
</tr>
<?php endforeach; ?>
</table>
