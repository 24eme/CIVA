<table border="0" cellspacing=0 cellpadding="0">
	<tr>
		<td style="width: 8%;"><img src="<?php echo sfConfig::get('sf_web_dir')."/images/pdf/civa.png" ?>" alt="CIVA-Logo" /></td>
		<td style="width: 92%; text-align: center;">
			<h1 style="color: #b1920c;"><span style="font-weight: normal; color: #000;">AOC Alsace</span>  PINOT BLANC - PINOT GRIS - PINOT NOIR</h1>
			<span>relevés bi-mensuels des cours des transactions en vrac entre opérateurs du vignoble d'Alsace AOC (relevés définitifs)</span>
		</td>
	</tr>
</table>
<br /><br />
<img src="<?php echo $mercuriale->getFolderPath().date("Ymd", mktime(1, 1, 1, date('m'), date('d') - 1, date('Y'))).'_plot_PN_PG_PB.svg' ?>" alt="Plot2" />
<p style="text-align: center; size:80%;">nota : à partir du 01/12/12 le constat intègre les transactions de Négoce à Négoce (conformément à l'accord interprofessionnel &nbsp;&nbsp; * <?php echo $mercuriale->getEnd('Y')-1 ?>/<?php echo $mercuriale->getEnd('y') ?> = constats provisoires</p>