<?php 
if (!$csv) 
  return;
?>
<div>
<style>
   .error{color: red;}
</style>
<table>
<?php   $cpt = 0;
foreach ($csv->getRawValue()->getCsv() as $line) 
{
  echo '<tr';
  if (count($errors[$cpt]))
    echo ' class="error"';
  echo '><td>'.($cpt+1).'</td><td>'.$line[0].'</td><td>'.$line[1].'</td><td>'.$line[2].'</td><td>'.$line[3].'</td><td>'.$line[4].'</td><td>'.$line[5].'</td><td>'.$line[6].'</td><td>'.$line[7].'</td><td>'.$line[8].'</td><td>'.$line[9].'</td><td>'.$line[10].'</td><td>'.$line[11].'</td><tr>';
  if (count($errors[$cpt])) {
    echo '<tr class="error"><td>&nbsp;</td><td colspan="12">';
    foreach($errors[$cpt] as $error) {
      echo $error.' | ';
    }
    echo '</td></tr>';
  }
  $cpt++;
}
?></table>
</div>