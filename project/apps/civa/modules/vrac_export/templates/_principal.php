<?php use_helper('Float') ?>
<?php use_helper('Date') ?>
<?php  use_helper('vracExport'); ?>
<html class="no-js">
	<head>
	
	</head>
	<body>
<?php  include_partial("vrac_export/soussignes", array('vrac' => $vrac));  ?>
<small><br /></small>
<span style="background-color: black; color: white; font-weight: bold;">&nbsp;Transactions vrac&nbsp;</span><br/>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="text-align: right; border-collapse: collapse;">
	<tr>
		<th width="60px" style="font-weight: bold; text-align: center; border: 1px solid black;">AOC
		</th>
		<th width="320px" style="font-weight: bold; text-align: center; border: 1px solid black;">Produit</th>
		<th width="50px" style="font-weight: bold; text-align: center; border: 1px solid black;">Mill.</th>  
		<th width="70px" style="font-weight: bold; text-align: center; border: 1px solid black;">Prix*<br/><small>(en &euro;/HL)</small></th>
		<th width="70px" style="font-weight: bold; text-align: center; border: 1px solid black;">Volume estimé<br/><small>(en HL)</small></th>
		<?php if (1 || $vrac->isCloture()): ?>
			<th width="70px" style="font-weight: bold; text-align: center; border: 1px solid black;">Volume réel<br/><small>(en HL)</small></th>
		<?php endif; ?>
	</tr>
	<?php foreach ($vrac->declaration->getProduitsDetailsSorted() as $product): 
			$productLine = $product->getRawValue();
					foreach ($productLine as $detailKey => $detailLine): 
							$backgroundColor = getColorRowDetail($detailLine);
			?>
	<tr>
			<td width="60px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;"><span style="font-size: 5pt;"><?php echo $detailLine->getCepage()->getAppellation()->getCodeCiva(); ?></span></td>
			<td width="320px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: left;">&nbsp;<?php echo $detailLine->getCepage()->getLibelle(); echo " ".$detailLine->getLieuLibelle(); ?> <?php echo $detailLine->getLieuDit(); ?> <?php echo $detailLine->getVtsgn(); ?> <?php echo $detailLine->getDenomination(); ?></td>
			<td width="50px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;"><?php echo $detailLine->getMillesime(); ?>&nbsp;</td>    
			<td width="70px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echoPrix($detailLine->getPrixUnitaire(), true); ?></td>
			<td width="70px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echoVolume($detailLine->volume_propose, true); ?></td>
			<?php if (1 || $vrac->isCloture()): ?>
			<td width="70px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echoVolume($detailLine->volume_enleve, true); ?></td>
			<?php endif; ?>
	</tr>
	<?php 
		endforeach;
	endforeach; 
	?>

	<tr>
			<td style="text-align: left;" colspan="4" >&nbsp;</td>
			<td style="border: 2px solid black;"><?php echoVolume($vrac->getTotalVolumePropose(), true); ?></td>
			<?php if ($vrac->isCloture()): ?>
			<td style="border: 2px solid black;"><?php echoVolume($vrac->getTotalVolumeEnleve(), true); ?></td>
			<?php endif; ?>
	</tr>
</table>
<p>*&nbsp;<span style="font-size: 8pt; padding-left: 20px"><?php echo getExplicationEtoile(); ?></span></p>
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<br />&nbsp;
<p>Conditions de paiement : <?php echo $vrac->conditions_paiement; ?></p>
<p>Conditions particulières : <?php echo $vrac->conditions_particulieres; ?></p>
<br />
<br />

<!-- <?php for($i=0;$i<22-count($vrac->declaration->getProduitsDetailsSorted());$i++): ?>
		<br />
<?php endfor;?> -->

<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align: left; border-collapse: collapse;">
	<tr>
		<td width="33.33%" valign="top" style="border: 1px solid #000;">
			<table cellspacing="0" cellpadding="5" border="0" width="100%">
				<tr>
					<th style="background-color: grey; text-align: center; color: #FFF; font-weight: bold;">LE VENDEUR</th>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_vendeur): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_vendeur); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_vendeur): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
		<?php if($vrac->hasCourtier()): ?>
		<td width="33.33%" valign="top" style="border: 1px solid #000;">
			<table cellspacing="0" cellpadding="5" border="0" width="100%">
				<tr>
					<th style="background-color: grey; text-align: center; color: #FFF; font-weight: bold;">VU, le Courtier</th>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_mandataire): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_mandataire); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_mandataire): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
		<?php endif; ?>
		<td width="33.33%" valign="top" style="border: 1px solid #000;">
			<table cellspacing="0" cellpadding="5" border="0" width="100%">
				<tr>
					<th style="background-color: grey; text-align: center; color: #FFF; font-weight: bold;">L'ACHETEUR</th>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_acheteur): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_acheteur); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_acheteur): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
	</tr>

</table>
<br />
<br />

<table cellspacing="0" cellpadding="0" border="0" style="text-align: right;">
	<tr>
		<td style="text-align: left; font-size: 8pt;"><?php echo getLastSentence(); ?></td>
	</tr>
</table>
</body>
</html>
