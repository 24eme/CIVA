<?php 
	use_helper('civa');
	$appellations = $infos['appellations'];
	$libelle = $infos['libelle'];
	$superficie = $infos['superficie'];
	$volume = $infos['volume'];
	$revendique = $infos['revendique'];
	$dplc = $infos['dplc'];
?>
<span style="background-color: black; color: white; font-weight: bold;">Récapitulatif</span><br/>
<table border="1" cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
	<thead>
		<tr>
			<th>Appellations</th>
			<?php foreach ($appellations as $a): ?>
			<th><?php echo preg_replace('/(AOC|Vin de table)/', '<span>\1</span>', $libelle[$a]); ?></th>
			<?php endforeach; ?>
			<th>Total général</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Superficie (ares)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $superficie[$a]); ?></td>
			<?php endforeach; ?>
			<td><?php echoFloat( $infos['total_superficie']);?></td>
		</tr>
		<tr>
			<td>Volume Total (Hl)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $volume[$a]); ?></td>
			<?php endforeach; ?>
			<td><?php echoFloat( $infos['total_volume']);?></td>
		</tr>
		<tr>
			<td>Volume Revendiqué (Hl)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $revendique[$a]); ?></td>
			<?php endforeach; ?>
			<td><?php echoFloat( $infos['total_revendique']);?></td>
		</tr>
		<tr>
			<td>DPLC (Hl)</td>
			<?php foreach ($appellations as $a): ?>
			<td><?php echoFloat( $dplc[$a]); ?></td>
			<?php endforeach; ?>
			<td><?php echoFloat( $infos['total_dplc']);?></td>
		</tr>
	</tbody>
</table>
<br />
<span style="background-color: black; color: white; font-weight: bold;">Lies et Jeunes vignes</span><br/>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
    <tr><td style="border: 1px solid black;font-weight: bold; text-align: left; width: 120px;">&nbsp;Lies</td><td style="border: 1px solid black;"><?php echoFloatFr($infos['lies']); ?>&nbsp;<small>hl</small></td></tr>
<tr><td style="border: 1px solid black;font-weight: bold; text-align: left; width: 120px;">&nbsp;Jeunes vignes</td><td style="border: 1px solid black;"><?php echoFloatFr($infos['jeunes_vignes']); ?>&nbsp;<small>ares</small></td></tr>
</table>
