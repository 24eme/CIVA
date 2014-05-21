<?php use_helper('Float') ?>
<p class="intro_contrat_vrac">Vous trouverez ci-dessous le récapitulatif du contrat, les informations relatives aux soussignés et les quantités de produit concernées. <br />Saisissez ici les éventuelles conditions du contrat.</p>
<?php include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => false)) ?>

<table class="validation table_donnees">
	<thead>
		<tr>
			<th>Produits</th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille" style="text-align: center">Nb bouteilles</th>
			<th class="centilisation" style="text-align: center">Centilisation</th>
			<?php endif; ?>
			<th class="volume" style="text-align: center">Volume</th>
			<th class="prix" style="text-align: center">Prix</th>
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
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille"><?php echo $detail->nb_bouteille ?></td>
			<td class="centilisation"><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<?php endif; ?>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;hl
			</td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>hl<?php endif; ?><?php endif; ?>
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
			<th style="width: 212px;">Conditions</th>
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
