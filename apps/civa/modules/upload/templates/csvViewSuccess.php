<?php 
if (!$csv) 
  return;
?>
<div>
<style>
   .error{color: red;}
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
      echo "<tr><td style='width: 700px; vertical-align: top; color: red;'>$msg</td><td>".implode(', ', $lines->getRawValue())."</td></tr>";
    }
    echo "</table>";
  }
  if (count($recap->warnings)) {
    echo "<p>Des alertes ont été repérées concernant le fichier CSV que vous venez de nous fournir</p><table><tr><th>Message</th><th>Numéros de ligne</th></tr>";
    foreach ($recap->warnings as $msg => $lines) {
      echo "<tr><td style='width: 700px; vertical-align: top; color: red;'>$msg</td><td>".implode(', ', $lines->getRawValue())."</td></tr>";
    }
    echo "</table>";
  }
?>
</div>

<table class="csv">
<tr class="titre"><td>&nbsp;</td><td>CVI acheteur</td><td>Nom acheteur</td><td>CVI récoltant</td><td>Nom récoltant</td><td>Appellation</td><td>Lieu</td><td>Cepage</td><td>VT/SGN</td><td>Denomination</td><td>Superficie acheté</td><td>Volume acheté</td></tr>
<?php   $cpt = 0;
foreach ($csv->getRawValue()->getCsv() as $line) 
{
  echo '<tr';
  if (count($errors[$cpt]))
    echo ' class="error"';
  echo '><td class="titre maintitre" id="l'.($cpt+1).'">'.($cpt+1).'</td><td>'.$line[0].'</td><td>'.$line[1].'</td><td>'.$line[2].'</td><td>'.$line[3].'</td><td>'.$line[4].'</td><td>'.$line[5].'</td><td>'.$line[6].'</td><td>'.$line[7].'</td><td>'.$line[8].'</td><td class="csv_num">'.sprintf('%.02f', $line[9]).'</td><td class="csv_num">'.sprintf('%.02f', $line[10]).'</td><tr>';
  if (count($errors[$cpt])) {
    foreach($errors[$cpt] as $error) {
      echo '<tr class="error"><td class="titre" style="color:gray;">'.($cpt+1).'</td><td colspan="12">';
      echo $error;
      echo '</td></tr>';
    }
  }
  if (count($warnings[$cpt])) {
    foreach($warnings[$cpt] as $error) {
      echo '<tr class="error warnings"><td class="titre" style:"color:gray">'.($cpt+1).'</td><td colspan="12">';
      echo $error;
      echo '</td></tr>';
    }
  }
  $cpt++;
}
?></table>
</div>
