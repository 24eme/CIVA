<?php use_helper('Date') ?>
<?php use_helper('Float') ?>
<?php use_helper('Text') ?>
<?php use_helper('vrac') ?>
<?php
    $quantiteType = ($vrac->isInModeSurface())? 'surface' : 'volume';
    $autreQuantiteType = ($quantiteType == 'volume')? 'surface' : 'volume';
?>
<table class="table_donnees produits validation">
	<thead>
		<tr>
			<th class="produit">Produit</th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille">Nb bouteilles</th>
			<th class="centilisation">Centilisation</th>
			<th class="prix">Prix</th>
			<th class="volume">Volume</th>
			<?php else: ?>
            <th class="volume"><?php echo ucfirst($quantiteType); ?> <?php if(!$vrac->needRetiraison()): ?>engagé<?php else: ?>estimé<?php endif; ?></th>
			<th class="prix">Prix</th>
			<?php if ($vrac->needRetiraison() && ($vrac->isCloture() || $form)): ?>
			<th class="echeance">Date</th>
			<th class="enleve">Volume réel</th>
			<?php endif; ?>
			<?php if ($form): ?>
			<th class="cloture">Clotûre</th>
			<th class="actions"></th>
            <?php else: ?>
            <th colspan="2" style=" width: 100px;"></th>
			<?php endif; ?>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php if ($form): ?>
		<?php
			$counter = 0;
            $volumeTotal = 0;
            $autreVolumeTotal = 0;
			foreach ($form['produits'] as $key => $formProduit):
				$detail = $vrac->get($key);
    			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
                $autreVolumeTotal += (!$vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr class="produits">
			<?php include_partial('vrac/produitsLigne', array('vrac' => $vrac, 'detail' => $detail, 'form' => $form, 'quantiteType' => $quantiteType, 'produits_hash_in_error' => isset($produits_hash_in_error) ? $produits_hash_in_error : null, 'formProduit' => isset($formProduit) ? $formProduit : null, 'key' => $key)) ?>
		</tr>
			<?php
				foreach ($formProduit['enlevements'] as $keySub => $formEnlevement):
					if ($vrac->get($key)->retiraisons->exist($keySub)) {
					$enlevement = $vrac->get($key)->retiraisons->get($keySub);
			?>
				<?php include_partial('vrac/form_retiraisons_item', array('detail' => $detail, 'form' => $formEnlevement)) ?>
			<?php 	}
				endforeach; ?>
		<?php
			$counter++; endforeach;
		?>
	<?php elseif($vrac->isCloture() && $vrac->needRetiraison()): ?>
		<?php
			$counter = 0;
            $volumeTotal = 0;
            $autreVolumeTotal = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
    			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
                $autreVolumeTotal += (!$vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr>
			<?php include_partial('vrac/produitsLigne', array('vrac' => $vrac, 'detail' => $detail, 'form' => $form, 'quantiteType' => $quantiteType, 'produits_hash_in_error' => isset($produits_hash_in_error) ? $produits_hash_in_error : nul)) ?>
		</tr>
		<?php foreach ($detail->retiraisons as $retiraison): ?>
		<tr>
		<td><strong class="printonly"><br/><br/>Enlèvement :</strong></td>
			<td></td>
			<td></td>
			<td class="echeance"><?php echo format_date($retiraison->date, 'p', 'fr'); ?></td>
			<td class="volume"><?php echoFloat($retiraison->volume) ?>&nbsp;hl</td>
		</tr>
		<?php endforeach; ?>
		<?php
			$counter++; endforeach;
			endforeach;
		?>

	<?php else: ?>
		<?php
			$counter = 0;
            $volumeTotal = 0;
            $autreVolumeTotal = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
    			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
                $autreVolumeTotal += (!$vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr>
			<?php include_partial('vrac/produitsLigne', array('vrac' => $vrac, 'detail' => $detail, 'form' => $form, 'quantiteType' => $quantiteType, 'produits_hash_in_error' => isset($produits_hash_in_error) ? $produits_hash_in_error : nul)) ?>
		</tr>
		<?php
			$counter++; endforeach;
			endforeach;
		?>
	<?php endif; ?>
	</tbody>
    <tfoot>
        <tr>
            <td style="text-align: right;"<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?> colspan="4"<?php endif; ?>><strong>Total</strong></td>
            <td class="volume">
                <?php echoFloat($volumeTotal) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?>
            </td>
            <td colspan="<?php $colspan=2; if ($vrac->type_contrat != VracClient::TYPE_BOUTEILLE) $colspan += 1; if ($form) $colspan += 2; echo $colspan; ?>"></td>
        </tr>
    </tfoot>
</table>
