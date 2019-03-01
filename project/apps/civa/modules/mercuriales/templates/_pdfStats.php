<?php 
$stats = $mercuriale->getStats();
$nbContrats = 0;
foreach ($stats as $stat) {
$nbContrats += $stat[VracMercuriale::OUT_CONTRAT];
}

$start = $mercuriale->getStart('Y-m').'-01';
$statsCR = null;
if ($start != $mercuriale->getStart('Y-m-d')) {
    $statsCR = $mercuriale->getStats($start, $mercuriale->getEnd('Y-m-d'), true);
    if (isset($statsCR['CR'])) {
        $statsCR = $statsCR['CR'];
    } else {
        $statsCR = null;
    }
}
?>
<table>
	<tr>
		<td style="width: 12%;"><img src="<?php echo sfConfig::get('sf_web_dir')."/images/pdf/civa.png" ?>" alt="CIVA-Logo" /></td>
		<td style="width: 88%; text-align: center;">
			<h1 style="text-decoration: underline;">MERCURIALE DES VINS D'ALSACE</h1>
			<span>Transactions en vrac entre Opérateurs du Vignoble Alsace AOC</span><br />
    		<span style="font-size: 80%;">(hors Grands-crus et hors vins de base Crémant d'Alsace)</span><br />
    		<span style="font-size: 80%;">(depuis le 01/12/2012, la mercuriale intègre les transactions de Négoce à Négoce,<br />conformément à l'article 5 de l'Accord Interprofessionnel)</span>	
		</td>
	</tr>
</table>

<table>
	<tr>
		<td style="width: 31%;"></td>
		<td>
            <p>PERIODE DU : <strong><?php echo $mercuriale->getStart() ?></strong> AU <strong><?php echo $mercuriale->getEnd() ?></strong></p>
            <p>ARRETE PROVISOIRE AU : <strong><?php echo date('d/m/Y') ?></strong></p>
            <p>NOMBRE DE CONTRATS ENREGISTRES AU CIVA : <strong><?php echo $nbContrats ?></strong></p>
    	</td>
    </tr>
</table>

    <br />
    <p>&nbsp;</p>
    <br />
    
<table border="0" cellspacing=0 cellpadding="0" style="width: 100%;">
	<tr>
		<td style="width: 10%;"></td>
		<td style="width: 80%;">
			<table border="0" cellspacing=0 cellpadding="12" style="width: 100%;">
        		<tr>
        			<th style="font-weight: bold; text-align: center; width: 30%; border: 1px solid black;">CEPAGES</th>
        			<th style="font-weight: bold; text-align: center; width: 20%; border: 1px solid black;">NOMBRE<br />DE LOTS</th>
        			<th style="font-weight: bold; text-align: center; width: 25%; border: 1px solid black;">VOLUME VENDU<br />EN HL</th>
        			<th style="font-weight: bold; text-align: center; width: 25%; border: 1px solid black;">PRIX MOYEN<br />EUROS/L</th>
        		</tr>
        		<?php 
        		  $nb = 0;
        		  $vol = 0;
        		  $i = 0;
        		  $total = count($stats);
        		  foreach ($stats as $stat):
        		  $nb += $stat[VracMercuriale::OUT_NB];
        		  $i++;
        		  $vol += str_replace(',', '.', $stat[VracMercuriale::OUT_VOL]) * 1;
        		?>
        		<tr>
        			<td style="width: 30%; border-left: 1px solid black;<?php if($i == $total): ?> border-bottom: 1px solid black;<?php endif;?>"><?php echo strtoupper($stat[VracMercuriale::OUT_CP_LIBELLE]) ?></td>
        			<td style="text-align: right; width: 20%; border-left: 1px solid black;<?php if($i == $total): ?> border-bottom: 1px solid black;<?php endif;?>"><?php echo $stat[VracMercuriale::OUT_NB] ?></td>
        			<td style="text-align: right; width: 25%; border-left: 1px solid black;<?php if($i == $total): ?> border-bottom: 1px solid black;<?php endif;?>"><?php echo number_format(str_replace(',', '.', $stat[VracMercuriale::OUT_VOL]) * 1, 2, ',', '.') ?></td>
        			<td style="text-align: right; width: 25%; border-left: 1px solid black; border-right: 1px solid black;<?php if($i == $total): ?> border-bottom: 1px solid black;<?php endif;?>"><?php echo ($stat[VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $stat[VracMercuriale::OUT_PRIX] : '*' ?></td>
        		</tr>
        		<?php endforeach; ?>
        	</table>
        	<br /><br />
        	<table border="1" cellspacing=0 cellpadding="12" style="width: 100%;">
        		<tr>
        			<td style="text-align: right; width: 30%;"><strong>TOTAL</strong></td>
        			<td style="text-align: right; width: 20%;"><strong><?php echo $nb ?></strong></td>
        			<td style="text-align: right; width: 25%;"><strong><?php echo number_format($vol, 2, ',', '.') ?></strong></td>
        		</tr>
        	</table>
        	<p>&nbsp;</p>
        	<p>* nombre minimum de lots non-atteint pour publication</p>
        	<?php if($statsCR): ?>
        	<p>&nbsp;</p>
        	<h2><span style="text-decoration: underline;">Vins de base Crémant d'Alsace</span> <span style="font-size: 80%">Période du <?php echo  '01/'.$mercuriale->getStart('m/Y') ?> au <?php echo $mercuriale->getEnd() ?></span></h2>
        	<p>Nombre de lots : <strong><?php echo $statsCR[VracMercuriale::OUT_NB] ?></strong> &nbsp;&nbsp; Volume : <strong><?php echo number_format(str_replace(',', '.', $statsCR[VracMercuriale::OUT_VOL]) * 1, 2, ',', '.') ?></strong>&nbsp;hl &nbsp;&nbsp; Prix moyen : <strong><?php echo ($statsCR[VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $statsCR[VracMercuriale::OUT_PRIX] : '*' ?></strong></p>
        	<?php endif; ?>
		</td>
		<td style="width: 10%;"></td>
	</tr>
</table>