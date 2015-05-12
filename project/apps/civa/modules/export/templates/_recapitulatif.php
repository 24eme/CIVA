<?php 
	use_helper('Float');
	use_helper('drExport');
	$appellations = $infos['appellations'];
	$libelle = $infos['libelle'];
	$superficie = $infos['superficie'];
	$volume = $infos['volume'];
        $volume_vendus = $infos['volume_vendus'];
        $volume_sur_place = $infos['volume_sur_place'];
        $volume_rebeches = $infos['volume_rebeches'];
        $volume_rebeches_sur_place = $infos['volume_rebeches_sur_place'];
	$revendique = $infos['revendique'];
	$revendique_sur_place = $infos['revendique_sur_place'];
	$usages_industriels = $infos['usages_industriels'];
	$usages_industriels_sur_place = $infos['usages_industriels_sur_place'];
?>
<style>
.tableau td, .tableau th, .tableau table {border: 1px solid black; }
pre {display: inline;}
table {
   padding-left: 0px;
   padding-right: 5px;
}
</style>

<?php include_partial('export/exploitation', array('dr' => $dr)); ?>
<br /><br />
<span style="background-color: black; color: white; font-weight: bold;">Récapitulatif</span><br/>
<table border="1" cellspacing=0 cellpaggind=0 style="text-align: right; border: 1px solid black;">
	<thead>
		<tr>
			<th style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Appellations</th>
			<?php foreach ($appellations as $a): ?>
			<th style="border: 1px solid black;font-weight: bold; text-align: center; width: 120px;"><?php echo preg_replace('|(<span>AOC</span> Alsace)|', '\1<br />', preg_replace('/(AOC|Vin de table)/', '<span>\1</span>', $libelle[$a])); ?></th>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<th style="border: 1px solid black;font-weight: bold; text-align: center; width: 120px;">Total général</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Superficie</td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;"><?php echoSuperficie($superficie[$a]); ?></td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoSuperficie($infos['total_superficie'], true);?></td>
			<?php endif; ?>
		</tr>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Volume Total</td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;"><?php echoVolume( $volume[$a]); ?></td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_volume'], true);?></td>
			<?php endif; ?>
		</tr>
		<?php if($infos['total_volume_vendus'] !== null): ?>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Volume Vendu</td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;"><?php echoVolume( $volume_vendus[$a]); ?></td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_volume_vendus'], true);?></td>
			<?php endif; ?>
		</tr>
		<?php endif; ?>
        <tr>
            <td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Volume sur place</td>
            <?php foreach ($appellations as $a): ?>
            <td style="width: 120px;"><?php echoVolume( $volume_sur_place[$a]); ?></td>
            <?php endforeach; ?>
            <?php if ($has_total): ?>
            <td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_volume_sur_place'], true);?></td>
            <?php endif; ?>
        </tr>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Volume Revendiqué</td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;"><?php echoVolume( $revendique[$a]); ?></td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_revendique'], true);?></td>
			<?php endif; ?>
		</tr>
		<?php if($infos['total_revendique_sur_place'] !== null): ?>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;<small>&nbsp;dont sur place</small></td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;">
				<?php echoVolume( $revendique_sur_place[$a]); ?>
			</td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_revendique_sur_place'], true);?></td>
			<?php endif; ?>
		</tr>
		<?php endif; ?>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;"><?php if($has_no_usages_industriels): ?>&nbsp;DPLC<?php else: ?>&nbsp;Usages industriels<?php endif; ?></td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;"><?php echoVolume( $usages_industriels[$a]); ?></td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_usages_industriels'], true);?></td>
			<?php endif; ?>
		</tr>
		<?php if($infos['total_usages_industriels_sur_place'] !== null): ?>
		<tr>
			<td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;<small>&nbsp;dont sur place</small></td>
			<?php foreach ($appellations as $a): ?>
			<td style="width: 120px;">
				<?php echoVolume( $usages_industriels_sur_place[$a]); ?>
			</td>
			<?php endforeach; ?>
			<?php if ($has_total): ?>
			<td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_usages_industriels_sur_place'], true);?></td>
			<?php endif; ?>
		</tr>
		<?php endif; ?>
		<?php if($infos['total_volume_rebeches'] !== null): ?>
        <tr>
            <td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Rebêches</td>
            <?php foreach ($appellations as $a): ?>
            <td style="width: 120px;">
            	<?php if(!is_null($volume_rebeches[$a])): ?>
        			<?php echoVolume($volume_rebeches[$a]); ?>
        		<?php else: ?>
        			&nbsp;
            	<?php endif; ?>
            </td>
            <?php endforeach; ?>
            <?php if ($has_total): ?>
            <td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_volume_rebeches'], true);?></td>
            <?php endif; ?>
        </tr>
        <?php endif; ?>
        <?php if($infos['total_volume_rebeches_sur_place'] !== null): ?>
                <tr>
            <td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;<small>&nbsp;dont sur place</small></td>
            <?php foreach ($appellations as $a): ?>
            <td style="width: 120px;">
            	<?php if(!is_null($volume_rebeches_sur_place[$a])): ?>
        			<?php echoVolume($volume_rebeches_sur_place[$a]); ?>
        		<?php else: ?>
        			&nbsp;
            	<?php endif; ?>
            </td>
            <?php endforeach; ?>
            <?php if ($has_total): ?>
            <td style="border: 1px solid black; width: 120px;"><?php echoVolume( $infos['total_volume_rebeches_sur_place'], true);?></td>
            <?php endif; ?>
        </tr>
    	<?php endif; ?>
	</tbody>
</table>
<?php if ($has_total): ?>
<br />
<br />
<?php if($has_no_usages_industriels): ?>
<span style="background-color: black; color: white; font-weight: bold;">Lies et Jeunes vignes</span><br/>
<?php else: ?>
<span style="background-color: black; color: white; font-weight: bold;">Jeunes vignes</span><br/>
<?php endif; ?>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: right; border: 1px solid black;">
    <?php if($has_no_usages_industriels): ?>
    <tr>
        <td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Lies</td>
        <td style="border: 1px solid black; width: 120px;"><?php echoSuperficie($infos['lies']); ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td style="border: 1px solid black;font-weight: bold; text-align: left; width: 250px;">&nbsp;Jeunes vignes</td>
        <td style="border: 1px solid black; width: 120px;"><?php echoSuperficie($infos['jeunes_vignes']); ?></td>
    </tr>
</table>
<?php endif; ?>