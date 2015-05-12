<?php use_helper('Float'); ?>
<?php use_helper('drExport'); ?>
<style>
.tableau td, .tableau th, .tableau table {border: 1px solid black;}
table {
  padding-left: 0px;
  padding-right: 5px;
}
pre {display: inline;}
</style>

<?php include_partial('export/exploitation', array('dr' => $dr)); ?>
<br />
<div><span style="background-color: black; color: white; font-weight: bold;"><?php echo $libelle_appellation; ?></span></div>
<table border="1" cellspacing=0 cellpaggind=0 style="text-align: right; border: 1px solid black;">
<?php 

if (!function_exists('printColonne')) {
  function printColonne($libelle, $colonnes, $key, $unite = '', $display_zero = true) {
    $cpt = 0;
    foreach($colonnes as $c) {
      if (array_key_exists($key, $c->getRawValue())) {
         $cpt++;
      }
    }
    /*if (!$cpt)
      return ;*/
    echo '<tr><th style="text-align: left; font-weight: bold; width: 250px; border: 1px solid black;">&nbsp;'.$libelle.'</th>';
    foreach($colonnes as $c) {
        $arr_col = $c->getRawValue();

        if($arr_col['cepage'] == 'Rebêches' && $key == 'superficie') continue;
        if($arr_col['cepage'] == 'Rebêches' && $key == 'revendique') continue;
        if($arr_col['cepage'] == 'Rebêches' && $key == 'usages_industriels') continue;
        if (array_key_exists($key, $c->getRawValue())) {
        $v = $c[$key];

	echo '<td style="width: 120px; border: 1px solid black; '. ((!$unite) ? "text-align: center;" : "") .'">';
	if (($c['type'] == 'total'))    echo '<b>';

        if (!$v && in_array($key, array('superficie', 'volume', 'revendique', 'usages_industriels', 'cave_particuliere'))) {
            $v = 0;
        }

        $afficher_volume = !(!$display_zero && $v == 0);

        if (is_numeric($v)){
          $v = sprintf('%01.02f', $v);
        }

      	if ($unite) {
  	      $v = preg_replace('/\./', ',', $v);
  	    }

        if($afficher_volume) {
          echo $v;

          if (($c['type'] == 'total'))    echo '</b>';
          
	        if ($unite) {
	         echo "&nbsp;<small>$unite</small>";
           if($unite == 'hl') {
              echo "&nbsp;&nbsp;&nbsp;";
           }
           if($unite == 'ares') {
            echo "<small>&nbsp;</small>";
           }
          }
        } else {
          echo "&nbsp;&nbsp;";
          if ($c['type'] == 'total')    echo '</b>';
        }

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
if ($hasLieuEditable) {
  echo printColonne('Lieu', $colonnes_cepage, 'lieu');
}
echo printColonne('Dénomination complémentaire', $colonnes_cepage, 'denomination');	
if ($hasVTSGN) {
  echo printColonne('VT/SGN', $colonnes_cepage, 'vtsgn');
}
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

if($has_no_usages_industriels) {
  echo printColonne('DPLC', $colonnes_cepage, 'usages_industriels', 'hl');
} else {
  echo printColonne('Usages Industriels', $colonnes_cepage, 'usages_industriels', 'hl', false);
}
?>
</table>
<br />
<div>
<table>
<tr>
<td style="width: 820px">
<?php if ($enable_identification && count($acheteurs->getParent()->hasAcheteurs())) : ?>
<span style="background-color: black; color: white; font-weight: bold;">Identification des acheteurs et caves coopératives</span><br/>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
  <tr style="font-weight: bold;"><th style="border: 1px solid black;width: 100px;">N° CVI</th><th style="border: 1px solid black;width: 300px;">Raison sociale</th><th style="width: 120px;border: 1px solid black;">Superficie</th><th style="border: 1px solid black;width: 120px;">Volume</th><th style="border: 1px solid black;width: 180px;">Dont dépassement</th></tr>
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
            <td style="border: 1px solid black;width: 120px; text-align: right;"><?php echoSuperficie($a->superficie); ?></td>
            <td  style="border: 1px solid black;width: 120px; text-align: right;"><?php echoVolume($a->volume); ?></td>
            <td style="border: 1px solid black;width: 180px; text-align: right;"><?php echoVolume($a->dontdplc); ?></td></tr>
    <?php endforeach; ?>
  <?php endforeach; ?>
</table>
<?php endif;?>
</td>
</tr>
</table>


