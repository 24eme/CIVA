<?php use_helper('Float') ?>
<?php include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => false)) ?>

<table class="validation table_donnees">
	<thead>
		<tr>
			<th>Produits</th>
			<th class="volume">Volume</th>
			<th class="prix">Prix</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$counter = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
			$alt = ($counter%2);
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td>
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?></strong>
			</td>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
		</tr>
		<?php 
			$counter++;  endforeach;
			endforeach;
		?>

	</tbody>
</table>

<table class="validation table_donnees">
	<thead>
		<tr>
			<th>Conditions</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo $form['conditions_paiement']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['conditions_paiement']->renderError() ?></span>
				<?php echo $form['conditions_paiement']->render(array('class' => 'input_long')) ?>
			</td>
		</tr>
		<tr class="alt">
			<td>
				<?php echo $form['conditions_particulieres']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['conditions_particulieres']->renderError() ?></span>
				<?php echo $form['conditions_particulieres']->render(array('class' => 'input_long')) ?>
			</td>
		</tr>

	</tbody>
</table>
<?php include_partial('vrac/popupConfirmeValidation'); ?>
