<?php 
	use_helper('civa');
	$appellations = $infos['appellations'];
	$libelle = $infos['libelle'];
	$superficie = $infos['superficie'];
	$volume = $infos['volume'];
	$revendique = $infos['revendique'];
	$dplc = $infos['dplc'];
?>
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
<br />
<span style="background-color: black; color: white; font-weight: bold;">Récapitulatif</span><br/>
<table border="1" cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
	<thead>
		<tr>
			<th style="border: 1px solid black;font-weight: bold; text-align: left;">Appellations</th>
			<?php foreach ($appellations as $a): ?>
			<th style="border: 1px solid black;font-weight: bold;"><?php echo preg_replace('/(AOC|Vin de table)/', '<span>\1</span>', $libelle[$a]); ?></th>
			<?php endforeach; ?>
			<th style="border: 1px solid black;font-weight: bold;">Total général</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left;">Superficie (ares)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $superficie[$a]); ?> <small>ares</small></td>
			<?php endforeach; ?>
			<td style="border: 1px solid black;"><strong><?php echoFloat( $infos['total_superficie']);?></strong> <small>ares</small></td>
		</tr>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left;">Volume Total (Hl)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $volume[$a]); ?> <small>hl</small></td>
			<?php endforeach; ?>
			<td style="border: 1px solid black;"><strong><?php echoFloat( $infos['total_volume']);?></strong> <small>hl</small></td>
		</tr>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left;">Volume Revendiqué (Hl)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $revendique[$a]); ?> <small>hl</small></td>
			<?php endforeach; ?>
			<td style="border: 1px solid black;"><strong><?php echoFloat( $infos['total_revendique']);?></strong> <small>hl</small></td>
		</tr>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left;">DPLC (Hl)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $dplc[$a]); ?> <small>hl</small></td>
			<?php endforeach; ?>
			<td style="border: 1px solid black;"><strong><?php echoFloat( $infos['total_dplc']);?></strong> <small>hl</small></td>
		</tr>
	</tbody>
</table>
<br />
<span style="background-color: black; color: white; font-weight: bold;">Lies et Jeunes vignes</span><br/>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
    <tr><td style="border: 1px solid black;font-weight: bold; text-align: left; width: 120px;">&nbsp;Lies</td><td style="border: 1px solid black;"><?php echoFloatFr($infos['lies']); ?>&nbsp;<small>hl</small></td></tr>
<tr><td style="border: 1px solid black;font-weight: bold; text-align: left; width: 120px;">&nbsp;Jeunes vignes</td><td style="border: 1px solid black;"><?php echoFloatFr($infos['jeunes_vignes']); ?>&nbsp;<small>ares</small></td></tr>
</table>
