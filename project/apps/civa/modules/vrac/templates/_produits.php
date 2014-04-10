<?php use_helper('Date') ?>
<?php use_helper('Float') ?>
<?php use_helper('Text') ?>
<?php use_helper('vrac') ?>

<table class="table_donnees produits validation">
	<thead>
		<tr>
			<th class="produit">Produit</th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille">Nb bouteille</th>
			<th class="centilisation">Centilisation</th>
			<th class="prix">Prix</th>
			<th class="volume">Volume</th>
			<?php else: ?>
			<th class="volume">Volume estimé</th>
			<th class="prix">Prix</th>
			<?php if ($vrac->isCloture() || $form): ?>
			<th class="echeance">Date</th>
			<th class="enleve">Volume réel</th>
			<?php endif; ?>
			<?php if ($form): ?>
			<th class="cloture">Cloture</th>
			<th class="actions"></th>
			<?php endif; ?>
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
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong>
				<?php if(isset($produits_hash_in_error) && in_array($detail->getHash(), $produits_hash_in_error->getRawValue())): ?>
					<img src="/images/pictos/pi_alerte.png" alt="" />
				<?php endif; ?>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille">
				<?php echoFloat($detail->nb_bouteille) ?>
			</td>
			<td class="centilisation"><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>Hl<?php endif; ?><?php endif; ?>
			</td>
			<td class="volume">
				<span id="prop<?php echo renderProduitIdentifiant($detail) ?>"><?php echoFloat($detail->volume_propose) ?></span>&nbsp;Hl
			</td>
			<?php else: ?>
			<td class="volume">
				<span id="prop<?php echo renderProduitIdentifiant($detail) ?>"><?php echoFloat($detail->volume_propose) ?></span>&nbsp;Hl
			</td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>Hl<?php endif; ?><?php endif; ?>
			</td>
			<?php endif; ?>
			<td class="echeance"></td>
			<td class="enleve"><strong id="vol<?php echo renderProduitIdentifiant($detail) ?>" data-compare="prop<?php echo renderProduitIdentifiant($detail) ?>" data-cibling="<?php echo $formProduit['cloture']->renderId() ?>"><?php echo echoFloat($detail->volume_enleve) ?></strong> Hl</td>
			<td class="cloture">
                               <input type="checkbox" name="<?php echo $formProduit['cloture']->renderName(); ?>" id="<?php echo $formProduit['cloture']->renderId(); ?>" value="<?php echo "1"; ?>" <?php echo ($detail->cloture)? "checked='checked'" : '' ?>  <?php echo ($detail->exist('volume_enleve') && $detail->volume_enleve)? '' : "style='display:none'"; ?> />
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
					if ($vrac->get($key)->retiraisons->exist($keySub)) {
					$enlevement = $vrac->get($key)->retiraisons->get($keySub);
			?>
				<?php include_partial('vrac/form_retiraisons_item', array('detail' => $detail, 'form' => $formEnlevement, 'alt' => $alt)) ?>
			<?php 	}
				endforeach; ?>
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
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille">
				<?php echoFloat($detail->nb_bouteille) ?>
			</td>
			<td class="centilisation"><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>Hl<?php endif; ?><?php endif; ?>
			</td>
			<td class="volume"><strong><?php echoFloat($detail->volume_enleve) ?>&nbsp;Hl</strong></td>
			<?php else: ?>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>Hl<?php endif; ?><?php endif; ?>
			</td>
			<td></td>
			<td class="volume"><strong><?php echoFloat($detail->volume_enleve) ?>&nbsp;Hl</strong></td>
			<?php endif; ?>
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
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?> <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille">
				<?php echoFloat($detail->nb_bouteille) ?>
			</td>
			<td class="centilisation"><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>Hl<?php endif; ?><?php endif; ?>
			</td>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<?php else: ?>
			<td class="volume">
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>Hl<?php endif; ?><?php endif; ?>
			</td>
			<?php endif; ?>
		</tr>
		<?php 
			$counter++; endforeach;
			endforeach;
		?>
	<?php endif; ?>
	</tbody>
</table>
