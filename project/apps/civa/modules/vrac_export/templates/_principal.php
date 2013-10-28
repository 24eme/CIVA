<?php use_helper('Float') ?>
<?php use_helper('Date') ?>
<?php  use_helper('vracExport'); ?>
<?php  include_partial("vrac_export/soussignes", array('vrac' => $vrac));  ?>
<small><br /></small>
<span style="display: block; background-color: black; color: white; font-weight: bold; text-align: center; padding: 5px; width: 220px;">&nbsp;<?php echo "TRANSACTIONS EN VRAC"; ?>&nbsp;</span>

<table border="0" cellspacing="0" cellpadding="0" width="100%" style="text-align: right; border-collapse: collapse;">
	<tr>
		<th style="width: 34px; font-weight: bold; text-align: center; border: 1px solid black; ">AOC
			<span style="font-size:8px;">
				<br/>&nbsp;<b>1</b> Alsace
				<br/>&nbsp;<b>2</b> Crémant
				<br/>&nbsp;<b>3</b> Grands Crus
			</span>
		</th>
		<th style="width: 116px; font-weight: bold; text-align: center; border: 1px solid black; ">Cépage</th>
		<th style="width: 138px; font-weight: bold; text-align: center; border: 1px solid black; "><small>Dénominations spécifiques, communales, lieux-dits, VT/SGN, etc...</small></th>
		<th style="width: 66px; font-weight: bold; text-align: center; border: 1px solid black; ">Millésime</th>  
		<th style="width: 92px; font-weight: bold; text-align: center; border: 1px solid black; ">Prix* de l'hectolitre <small>(en &euro;/HL)</small></th>
		<th style="width: 77px; font-weight: bold; text-align: center; border: 1px solid black; ">VOLUME estimé<br/><small>(en HL)</small></th>
		<?php if ($vrac->isCloture()): ?>
			<th style="width: 77px; font-weight: bold; text-align: center; border: 1px solid black; ">VOLUME réel<br/><small>(en HL)</small></th>
		<?php endif; ?>
	</tr>
	<?php foreach ($vrac->declaration->getProduitsDetailsSorted() as $product): 
			$productLine = $product->getRawValue();
					foreach ($productLine as $detailKey => $detailLine): 
							$backgroundColor = getColorRowDetail($detailLine);
			?>
	<tr>
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;"><?php echo $detailLine->getCepage()->getAppellation()->getCodeCiva(); ?></td>
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: left;">&nbsp;<?php echo $detailLine->getCepage()->getLibelle(); ?></td>
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echo $detailLine->getDenomination(); ?> <?php echo $detailLine->getLieuDit(); ?> <?php echo $detailLine->getVtsgn(); ?></td>
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;"><?php echo $detailLine->getMillesime(); ?>&nbsp;</td>    
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echoPrix($detailLine->getPrixUnitaire(), true); ?></td>
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echoVolume($detailLine->volume_propose, true); ?></td>
			<?php if ($vrac->isCloture()): ?>
			<td style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echoVolume($detailLine->volume_enleve, true); ?></td>
			<?php endif; ?>
	</tr>
	<?php 
		endforeach;
	endforeach; 
	?>

	<tr>
			<td style="border: none; text-align: left;" colspan="4" >&nbsp;*<span style="font-size:20px; padding-left: 20px"><?php echo getExplicationEtoile(); ?></span></td>
			<td style="border: 2px solid black;"><?php echoVolume($vrac->getTotalVolumePropose(), true); ?></td>
			<td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>    
			<?php if ($vrac->isCloture()): ?>
			<td style="border: 2px solid black;"><?php echoVolume($vrac->getTotalVolumeEnleve(), true); ?></td>
			<?php endif; ?>
	</tr>
</table>

<br />
<br />
<br />
<table cellspacing="10" cellpadding="0" border="0" width="100%" style="text-align: right;">
	<tr>
		<td style="text-align: left; width: 200px; border: none; font-weight: bold;">Conditions de paiement :</td>
		<td style="text-align: left;"><?php echo $vrac->conditions_paiement; ?></td>
	</tr>
	<tr>
		<td style="text-align: left; width: 200px; border: none; font-weight: bold;">Conditions particulières :</td>
		<td style="text-align: left;"><?php echo $vrac->conditions_particulieres; ?></td>
	</tr>
</table>
<br />
<br />
<?php for($i=0;$i<22-count($vrac->declaration->getProduitsDetailsSorted());$i++): ?>
		<br />
<?php endfor;?>

<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align: left;">
<?php $widthAcheteur = ($vrac->hasCourtier())? "width: 210px;" : "width: 420px;"; ?>
	<tr>
		<td width="33%" valign="top" style="padding-right: 10px;">
			<span style="display: block; padding: 5px; background: grey; text-align: center; color: #FFF; font-weight: bold; width: 150px;">LE VENDEUR</span>
			<table cellspacing="0" cellpadding="5" border="0" width="100%" style="border: 1px solid #000;">
				<tr>
					<td><?php if ($vrac->valide->date_validation_vendeur): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_vendeur); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_vendeur): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
		<?php if($vrac->hasCourtier()): ?>
		<td width="33%" valign="top">
			<span style="display: block; padding: 5px; background: grey; text-align: center; color: #FFF; font-weight: bold; width: 150px;">VU, le Courtier</span>
			<table cellspacing="0" cellpadding="5" border="0" width="100%" style="border: 1px solid #000;">
				<tr>
					<td><?php if ($vrac->valide->date_validation_mandataire): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_mandataire); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_mandataire): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
		<?php endif; ?>
		<td width="33%" valign="top" style="padding-left: 10px;">
			<span style="display: block; padding: 5px; background: grey; text-align: center; color: #FFF; font-weight: bold; width: 150px;">L'ACHETEUR</span>
			<table cellspacing="0" cellpadding="5" border="0" width="100%" style="border: 1px solid #000;">
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
<br />

<table cellspacing="10" cellpadding="0" border="0" style="text-align: right;">
	<tr>
		<td style="text-align: left; width: 600px; border: none; font-size:20px;"><?php echo getLastSentence(); ?></td>
	</tr>
</table>

