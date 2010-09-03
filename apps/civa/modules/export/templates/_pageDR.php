<style>
.tableau td, .tableau th, .tableau table {border: 1px solid black; }
pre {display: inline;}
</style>

<span style="background-color: grey; color: white; font-weight: bold;">Exploitation</span><br/>
<table style="border: 1px solid grey;"><tr><td>
<table border="0">
  <tr><td>N° CVI : <i><?php echo $tiers->cvi; ?></i></td><td>Nom : <i><?php echo $tiers->intitule.' '.$tiers->nom; ?></i></td></tr>
  <tr><td>SIRET : <i><?php echo $tiers->siret; ?></i></td><td>Adresse : <i><?php echo $tiers->siege->adresse; ?></i></td></tr>
  <tr><td>Régime Fiscal : <i><?php echo $tiers->regime_fiscal; ?></i></td><td>Commune : <i><?php echo $tiers->siege->code_postal." ".$tiers->siege->commune; ?></i></td></tr>
  <tr><td>Tel. : <i><?php echo $tiers->telephone; ?></i></td><td>Fax : <i><?php echo $tiers->fax; ?></i></td></tr>
</table>
</td></tr></table>

<span style="background-color: grey; color: white; font-weight: bold;">Gestionnaire de l'exploitation</span><br/>
<table style="border: 1px solid grey;"><tr><td>
<table border="0" style="margin: 0px; padding: 0px;">
  <tr><td>Nom et prénom : <i><?php echo $tiers->exploitant->nom; ?></i></td><td>Né le <i><?php echo $tiers->exploitant->date_naissance; ?></i></td></tr>
  <tr><td>Adresse complete : <i><?php echo $tiers->exploitant->adresse.', '.$tiers->exploitant->code_postal.' '.$tiers->exploitant->commune; ?></i></td><td>Tel. <i><?php echo $tiers->exploitant->telephone; ?></i></td></tr>
</table>
</td></tr></table>
  <div><span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle_appellation; ?></span></div>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
<?php 

if (!function_exists('printColonne')) {
  function printColonne($libelle, $colonnes, $key, $unite = '') {
    echo '<tr><th style="text-align: left; font-weight: bold; width: 250px;">'.$libelle.'</th>';
    foreach($colonnes as $c) {
      if (isset($c[$key]) && $v = $c[$key]) {
	echo '<td style="width: 120px;">';
	if ($c['type'] == 'total')    echo '<b>';
	if ($unite)
	  echo preg_replace('/\./', ',', $v);
	else
	  echo $v;
	if ($c['type'] == 'total')    echo '</b>';
	if ($unite)
	  echo "&nbsp;<small>$unite</small>";
	echo '</td>';
      }else
	echo '<td style="width: 120px;">&nbsp;</td>';
    }
    echo '</tr>';
  }
}

echo printColonne('Cépage', $colonnes_cepage, 'cepage');
echo printColonne('Dénom. complém.', $colonnes_cepage, 'denomination');
echo printColonne('VT/SGN', $colonnes_cepage, 'vtsgn');
echo printColonne('Superficie', $colonnes_cepage, 'superficie', 'ares');
echo printColonne('Récolte totale', $colonnes_cepage, 'volume', 'hl');
foreach ($acheteurs as $cvi => $a) {
  echo printColonne('Vente à '.$a->nom, $colonnes_cepage, $cvi, 'hl');
}
echo printColonne('Cave particulière', $colonnes_cepage, 'cave_particuliere', 'hl');
echo printColonne('Volume revendiqué', $colonnes_cepage, 'revendique', 'hl');
echo printColonne('DPLC', $colonnes_cepage, 'dplc', 'hl');
?>
</table>
<?php if ($enable_identification && count($acheteurs)) : ?>
<div style="margin-top: 20px;">
<div><span style="background-color: black; color: white; font-weight: bold;">Identification des acheteurs et caves coopératives</span></div>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
  <tr style="font-weight: bold;"><th style="width: 100px;">N° CVI</th><th style="width: 300px;">Raison social (commune)</th><th style="width: 100px;">Superficie</th><th style="width: 120px;">Vente raisins</th><th style="width: 100px;">dont DPLC</th></tr>
  <?php foreach($acheteurs as $cvi => $a) : ?>
  <tr><td style="width: 100px;"><?php echo $cvi; ?></td><td style="width: 300px;"><?php echo $a->nom.' ('.$a->commune.')'; ?></td><td style="width: 100px;"><?php echo $a->superficie; ?>&nbsp;</td><td  style="width: 120px;"><?php echo $a->volume; ?>&nbsp;<small>hl</small></td><td style="width: 100px;"><?php echo $a->dontdplc; ?>&nbsp;</td></tr>
  <?php endforeach; ?>
</table>
</div>
<?php endif;?>


