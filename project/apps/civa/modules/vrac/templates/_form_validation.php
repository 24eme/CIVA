<?php include_partial('vrac/soussignes', array('vrac' => $vrac)) ?>

<table class="table_donnees">
	<thead>
		<tr>
			<th>Produit</th>
			<th>Volume proposé</th>
			<th>Prix unitaire</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
		?>
		<tr>
			<td>
				<strong><?php echo $detail->getLibelle(); ?></strong><?php if ($detail->millesime) { echo ' '.$detail->millesime; } if ($detail->denomination) { echo ' '.$detail->denomination; } if ($detail->vtsgn) { echo ' '.$detail->vtsgn; } ?>
			</td>
			<td>
				<?php echo $detail->volume_propose ?>&nbsp;Hl
			</td>
			<td>
				<?php echo $detail->prix_unitaire ?>&nbsp;&euro;/Hl
			</td>
		</tr>
		<?php 
			endforeach;
			endforeach;
		?>

		<tr>
			<td>Lorem ipsum dolor sit amet.</td>
			<td>Ipsa nobis voluptas quos cupiditate.</td>
			<td>Quisquam, quas dignissimos laudantium accusamus.</td>
		</tr>
		<tr>
			<td>Lorem ipsum dolor sit amet.</td>
			<td>Ipsa nobis voluptas quos cupiditate.</td>
			<td>Quisquam, quas dignissimos laudantium accusamus.</td>
		</tr>

	</tbody>
</table>

<div class="conditions">
	<div class="ligne_form">
		<?php echo $form['conditions_paiement']->renderLabel() ?>
		<?php echo $form['conditions_paiement']->render() ?>
		<span><?php echo $form['conditions_paiement']->renderError() ?></span>
	</div>
	<div class="ligne_form">
		<?php echo $form['conditions_particulieres']->renderLabel() ?>
		<?php echo $form['conditions_particulieres']->render() ?>
		<span><?php echo $form['conditions_particulieres']->renderError() ?></span>
	</div>
</div>

