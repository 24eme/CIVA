<?php use_helper('Date') ?>
<?php use_helper('Float') ?>

<table class="table_donnees produits">
	<thead>
		<tr>
			<th class="produit">Produit</th>
			<th class="volume"><span>Volume</span></th>
			<th class="prix"><span>Prix</span></th>
			<?php if ($vrac->isCloture() || $form): ?>
			<th class="echeance"><span>Echéance</span></th>
			<th class="enleve"><span>Enlevé</span></th>
			<?php endif; ?>
			<?php if ($form): ?>
			<th class="cloture"><span>Cloturé</span></th>
			<th class="actions"></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php if ($form): ?>
		<?php 
			$counter = 0;
			foreach ($form['produits'] as $key => $formProduit):
				$detail = $vrac->get($key);
				$alt = ($counter%2);
		?>
		<tr class="produits<?php if ($alt): ?> alt<?php endif; ?>">
			<td class="produit">
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?></strong>
			</td>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
			<td class="echeance"></td>
			<td class="enleve"><?php if ($detail->volume_enleve): ?><strong><?php echo echoFloat($detail->volume_enleve) ?></strong> Hl<?php endif; ?></td>
			<td class="cloture">
				<span><?php echo $formProduit['cloture']->renderError() ?></span>
				<?php echo $formProduit['cloture']->render() ?>
			</td>
			<td>
				<?php if (!$detail->cloture): ?>
				<a class="btn_ajouter_ligne_template" data-container-last-brother=".produits" data-template="#template_form_<?php echo str_replace('/', '_', $key); ?>_retiraisons_item" href="#">Enlever</a>
				<script id="template_form_<?php echo str_replace('/', '_', $key); ?>_retiraisons_item" class="template_form" type="text/x-jquery-tmpl">
    					<?php echo include_partial('form_retiraisons_item', array('detail' => $detail, 'form' => $form->getFormTemplateRetiraisons($detail->getRawValue(), $key))); ?>
				</script>
				<?php endif; ?>
			</td>
		</tr>
			<?php 
				foreach ($formProduit['enlevements'] as $keySub => $formEnlevement): 
					$enlevement = $vrac->get($key)->retiraisons->get($keySub);
			?>
				<?php include_partial('vrac/form_retiraisons_item', array('detail' => $detail, 'form' => $formEnlevement, 'alt' => $alt)) ?>
			<?php endforeach; ?>
		<?php 
			$counter++; endforeach;
		?>
	<?php elseif($vrac->isCloture()): ?>
		<?php 
			$counter = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
				$alt = ($counter%2);
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td class="produit">
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?></strong>
			</td>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
			<td></td>
			<td class="volume"><strong><?php echoFloat($detail->volume_enleve) ?>&nbsp;Hl</strong></td>
		</tr>
		<?php foreach ($detail->retiraisons as $retiraison): ?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td></td>
			<td></td>
			<td></td>
			<td class="echeance"><?php echo format_date($retiraison->date, 'p', 'fr'); ?></td>
			<td class="volume"><?php echoFloat($retiraison->volume) ?>&nbsp;Hl</td>
		</tr>
		<?php endforeach; ?>
		<?php 
			$counter++; endforeach;
			endforeach;
		?>
	
	<?php else: ?>
		<?php 
			$counter = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
				$alt = ($counter%2);
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td class="produit">
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?></strong>
			</td>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
		</tr>
		<?php 
			$counter++; endforeach;
			endforeach;
		?>
	<?php endif; ?>
	</tbody>
</table>