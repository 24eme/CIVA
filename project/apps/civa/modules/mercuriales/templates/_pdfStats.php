<?php 
$stats = $mercuriale->getStats()->getRawValue();
$statsBio = $mercuriale->getStats(null, null, false, 1)->getRawValue();
$keys = VracMercuriale::$ordres;
unset($keys['CR']);
asort($keys);
$cepages = VracMercuriale::$cepages;
$nbContrats = count(array_merge($mercuriale->getAllContrats()->getRawValue(), $mercuriale->getAllContratsBio()->getRawValue()));
$nbLots = count($mercuriale->getAllLots());
$nbLotsBio = count($mercuriale->getAllLotsBio());

$start = $mercuriale->getStart('Y-m').'-01';
$end = new DateTime($mercuriale->getEnd('Y-m-d'));
$end->modify('+1 day');
$statsCRConv = null;
$statsCRBio = null;
if ($end->format('m') != $mercuriale->getEnd('m')) {
    $ordre = VracMercuriale::$ordres;
    $key = $ordre['CR'].'CR';
    $statsCRConv = $mercuriale->getStats($start, $mercuriale->getEnd('Y-m-d'), true, 0);
    $statsCRConv = (isset($statsCRConv[$key])) ? $statsCRConv[$key] : null ;
    $statsCRBio = $mercuriale->getStats($start, $mercuriale->getEnd('Y-m-d'), true, 1);
    $statsCRBio = (isset($statsCRBio[$key])) ? $statsCRBio[$key] : null ;
}
?>
<table>
	<tr>
		<td style="width: 12%;"><img src="<?php echo sfConfig::get('sf_web_dir')."/images/pdf/civa.png" ?>" alt="CIVA-Logo" /></td>
		<td style="width: 88%; text-align: center;">
			<h1 style="text-decoration: underline;">MERCURIALE DES VINS D'ALSACE</h1>
			<span>Transactions en vrac entre Opérateurs du Vignoble Alsace AOC</span><br />
    		<span style="font-size: 80%;">(hors Grands-crus et hors vins de base Crémant d'Alsace)</span><br />
    		<span style="font-size: 80%;">(depuis le 01/12/2019, la mercuriale distingue de manière systématique,<br />les transactions en bio et en conventionnel)</span>	
		</td>
	</tr>
</table>

<table>
	<tr>
		<td style="width: 31%;"></td>
		<td>
            <p>PERIODE DU : <strong><?php echo $mercuriale->getStart() ?></strong> AU <strong><?php echo $mercuriale->getEnd() ?></strong><br />
            ARRETE PROVISOIRE AU : <strong><?php echo date('d/m/Y') ?></strong><br />
            NOMBRE DE CONTRATS ENREGISTRES AU CIVA : <strong><?php echo $nbContrats ?></strong></p>
    	</td>
    </tr>
</table>

<p>&nbsp;</p>
    
<table border="0" cellspacing=0 cellpadding="0" style="width: 100%;">
	<tr>
		<td style="width: 100%;">
			<table border="0" cellspacing=0 cellpadding="<?php if($nbLotsBio > 0): ?>8<?php else: ?>12<?php endif; ?>" style="width: 100%;">
        		<tr>
        			<th style="font-weight: bold; text-align: center; width: 35%; border: 1px solid black;">CEPAGES</th>
        			<th style="font-weight: bold; text-align: center; width: 15%; border: 1px solid black;">NOMBRE<br />DE LOTS</th>
        			<th style="font-weight: bold; text-align: center; width: 25%; border: 1px solid black;">VOLUME VENDU<br />EN HL</th>
        			<th style="font-weight: bold; text-align: center; width: 25%; border: 1px solid black;">PRIX MOYEN<br />EUROS/L</th>
        		</tr>
        		<?php
        		  $vol = 0;
        		  $volBio = 0;
        		  foreach ($keys as $key => $v):
        		      $k = $v.$key;
            		  if (isset($stats[$k])) {
            		      $vol += str_replace(',', '.', $stats[$k][VracMercuriale::OUT_VOL]) * 1;
            		  }
        		?>
                <?php if (isset($stats[$k]) && $stats[$k][VracMercuriale::OUT_NB]): ?>
        		<tr>
        			<td style="width: 35%; border-left: 1px solid black;"><?php echo strtoupper($cepages[$key]) ?>  Conventionnel</td>
        			<td style="text-align: right; width: 15%; border-left: 1px solid black;"><?php echo (isset($stats[$k]))? $stats[$k][VracMercuriale::OUT_NB] : 0; ?></td>
        			<td style="text-align: right; width: 25%; border-left: 1px solid black;"><?php echo (isset($stats[$k]))? ($stats[$k][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? number_format(str_replace(',', '.', $stats[$k][VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ') : "*" : '*'; ?></td>
        			<td style="text-align: right; width: 25%; border-left: 1px solid black; border-right: 1px solid black;"><?php echo (isset($stats[$k]))? ($stats[$k][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $stats[$k][VracMercuriale::OUT_PRIX] : '*' : '*'; ?></td>
        		</tr>
                <?php endif; ?>
                <?php endforeach; ?>
                <tr>
        			<td style="text-align: right; width: 35%; border: 1px solid black;"><strong style="color: #b1920c;">TOTAL Conventionnel</strong></td>
        			<td style="text-align: right; width: 15%; border: 1px solid black;"><strong style="color: #b1920c;"><?php echo $nbLots ?></strong></td>
        			<td style="text-align: right; width: 25%; border: 1px solid black;"><strong style="color: #b1920c;"><?php echo number_format($vol, 2, ',', ' ') ?></strong></td>
                    <td style="text-align: right; width: 25%; border-top: 1px solid black; <?php if ($nbLotsBio) echo 'border-bottom: 1px solid black;'; ?>">&nbsp;</td>
        		</tr>
                <?php if($nbLotsBio > 0):
                foreach ($keys as $key => $v):
                    $k = $v.$key;
                    if (isset($statsBio[$k])) {
                        $volBio += str_replace(',', '.', $statsBio[$k][VracMercuriale::OUT_VOL]) * 1;
                    }
                ?>
        		<?php if (isset($statsBio[$k]) && $statsBio[$k][VracMercuriale::OUT_NB]): ?>
        		<tr>
        			<td style="width: 35%; border-left: 1px solid black;"><?php echo strtoupper($cepages[$key]) ?> Biologique</td>
        			<td style="text-align: right; width: 15%; border-left: 1px solid black;"><?php echo (isset($statsBio[$k]))? $statsBio[$k][VracMercuriale::OUT_NB] : 0; ?></td>
        			<td style="text-align: right; width: 25%; border-left: 1px solid black;"><?php echo (isset($statsBio[$k]))? ($statsBio[$k][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? number_format(str_replace(',', '.', $statsBio[$k][VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ') : "*" : '*'; ?></td>
        			<td style="text-align: right; width: 25%; border-left: 1px solid black; border-right: 1px solid black;"><?php echo (isset($statsBio[$k]))? ($statsBio[$k][VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $statsBio[$k][VracMercuriale::OUT_PRIX] : '*' : '*'; ?></td>
        		</tr>
        		<?php endif; ?>
        		<?php endforeach; ?>
        		<tr>
        			<td style="text-align: right; width: 35%; border: 1px solid black;"><strong style="color: #b1920c;">TOTAL Biologique</strong></td>
        			<td style="text-align: right; width: 15%; border: 1px solid black;"><strong style="color: #b1920c;"><?php echo $nbLotsBio ?></strong></td>
        			<td style="text-align: right; width: 25%; border: 1px solid black;"><strong style="color: #b1920c;"><?php echo number_format($volBio, 2, ',', ' ') ?></strong></td>
                    <td style="text-align: right; width: 25%; border-top: 1px solid black;">&nbsp;</td>
        		</tr>
        		<?php endif; ?>
        	</table>
        	<p>* non significatif : nombre minimum de lots non-atteint pour publication (secret statistique)</p>
            <?php if($mercuriale->hasWithCremant()): ?>
        	<h2><span style="text-decoration: underline;">Vins de base Crémant d'Alsace</span> <span style="font-size: 80%">Période du <?php echo  '01/'.$mercuriale->getStart('m/Y') ?> au <?php echo $mercuriale->getEnd() ?></span></h2>
            <table>
                <?php if($statsCRConv): ?>
                <tr>
                    <td>Nombre de lots Conventionnels : <strong><?php echo $statsCRConv[VracMercuriale::OUT_NB] ?></strong></td>
                    <td>Volume Conventionnel : <strong><?php echo ($statsCRConv[VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? number_format(str_replace(',', '.', $statsCRConv[VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ')."&nbsp;hl" : '*' ?></strong></td>
                    <td>Prix moyen : <strong><?php echo ($statsCRConv[VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $statsCRConv[VracMercuriale::OUT_PRIX] : '*' ?></strong></td>
                </tr>
                <?php else: ?>
                <tr><td>Nombre de lots Conventionnels : <strong>0</strong></tr>
                <?php endif; ?>
                <?php if($statsCRBio): ?>
                <tr>
                    <td>Nombre de lots Biologiques : <strong><?php echo $statsCRBio[VracMercuriale::OUT_NB] ?></strong></td>
                    <td>Volume Biologique : <strong><?php echo ($statsCRBio[VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? number_format(str_replace(',', '.', $statsCRBio[VracMercuriale::OUT_VOL]) * 1, 2, ',', ' ')."&nbsp;hl" : '*' ?></strong></td>
                    <td>Prix moyen : <strong><?php echo ($statsCRBio[VracMercuriale::OUT_NB] >= VracMercuriale::NB_MIN_TO_AGG)? $statsCRBio[VracMercuriale::OUT_PRIX] : '*' ?></strong></td>
                </tr>
                <?php else: ?>
                <tr><td>Nombre de lots Biologique : <strong>0</strong></td></tr>
                <?php endif; ?>
                </table>
            <?php endif; ?>
		</td>
	</tr>
</table>
