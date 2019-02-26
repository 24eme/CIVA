<table border="0" cellspacing=0 cellpadding="0">
	<tr>
		<td style="width: 8%;"><img src="<?php echo sfConfig::get('sf_web_dir')."/images/pdf/civa.png" ?>" alt="CIVA-Logo" /></td>
		<td style="width: 92%; text-align: center;">
			<h1 style="text-decoration: underline;">CUMUL PERMANENT PROVISOIRE DES CONTRATS DE VENTE DE VINS D'ALSACE</h1>
			<span>Transactions en vrac entre Opérateurs du vignoble Alsace AOC(hors Grands-crus et hors vins de base Crémant d'Alsace)</span>
		</td>
	</tr>
	<tr>
		<td style="width: 20%;"></td>
		<td style="width: 40%; text-align: left;">
			<p>CAMPAGNE <strong><?php echo $mercuriale->getEnd('Y')-2 ?>/<?php echo $mercuriale->getEnd('Y')-1 ?></strong> DU : <strong><?php echo '01/12/'.($mercuriale->getEnd('Y')-2) ?></strong> AU <strong><?php echo $mercuriale->getEnd('m/d').'/'.($mercuriale->getEnd('Y')-1) ?></strong>
			<br />
			CAMPAGNE <strong><?php echo $mercuriale->getEnd('Y')-1 ?>/<?php echo $mercuriale->getEnd('Y') ?></strong> DU : <strong><?php echo '01/12/'.($mercuriale->getEnd('Y')-1) ?></strong> AU <strong><?php echo $mercuriale->getEnd() ?></strong></p>
		</td>
		<td style="width: 40%; text-align: right;">
			<p>ARRETE PROVISOIRE AU : <strong><?php echo date('d/m/Y') ?></strong></p>
		</td>
	</tr>
</table>
<br /><br />
<table border="0" cellspacing=0 cellpadding="0">
		<tr>
			<th style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; width: 16%">&nbsp;</th>
			<th colspan="2" style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; width: 24%"><strong><?php echo $mercuriale->getEnd('Y')-2 ?>/<?php echo $mercuriale->getEnd('Y')-1 ?></strong></th>
			<th colspan="2" style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; width: 24%"><strong><?php echo $mercuriale->getEnd('Y')-1 ?>/<?php echo $mercuriale->getEnd('Y') ?></strong></th>
			<th colspan="3" style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; width: 36%"><strong>VARIATION</strong></th>
		</tr>
		<tr>
			<th style="text-align: center; border-left: 1px solid black; width: 16%"><strong>CEPAGES</strong></th>
			<th style="text-align: center; border-left: 1px solid black; width: 12%"><strong>VOLUME&nbsp;VENDU</strong></th>
			<th style="text-align: center; width: 12%"><strong>PRIX&nbsp;MOYEN</strong></th>
			<th style="text-align: center; border-left: 1px solid black; width: 12%"><strong>VOLUME&nbsp;VENDU</strong></th>
			<th style="text-align: center; width: 12%"><strong>PRIX&nbsp;MOYEN</strong></th>
			<th colspan="2" style="text-align: center; border-left: 1px solid black; width: 24%"><strong>VOLUME</strong></th>
			<th style="text-align: center; border-left: 1px solid black; border-right: 1px solid black; width: 12%"><strong>PRIX&nbsp;MOYEN</strong></th>
		</tr>
		<tr>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 16%">&nbsp;</th>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><strong>HL</strong></th>
			<th style="text-align: center; border-bottom: 1px solid black; width: 12%"><strong>EUROS/L</strong></th>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><strong>HL</strong></th>
			<th style="text-align: center; border-bottom: 1px solid black; width: 12%"><strong>EUROS/L</strong></th>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><strong>HL</strong></th>
			<th style="text-align: center; border-bottom: 1px solid black; width: 12%"><strong>%</strong></th>
			<th style="text-align: center; border-right: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><strong>%</strong></th>
		</tr>
	</table>
	
	<table border="0" cellspacing=0 cellpadding="10">
		<?php 
		  $stats = $mercuriale->getCumul(); 
		  $nbCur = 0;
		  $nbPrev = 0;
		  $nbVar = 0;
		  $nbContratCur = 0;
		  $nbContratPrev = 0;
		  $nbContratVar = 0;
		  $volCur = 0;
		  $volPrev = 0;
		  $volVar = 0;
		  foreach ($stats as $stat):
		  $nbCur += $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_NB];
		  $nbPrev += $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB];
		  $nbVar += $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_NB];
		  $nbContratCur += $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_CONTRAT];
		  $nbContratPrev += $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_CONTRAT];
		  $nbContratVar += $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_CONTRAT];
		  $volCur += str_replace(',', '.', $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_VOL]) * 1;
		  $volPrev += str_replace(',', '.', $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_VOL]) * 1;
		  $volVar += str_replace(',', '.', $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL]) * 1;
		?>
		<tr>
			<td style="text-align: left; border-left: 1px solid black; border-bottom: 1px solid black; width: 16%"><?php echo strtoupper($stat[VracMercuriale::OUT_CP_LIBELLE]) ?></td>
			<td style="text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_VOL]) * 1, 2, ',', '.') ?></td>
			<td style="text-align: right; border-bottom: 1px solid black; width: 12%"><?php echo ($stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_PRIX] : '*' ?></td>
			<td style="text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_VOL]) * 1, 2, ',', '.') ?></td>
			<td style="text-align: right; border-bottom: 1px solid black; width: 12%"><?php echo ($stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_PRIX] : '*' ?></td>
			<td style="text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL]) * 1, 2, ',', '.') ?></td>
			<td style="text-align: right; border-bottom: 1px solid black; width: 12%"><?php echo $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL_PERC] ?></td>
			<td style="text-align: right; border-right: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; width: 12%"><?php echo $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_PRIX_PERC] ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
    <br /><br />
	<table border="0" cellspacing=0 cellpadding="10">
		<tr>
			<td style=" width: 16%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong>TOTAL</strong></td>
			<td style=" width: 12%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format($volPrev, 2, ',', '.') ?></strong></td>
			<td style=" width: 12%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
			<td style=" width: 12%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format($volCur, 2, ',', '.') ?></strong></td>
			<td style=" width: 12%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
			<td style=" width: 12%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format($volVar, 2, ',', '.') ?></strong></td>
			<td style=" width: 12%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format(round($volVar / $volCur * 100), 2, ',', '.') ?></strong></td>
			<td style=" width: 12%; text-align: right; border-right: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
		</tr>
	</table>
    <br /><br />
    <table border="0" cellspacing=0 cellpadding="10">
		<tr>
			<td style=" width: 16%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong>NOMBRE CONTRATS</strong></td>
			<td colspan="2" style=" width: 24%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo $nbContratPrev ?></strong></td>
			<td colspan="2" style=" width: 24%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo $nbContratCur ?></strong></td>
			<td style=" width: 12%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo $nbContratVar ?></strong></td>
			<td style=" width: 12%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format(round($nbContratVar / $nbContratCur * 100), 2, ',', '.') ?></strong></td>
			<td style=" width: 12%; text-align: right; border-right: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
		</tr>
		<tr>
			<td style=" width: 16%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black;"><strong>NOMBRE LOTS</strong></td>
			<td colspan="2" style=" width: 24%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black;"><strong><?php echo $nbPrev ?></strong></td>
			<td colspan="2" style=" width: 24%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black;"><strong><?php echo $nbCur ?></strong></td>
			<td style=" width: 12%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black;"><strong><?php echo $nbVar ?></strong></td>
			<td style=" width: 12%; text-align: right; border-bottom: 1px solid black;"><strong><?php echo number_format(round($nbVar / $nbCur * 100), 2, ',', '.') ?></strong></td>
			<td style=" width: 12%; text-align: right; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
		</tr>
	</table>
<?php 
    $stats = $mercuriale->getStats(($mercuriale->getEnd('Y')-2).'1201', ($mercuriale->getEnd('Y')-1).'1130'); 
    $nb = 0;
    $vol = 0;
    foreach ($stats as $stat):
    $nb += $stat[VracMercuriale::OUT_CONTRAT];
    $vol += str_replace(',', '.', $stat[VracMercuriale::OUT_VOL]) * 1;
    endforeach;
?>
<p>NOTA : &nbsp;&nbsp; TOTAL DEFINITIF EN FIN DE CAMPAGNE : <strong><?php echo $mercuriale->getEnd('Y')-2 ?>/<?php echo $mercuriale->getEnd('Y')-1 ?></strong> &nbsp;&nbsp; VOLUME : <strong><?php echo number_format($vol, 2, ',', '.') ?></strong>&nbsp;hl &nbsp;&nbsp; NOMBRE DE CONTRATS : <strong><?php echo $nb ?></strong></p>