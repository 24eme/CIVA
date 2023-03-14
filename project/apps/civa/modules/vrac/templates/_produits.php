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
            <?php elseif($vrac->needDateRetiraison()): ?>
            <th class="date_retiraison_limite" style="text-align: center; width: 100px;">Début de retiraison</th>
			<th class="date_retiraison_limite" style="text-align: center; width: 100px;">Limite de retiraison</th>
            <?php else: ?>
            <th colspan="2" style=" width: 100px;"></th>
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
            <td>
                <?php if (isset($popup_saisie_prix) && $popup_saisie_prix && isset($user) && $user && $user->_id == $vrac->acheteur_identifiant): ?>
                    <a href="" class="generationContratApplication" data-target="#popup_saisieprix_contratApplication">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                      <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                      <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/>
                    </svg>
                    Saisir les prix</a>
                <?php endif; ?>
            </td>
            <td colspan="<?php $colspan=1; if ($vrac->type_contrat != VracClient::TYPE_BOUTEILLE) $colspan += 1; if ($form) $colspan += 2; echo $colspan; ?>"></td>
        </tr>
    </tfoot>
</table>
