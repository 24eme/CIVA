<?php include_partial('vrac/soussignes', array('vrac' => $vrac)) ?>

<div class="clearfix">
		<table class="table_donnees" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>Produit</th>
					<th width="40px"><span>Volume proposé</span></th>
					<th width="40px"><span>Prix unitaire</span></th>
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
				<td width="40px">
					<?php echo $detail->volume_propose ?>&nbsp;Hl
				</td>
				<td width="40px">
					<?php echo $detail->prix_unitaire ?>&nbsp;&euro;/Hl
				</td>
			</tr>
			<?php 
				endforeach;
				endforeach;
			?>
			</tbody>
		</table>
		<div>
			<?php echo $form['conditions_paiement']->renderLabel() ?>
			<?php echo $form['conditions_paiement']->render() ?>
			<span><?php echo $form['conditions_paiement']->renderError() ?></span>
		</div>
		<div>
			<?php echo $form['conditions_particulieres']->renderLabel() ?>
			<?php echo $form['conditions_particulieres']->render() ?>
			<span><?php echo $form['conditions_particulieres']->renderError() ?></span>
		</div>
</div>

