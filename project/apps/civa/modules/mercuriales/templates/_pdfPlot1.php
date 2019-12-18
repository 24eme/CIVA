<?php 
$values = $mercuriale->getMercurialePlotDatas(array('GW', 'RI', 'SY'))->getRawValue();
$datas = array_pop($values);
$absents = array();
foreach ($datas as $k => $v) {
    if (!$v) {
        $absents[] = $k;
    }
}
?>
<table border="0" cellspacing=0 cellpadding="0">
	<tr>
		<td style="width: 8%;"><img src="<?php echo sfConfig::get('sf_web_dir')."/images/pdf/civa.png" ?>" alt="CIVA-Logo" /></td>
		<td style="width: 92%; text-align: center;">
			<h1 style="color: #b1920c;"><span style="font-weight: normal; color: #000;">AOC Alsace</span>  SYLVANER - RIESLING - GEWURZTRAMINER <span style="font-weight: normal; color: #000;">CONVENTIONNEL</span></h1>
			<span>relevés bi-mensuels des cours des transactions en vrac entre opérateurs du vignoble d'Alsace AOC (relevés définitifs)</span>
		</td>
	</tr>
</table>
<br /><br />
<img src="<?php echo $mercuriale->getFolderPath().'plot_GW_RI_SY.svg' ?>" alt="Plot1" />
<p style="text-align: center; size:80%;">
<?php if(count($absents) > 0): ?>
L'absence de points pour <?php echo implode(", ", $absents) ?> est due à un nombre de transactions insuffisant<br />
<?php endif; ?>
nota : à partir du 01/12/12 le constat intègre les transactions de Négoce à Négoce (conformément à l'accord interprofessionnel &nbsp;&nbsp; * <?php echo $mercuriale->getEnd('Y')-1 ?>/<?php echo $mercuriale->getEnd('y') ?> = constats provisoires</p>