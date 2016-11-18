<?php if($sf_user->hasFlash('confirmation')) : ?>
    <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
<?php endif; ?>
<div>
<style>
.error{color: red;}
td.error, td.warning{width: 700px; vertical-align: top;}
.warning{color: orange;}
.csv td{font-family: "Arial,sans-serif,sans";border: 1px solid grey;padding:2px;}
.error td.titre{border-bottom: none;}
.titre{background-color: grey;}
td.maintitre{border-top: 1px solid black;}
</style>
<div class="recap">
<?php
  if (count($recap->errors)) {
    echo "<p>Des erreurs ont été repérées concernant le fichier CSV que vous venez de nous fournir</p><table><tr><th>Message</th><th>Numéros de ligne</th></tr>";
    foreach ($recap->errors as $msg => $lines) {
      echo "<tr><td class='error'>$msg</td><td>";
      $c = 0;
      foreach ($lines->getRawValue() as $l) {
	if ($c)
	  echo ", ";
	else
	  $c = 1;
	echo "<a href='#l$l'>$l</a>";
      }
      echo "</td></tr>";
    }
    echo "</table>";
  }
  if (count($recap->warnings)) {
    echo "<p>Des alertes ont été repérées concernant le fichier CSV que vous venez de nous fournir</p><table><tr><th>Message</th><th>Numéros de ligne</th></tr>";
    foreach ($recap->warnings as $msg => $lines) {
      echo "<tr><td class='warning'>$msg</td><td>";
      $c = 0;
      foreach ($lines->getRawValue() as $l) {
	if ($c)
	  echo ", ";
	else
	  $c = 1;
	echo "<a href='#l$l'>$l</a>";
      }
      echo "</td></tr>";
    }
    echo "</table>";
  }
?>
</div>
<div class="linkback">
    <p style="float:right; margin: 15px;"><?php echo link_to("Retour à votre espace CIVA", "mon_espace_civa_dr_acheteur", $etablissement); ?></p>
</div>
<table class="csv">
<tr class="titre"><td>&nbsp;</td><td>CVI acheteur</td><td>Nom acheteur</td><td>CVI récoltant</td><td>Nom récoltant</td><td>Appellation</td><td>Lieu</td><td>Cepage</td><td>VT/SGN</td><td>Denomination</td><td>Superficie acheté</td><td>Volume acheté</td></tr>
<?php   $cpt = 0;
foreach ($csv->getRawValue()->getCsv() as $line)
{
  echo '<tr';
  if (count($errors[$cpt]))
    echo ' class="error"';
  echo '><td class="titre maintitre" id="l'.($cpt+1).'">'.($cpt+1).'</td><td>'.$line[0].'</td><td>'.$line[1].'</td><td>'.$line[2].'</td><td>'.$line[3].'</td><td>'.$line[4].'</td><td>'.$line[5].'</td><td>'.$line[6].'</td><td>'.$line[7].'</td><td>'.$line[8].'</td><td class="csv_num">';
  if (preg_match('/[^0-9,\.]/', $line[9]))
    echo $line[9];
  else
    printf('%.02f', $line[9]);
  echo '</td><td class="csv_num">';
  if (preg_match('/[^0-9,\.]/', $line[10]))
    echo $line[10];
  else
    printf('%.02f', $line[10]);
  echo '</td><tr>';
  if (count($errors[$cpt])) {
    foreach($errors[$cpt]->getRawValue() as $error) {
      echo '<tr class="error"><td class="titre" style="color:gray;">'.($cpt+1).'</td><td colspan="12">';
      echo $error;
      echo '</td></tr>';
    }
  }
  if (count($warnings[$cpt])) {
    foreach($warnings[$cpt]->getRawValue() as $error) {
      echo '<tr class="warning"><td class="titre" style:"color:gray">'.($cpt+1).'</td><td colspan="12">';
      echo $error;
      echo '</td></tr>';
    }
  }
  $cpt++;
}
?></table>
</div>
