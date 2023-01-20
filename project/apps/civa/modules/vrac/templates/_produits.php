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
            <th class="volume"><?php echo ucfirst($quantiteType); ?> estimé</th>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
            <th class="volume"><?php echo ucfirst($autreQuantiteType); ?> estimé</th>
            <?php endif; ?>
			<th class="prix">Prix</th>
			<?php if ($vrac->needRetiraison() && ($vrac->isCloture() || $form)): ?>
			<th class="echeance">Date</th>
			<th class="enleve">Volume réel</th>
            <?php elseif($vrac->needRetiraison()): ?>
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
				$alt = ($counter%2);
    			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
                $autreVolumeTotal += (!$vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr class="produits<?php if ($alt): ?> alt<?php endif; ?>">
			<td class="produit <?php echo isVersionnerCssClass($detail, 'millesime') ?> <?php echo isVersionnerCssClass($detail, 'denomination') ?> <?php echo isVersionnerCssClass($detail, 'label') ?> <?php echo isVersionnerCssClass($detail, 'actif') ?>">
				<?php echo $detail->getLibelleSansCepage(); ?> <strong>
					<?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
				<?php if(isset($produits_hash_in_error) && in_array($detail->getHash(), $produits_hash_in_error->getRawValue())): ?>
					<img src="/images/pictos/pi_alerte.png" alt="" />
				<?php endif; ?>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille <?php echo isVersionnerCssClass($detail, 'nb_bouteille') ?>">
				<?php echo $detail->nb_bouteille ?>
			</td>
			<td class="centilisation <?php echo isVersionnerCssClass($detail, 'centilisation') ?>"><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
			<td class="volume <?php echo isVersionnerCssClass($detail, 'volume_propose') ?> <?php echo isVersionnerCssClass($detail, 'surface_propose') ?>">
				<span id="prop<?php echo renderProduitIdentifiant($detail) ?>"><?php echoFloat(($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose) ?></span>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?>
			</td>
			<?php else: ?>
			<td class="volume <?php echo isVersionnerCssClass($detail, $quantiteType.'_propose') ?>">
				<span id="prop<?php echo renderProduitIdentifiant($detail) ?>"><?php echoFloat($detail->get($quantiteType.'_propose')) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?></span>
			</td>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
			<td class="volume <?php echo isVersionnerCssClass($detail, $autreQuantiteType.'_propose') ?>">
				<?php echoFloat($detail->get($autreQuantiteType.'_propose')) ?>&nbsp;<?php echo (!$vrac->isInModeSurface())? 'ares' : 'hl' ?>
			</td>
            <?php endif; ?>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
			<?php endif; ?>
			<td class="echeance"></td>
			<td class="enleve"><strong id="vol<?php echo renderProduitIdentifiant($detail) ?>" data-compare="prop<?php echo renderProduitIdentifiant($detail) ?>" data-cibling="<?php echo $formProduit['cloture']->renderId() ?>"><?php echo echoFloat($detail->volume_enleve) ?></strong> hl</td>
			<td class="cloture">
                               <input type="checkbox" name="<?php echo $formProduit['cloture']->renderName(); ?>" id="<?php echo $formProduit['cloture']->renderId(); ?>" value="<?php echo "1"; ?>" <?php echo ($detail->cloture)? "checked='checked'" : '' ?>  <?php echo ($detail->exist('volume_enleve') && $detail->volume_enleve !== null)? '' : "style='display:none'"; ?> />
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
	<?php elseif($vrac->isCloture() && $vrac->needRetiraison()): ?>
		<?php
			$counter = 0;
            $volumeTotal = 0;
            $autreVolumeTotal = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
				$alt = ($counter%2);
    			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
                $autreVolumeTotal += (!$vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td class="produit <?php echo isVersionnerCssClass($detail, 'millesime') ?> <?php echo isVersionnerCssClass($detail, 'denomination') ?> <?php echo isVersionnerCssClass($detail, 'label') ?> <?php echo isVersionnerCssClass($detail, 'actif') ?>">
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille <?php echo isVersionnerCssClass($detail, 'nb_bouteille') ?>">
				<span class="printonly">Nombre de bouteilles : </span><?php echo $detail->nb_bouteille ?>
			</td>
			<td class="centilisation <?php echo isVersionnerCssClass($detail, 'centilisation') ?>"><span class="printonly">Centilisation : </span><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<span class="printonly">Prix unitaire : </span><?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
			<td class="volume"><strong><span class="printonly"><?php echo ucfirst($quantiteType) ?> enlevé<?php if ($quantiteType == 'surface') echo 'e'; ?> : </span><?php echoFloat($detail->volume_enleve) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?></strong></td>
			<?php else: ?>
			<td class="volume <?php echo isVersionnerCssClass($detail, $quantiteType.'_propose') ?>">
				<span class="printonly"><?php echo ucfirst($quantiteType) ?> proposé<?php if ($quantiteType == 'surface') echo 'e'; ?> : </span><?php echoFloat($detail->get($quantiteType.'_propose')) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?>
			</td>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
			<td class="volume <?php echo isVersionnerCssClass($detail, $autreQuantiteType.'_propose') ?>">
				<span class="printonly"><?php echo ucfirst($autreQuantiteType) ?> proposé<?php if ($autreQuantiteType == 'surface') echo 'e'; ?> : </span><?php echoFloat($detail->get($autreQuantiteType.'_propose')) ?>&nbsp;<?php echo (!$vrac->isInModeSurface())? 'ares' : 'hl' ?>
			</td>
            <?php endif; ?>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<span class="printonly">Prix unitaire : </span><?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
			<td></td>
			<td class="volume"><strong><span class="printonly">Volume enlevé : </span><?php echoFloat($detail->volume_enleve) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?></strong></td>
			<?php endif; ?>
		</tr>
		<?php foreach ($detail->retiraisons as $retiraison): ?>
		<tr class="<?php if ($alt): ?> alt<?php endif; ?>">
		<td><strong class="printonly"><br/><br/>Enlèvement :</strong></td>
			<td></td>
			<td></td>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
			<td></td>
            <?php endif; ?>
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
				$alt = ($counter%2);
    			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
                $autreVolumeTotal += (!$vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td class="produit <?php echo isVersionnerCssClass($detail, 'millesime') ?> <?php echo isVersionnerCssClass($detail, 'denomination') ?> <?php echo isVersionnerCssClass($detail, 'label') ?> <?php echo isVersionnerCssClass($detail, 'actif') ?>">
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?> <?php echo $detail->millesime; ?> <i><?php echo $detail->denomination; ?></i></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille <?php echo isVersionnerCssClass($detail, 'nb_bouteille') ?>">
				<span class="printonly">Nombre de bouteilles : </span><?php echo $detail->nb_bouteille ?>
			</td>
			<td class="centilisation <?php echo isVersionnerCssClass($detail, 'centilisation') ?>"><span class="printonly">Centilisation : </span><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<span class="printonly">Prix unitaire : </span><?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
			<td class="volume <?php echo isVersionnerCssClass($detail, 'surface_propose') ?> <?php echo isVersionnerCssClass($detail, 'volume_propose') ?>">
				<span class="printonly">Volume proposé : </span><?php echoFloat(($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?>
			</td>
			<?php else: ?>
            <td class="volume <?php echo isVersionnerCssClass($detail, $quantiteType.'_propose') ?>">
                <span class="printonly"><?php echo ucfirst($quantiteType) ?> proposé<?php if ($quantiteType == 'surface') echo 'e'; ?> : </span><?php echoFloat($detail->get($quantiteType.'_propose')) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?>
            </td>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
            <td class="volume <?php echo isVersionnerCssClass($detail, $autreQuantiteType.'_propose') ?>">
                <span class="printonly"><?php echo ucfirst($autreQuantiteType) ?> proposé<?php if ($autreQuantiteType == 'surface') echo 'e'; ?> : </span><?php echoFloat($detail->get($autreQuantiteType.'_propose')) ?>&nbsp;<?php echo (!$vrac->isInModeSurface())? 'ares' : 'hl' ?>
            </td>
            <?php endif; ?>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<span class="printonly">Prix unitaire : </span><?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
            <?php if($vrac->needRetiraison()): ?>
            <td class="date_retiraison_limite <?php echo isVersionnerCssClass($detail, 'retiraison_date_debut') ?>" style="text-align: center;">
				<?php if($detail->retiraison_date_debut && !$vrac->isPluriannuelCadre()): ?>
                <?php echo format_date($detail->retiraison_date_debut, 'dd/MM/yyyy') ?>
                <?php else: ?>
                    <?php echo format_date('1970-'.$detail->retiraison_date_debut, 'dd/MM') ?>
                <?php endif; ?>
			</td>
            <td class="date_retiraison_limite <?php echo isVersionnerCssClass($detail, 'retiraison_date_limite') ?>" style="text-align: center;">
                <?php if($detail->retiraison_date_limite && !$vrac->isPluriannuelCadre()): ?>
                    <?php echo format_date($detail->retiraison_date_limite, 'dd/MM/yyyy') ?>
                <?php else: ?>
	                <?php echo format_date('1970-'.$detail->retiraison_date_limite, 'dd/MM') ?>
                <?php endif;  ?>
			</td>
            <?php else: ?>
            <td colspan="2"></td>
            <?php endif; ?>
			<?php endif; ?>
		</tr>
		<?php
			$counter++; endforeach;
			endforeach;
		?>
	<?php endif; ?>
    <tr<?php if (!$alt): ?> class="alt"<?php endif; ?>>
        <td style="text-align: right;"<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?> colspan="4"<?php endif; ?>><strong>Total</strong></td>
        <td class="volume">
            <?php echoFloat($volumeTotal) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?>
        </td>
        <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
        <td class="volume">
            <?php echoFloat($autreVolumeTotal) ?>&nbsp;<?php echo (!$vrac->isInModeSurface())? 'ares' : 'hl'; ?>
        </td>
        <?php endif; ?>
        <td colspan="<?php $colspan=2; if ($vrac->type_contrat != VracClient::TYPE_BOUTEILLE) $colspan += 1; if ($form) $colspan += 2; echo $colspan; ?>"></td>
    </tr>
	</tbody>
</table>
