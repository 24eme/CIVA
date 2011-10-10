<?php use_helper('civa') ?>
<style>
.tableau td, .tableau th, .tableau table {border: 1px solid black; }
pre {display: inline;}
</style>

<span style="background-color: grey; color: white; font-weight: bold;">Exploitation</span><br/>
<table style="border: 1px solid grey;"><tr><td>
<table border="0">
  <tr><td>N° CVI : <i><?php echo $tiers->cvi; ?></i></td><td>Nom : <i><?php echo $tiers->intitule.' '.$tiers->nom; ?></i></td></tr>
  <tr><td>SIRET : <i><?php echo $tiers->siret; ?></i></td><td>Adresse : <i><?php echo $tiers->siege->adresse; ?></i></td></tr>
  <tr><td>Tel. : <i><?php echo $tiers->telephone; ?></i></td><td>Commune : <i><?php echo $tiers->siege->code_postal." ".$tiers->siege->commune; ?></i></td></tr>
  <tr><td>Fax : <i><?php echo $tiers->fax; ?></i></td><td>&nbsp;</td></tr>
</table>
</td></tr></table>

<span style="background-color: grey; color: white; font-weight: bold;">Gestionnaire de l'exploitation</span><br/>
<table style="border: 1px solid grey;"><tr><td>
<table border="0" style="margin: 0px; padding: 0px;">
  <tr><td>Nom et prénom : <i><?php echo $tiers->exploitant->nom; ?></i></td><td>Né(e) le <i><?php echo $tiers->exploitant->getDateNaissanceFr(); ?></i></td></tr>
  <tr><td>Adresse complete : <i><?php echo $tiers->exploitant->adresse.', '.$tiers->exploitant->code_postal.' '.$tiers->exploitant->commune; ?></i></td><td>Tel. <i><?php echo $tiers->exploitant->telephone; ?></i></td></tr>
</table>
</td></tr></table>
  <div><span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle_appellation; ?></span></div>
<table border="1" cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
<?php 

if (!function_exists('printColonne')) {
  function printColonne($libelle, $colonnes, $key, $unite = '') {
    $cpt = 0;
    foreach($colonnes as $c) {
      if (array_key_exists($key, $c->getRawValue())) {
         $cpt++;
      }
    }
    if (!$cpt)
      return ;
    echo '<tr><th style="text-align: left; font-weight: bold; width: 250px; padding-left: 5px; border: 1px solid black;">'.$libelle.'</th>';
    foreach($colonnes as $c) {
      if (array_key_exists($key, $c->getRawValue())) {
        $v = $c[$key];
	echo '<td style="padding-left: 5px;width: 120px; border: 1px solid black;">';
	if ($c['type'] == 'total')    echo '<b>';

        if (!$v && in_array($key, array('superficie', 'volume', 'revendique', 'dplc', 'cave_particuliere'))) {
            $v = 0;
        }

        if (is_numeric($v)) 
          $v = sprintf('%01.02f', $v);
	if ($unite) {
	  $v = preg_replace('/\./', ',', $v);
	}
        echo $v;
	if ($c['type'] == 'total')    echo '</b>';
	if ($unite)
	  echo "&nbsp;<small>$unite</small>";

        if ($key == 'volume' && isset($c['motif_non_recolte'])) {
                echo '<br /><small><i>'.$c['motif_non_recolte'].'</i></small>';
        }
	echo '</td>';
      }else
	echo '<td style="width: 120px;border: 1px solid black;">&nbsp;</td>';
    }
    echo '</tr>';
  }
}

echo printColonne('Cépage', $colonnes_cepage, 'cepage');
if ($hasLieuEditable)
echo printColonne('Lieu', $colonnes_cepage, 'lieu');
echo printColonne('Dénomination complémentaire', $colonnes_cepage, 'denomination');	
echo printColonne('VT/SGN', $colonnes_cepage, 'vtsgn');
echo printColonne('Superficie', $colonnes_cepage, 'superficie', 'ares');
echo printColonne('Récolte totale', $colonnes_cepage, 'volume', 'hl');
//echo printColonne('Motif de non recolte', $colonnes_cepage, 'motif_non_recolte');
foreach ($acheteurs as $type_key => $acheteurs_type) {
    foreach ($acheteurs_type as $cvi => $a) {
        $type = 'Vente à ';
        if ($a->type_acheteur == 'cooperatives') {
        $type = 'Apport à ';
        } else if ($a->type_acheteur == 'mouts') {
        $type = 'Vente de mouts à ';
        }
        echo printColonne($type.$a->nom, $colonnes_cepage, $type_key.'_'.$cvi, 'hl');
    }
}
echo printColonne('Volume sur place', $colonnes_cepage, 'cave_particuliere', 'hl');
echo printColonne('Volume revendiqué', $colonnes_cepage, 'revendique', 'hl');
echo printColonne('DPLC', $colonnes_cepage, 'dplc', 'hl');
?>
</table>
<div style="margin-top: 20px;">
<table>
<tr>
<td style="width: 750px">
<?php if ($enable_identification && count($acheteurs->getParent()->hasAcheteurs())) : ?>
<span style="background-color: black; color: white; font-weight: bold;">Identification des acheteurs et caves coopératives</span><br/>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
  <tr style="font-weight: bold;"><th style="border: 1px solid black;width: 100px;">N° CVI</th><th style="border: 1px solid black;width: 300px;">Raison sociale</th><th style="width: 100px;border: 1px solid black;">Superficie</th><th style="border: 1px solid black;width: 120px;">Volume</th><th style="border: 1px solid black;width: 100px;">dont DPLC</th></tr>
  <?php foreach ($acheteurs as $type_key => $acheteurs_type) : ?>
    <?php foreach($acheteurs_type as $cvi => $a) : ?>
        <tr><td style="border: 1px solid black;width: 100px;"><?php echo $cvi; ?></td>
            <td style="border: 1px solid black;width: 300px;">
                <?php echo $a->nom.' - '.$a->commune; ?>
                <?php if ($type_key == 'mouts'): ?>
                    <br />
                    <small><i>(Acheteur de mouts)</i></small>
                <?php endif; ?>
            </td>
            <td style="border: 1px solid black;width: 100px;"><?php echo echoFloatFr($a->superficie); ?>&nbsp;</td>
            <td  style="border: 1px solid black;width: 120px;"><?php echoFloatFr($a->volume); ?>&nbsp;<small>hl</small></td>
            <td style="border: 1px solid black;width: 100px;"><?php echoFloatFr($a->dontdplc); ?>&nbsp;</td></tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
</table>
<?php endif;?>
</td>
</tr>
</table>


