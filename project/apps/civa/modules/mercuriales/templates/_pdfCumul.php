<table border="0" cellspacing=0 cellpadding="0">
	<tr>
		<td style="width: 8%;"><img src="<?php echo sfConfig::get('sf_web_dir')."/images/pdf/civa.png" ?>" alt="CIVA-Logo" /></td>
		<td style="width: 92%; text-align: center;">
			<h1 style="text-decoration: underline;">CUMUL PERMANENT PROVISOIRE DES CONTRATS DE VENTE DE VINS D'ALSACE<br />CONVENTIONNEL</h1>
			<span>Transactions en vrac entre Opérateurs du vignoble Alsace AOC(hors Grands-crus et hors vins de base Crémant d'Alsace)</span>
		</td>
	</tr>
	<tr>
		<td style="width: 20%;"></td>
		<td style="width: 40%; text-align: left;">
			<p>CAMPAGNE <strong><?php echo $mercuriale->getBeginPrevious('Y') ?>/<?php echo $mercuriale->getBeginPrevious('Y')+1 ?></strong> DU : <strong><?php echo $mercuriale->getBeginPrevious('d/m/Y'); ?></strong> AU <strong><?php echo $mercuriale->getEndPrevious('d/m/Y'); ?></strong>
			<br />
			CAMPAGNE <strong><?php echo $mercuriale->getBegin('Y') ?>/<?php echo $mercuriale->getBegin('Y')+1 ?></strong> DU : <strong><?php echo $mercuriale->getBegin('d/m/Y'); ?></strong> AU <strong><?php echo $mercuriale->getEnd('d/m/Y'); ?></strong></p>
		</td>
		<td style="width: 40%; text-align: right;">
			<p>ARRETE PROVISOIRE AU : <strong><?php echo date('d/m/Y') ?></strong></p>
		</td>
	</tr>
</table>
<br /><br />
<table border="0" cellspacing=0 cellpadding="0">
		<tr>
			<th style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; width: 23%"><strong>CEPAGES</strong></th>
			<th colspan="2" style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; width: 22%"><strong><?php echo $mercuriale->getBeginPrevious('Y') ?>/<?php echo $mercuriale->getBeginPrevious('Y') + 1 ?></strong></th>
			<th colspan="2" style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; width: 22%"><strong><?php echo $mercuriale->getBegin('Y') ?>/<?php echo $mercuriale->getBegin('Y') + 1 ?></strong></th>
			<th colspan="3" style="text-align: center; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; width: 33%"><strong>VARIATION</strong></th>
		</tr>
		<tr>
			<th style="text-align: center; border-left: 1px solid black; width: 23%"><strong>CONVENTIONNEL</strong></th>
			<th style="text-align: center; border-left: 1px solid black; width: 11%"><strong>VOLUME&nbsp;VENDU</strong></th>
			<th style="text-align: center; width: 11%"><strong>PRIX&nbsp;MOYEN</strong></th>
			<th style="text-align: center; border-left: 1px solid black; width: 11%"><strong>VOLUME&nbsp;VENDU</strong></th>
			<th style="text-align: center; width: 11%"><strong>PRIX&nbsp;MOYEN</strong></th>
			<th colspan="2" style="text-align: center; border-left: 1px solid black; width: 22%"><strong>VOLUME</strong></th>
			<th style="text-align: center; border-left: 1px solid black; border-right: 1px solid black; width: 11%"><strong>PRIX&nbsp;MOYEN</strong></th>
		</tr>
		<tr>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 23%">&nbsp;</th>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><strong>HL</strong></th>
			<th style="text-align: center; border-bottom: 1px solid black; width: 11%"><strong>EUROS/L</strong></th>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><strong>HL</strong></th>
			<th style="text-align: center; border-bottom: 1px solid black; width: 11%"><strong>EUROS/L</strong></th>
			<th style="text-align: center; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><strong>HL</strong></th>
			<th style="text-align: center; border-bottom: 1px solid black; width: 11%"><strong>%</strong></th>
			<th style="text-align: center; border-right: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><strong>%</strong></th>
		</tr>
	</table>
	
	<table border="0" cellspacing=0 cellpadding="10">
		<?php 
		  $stats = $mercuriale->getCumul(false, 0); 
		  $volCur = 0;
		  $volPrev = 0;
		  foreach ($stats as $k => $stat):
		  if ($k == VracMercuriale::OUT_STATS) {
		      continue;
		  }
		  $volCur += str_replace(',', '.', $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_VOL]) * 1;
		  $volPrev += str_replace(',', '.', $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_VOL]) * 1;
		?>
		<tr>
			<td style="text-align: left; border-left: 1px solid black; border-bottom: 1px solid black; width: 23%"><?php echo strtoupper($stat[VracMercuriale::OUT_CP_LIBELLE]) ?> <span style="font-size: 80%;">Conventionnel</span></td>
			<td style="text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ') ?></td>
			<td style="text-align: right; border-bottom: 1px solid black; width: 11%"><?php echo ($stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $stat[VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_PRIX] : '*' ?></td>
			<td style="text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ') ?></td>
			<td style="text-align: right; border-bottom: 1px solid black; width: 11%"><?php echo ($stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $stat[VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_PRIX] : '*' ?></td>
			<td style="text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ') ?></td>
			<td style="text-align: right; border-bottom: 1px solid black; width: 11%"><?php echo ($stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL_PERC] > 0)? "+".$stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL_PERC] : $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_VOL_PERC]; ?></td>
			<td style="text-align: right; border-right: 1px solid black; border-left: 1px solid black; border-bottom: 1px solid black; width: 11%"><?php echo ($stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_PRIX_PERC] > 0)? "+".$stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_PRIX_PERC] : $stat[VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_PRIX_PERC]; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
    <br /><br />
	<table border="0" cellspacing=0 cellpadding="10">
		<tr>
			<td style=" width: 23%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong>TOTAL <span style="font-size: 80%;">CONVENTIONNEL</span></strong></td>
			<td style=" width: 11%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format($volPrev, 2, ',', ' ') ?></strong></td>
			<td style=" width: 11%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
			<td style=" width: 11%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format($volCur, 2, ',', ' ') ?></strong></td>
			<td style=" width: 11%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
			<td style=" width: 11%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo number_format(($volCur - $volPrev), 2, ',', ' ') ?></strong></td>
			<td style=" width: 11%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo (round(($volCur - $volPrev) / $volPrev * 100) > 0)? "+".round(($volCur - $volPrev) / $volPrev * 100) : round(($volCur - $volPrev) / $volPrev * 100) ?></strong></td>
			<td style=" width: 11%; text-align: right; border-right: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
		</tr>
	</table>
    <br /><br />
    <table border="0" cellspacing=0 cellpadding="10">
		<tr>
			<td style=" width: 23%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong>NOMBRE CONTRATS</strong></td>
			<td colspan="2" style=" width: 22%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_CONTRAT] ?></strong></td>
			<td colspan="2" style=" width: 22%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_CONTRAT] ?></strong></td>
			<td style=" width: 11%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_CONTRAT] ?></strong></td>
			<td style=" width: 11%; text-align: right; border-bottom: 1px solid black; border-top: 1px solid black;"><strong><?php echo (round($stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_CONTRAT] / $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_CONTRAT] * 100) > 0)? "+".round($stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_CONTRAT] / $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_CONTRAT] * 100) : round($stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_CONTRAT] / $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_CONTRAT] * 100); ?></strong></td>
			<td style=" width: 11%; text-align: right; border-right: 1px solid black; border-bottom: 1px solid black; border-top: 1px solid black;"></td>
		</tr>
		<tr>
			<td style=" width: 23%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black;"><strong>NOMBRE LOTS</strong></td>
			<td colspan="2" style=" width: 22%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black;"><strong><?php echo $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB] ?></strong></td>
			<td colspan="2" style=" width: 22%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black;"><strong><?php echo $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_CURRENT][VracMercuriale::OUT_NB] ?></strong></td>
			<td style=" width: 11%; text-align: right; border-left: 1px solid black; border-bottom: 1px solid black;"><strong><?php echo $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_NB] ?></strong></td>
			<td style=" width: 11%; text-align: right; border-bottom: 1px solid black;"><strong><?php echo (round($stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_NB] / $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB] * 100) > 0)? "+".round($stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_NB] / $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB] * 100) : round($stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_VARIATION][VracMercuriale::OUT_NB] / $stats[VracMercuriale::OUT_STATS][VracMercuriale::OUT_PREVIOUS][VracMercuriale::OUT_NB] * 100); ?></strong></td>
			<td style=" width: 11%; text-align: right; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
		</tr>
	</table>
<?php
    $stats = $mercuriale->getStats($mercuriale->getBeginPrevious('Y').'1201', ($mercuriale->getBeginPrevious('Y') + 1).'1130', false, 0);
    $vol = 0;
    $nbContrats = count($mercuriale->getAllContrats());
    foreach ($stats as $stat):
    $vol += str_replace(',', '.', $stat[VracMercuriale::OUT_VOL]) * 1;
    endforeach;
?>
<p>NOTA : &nbsp;&nbsp; TOTAL DEFINITIF EN FIN DE CAMPAGNE : <strong><?php echo $mercuriale->getBeginPrevious('Y') ?>/<?php echo $mercuriale->getBeginPrevious('Y') + 1 ?></strong> &nbsp;&nbsp; VOLUME : <strong><?php echo number_format($vol, 2, ',', ' ') ?></strong>&nbsp;hl &nbsp;&nbsp; NOMBRE DE CONTRATS : <strong><?php echo $nbContrats ?></strong></p>
