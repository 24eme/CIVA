<?php use_helper('Float') ?>
<?php use_helper('Date') ?>
<?php
    $quantiteType = ($vrac->isInModeSurface())? 'surface' : 'volume';
    $autreQuantiteType = ($quantiteType == 'volume')? 'surface' : 'volume';
?>
<style media="screen">.printonly {display: none;}</style>

<p class="intro_contrat_vrac">Vous trouverez ci-dessous le récapitulatif du contrat, les informations relatives aux soussignés et les quantités de produit concernées.</p>
<?php include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => false)) ?>

<table class="validation table_donnees" style="margin: 0 0 20px;">
	<thead>
		<tr>
			<th>Produits
                <a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => VracEtapes::ETAPE_PRODUITS)) ?>" style="float:right;text-decoration: none;font-size:13px;padding-top:1px;">Modifier</a>
            </th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille" style="text-align: center">Nb bouteilles</th>
			<th class="centilisation" style="text-align: center">Centilisation</th>
			<?php endif; ?>
			<th class="volume" style="text-align: center"><?php echo ucfirst($quantiteType); ?></th>
			<th class="prix" style="text-align: center">Prix</th>
            <th style=" width: 200px;"></th>
		</tr>
	</thead>
	<tbody>
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
			<?php include_partial('vrac/produitsProduit', array('detail' => $detail, 'produits_hash_in_error' => isset($produits_hash_in_error) ? $produits_hash_in_error : null)) ?>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			    <?php include_partial('vrac/produitsNombreBouteilles', array('detail' => $detail)) ?>
                <?php include_partial('vrac/produitsCentilisation', array('detail' => $detail)) ?>
			<?php endif; ?>
            <?php include_partial('vrac/produitsVolumePropose', array('vrac' => $vrac, 'detail' => $detail, 'quantiteType' => $quantiteType)) ?>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
            <td colspan="2"></td>
		</tr>
		<?php
			$counter++;  endforeach;
			endforeach;
		?>
	</tbody>
    <tfoot>
        <tr>
			<td style="text-align: right;"<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?> colspan="3"<?php endif; ?>><strong>Total</strong></td>
			<td class="volume">
				<?php echoFloat($volumeTotal) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?>
			</td>
            <td colspan="3"></td>
		</tr>
    </tfoot>
</table>

<?php if(!$vrac->isPapier()): ?>
<?php include_partial('vrac/ficheConditions', array('vrac' => $vrac, 'fiche' => false)); ?>
<?php include_partial('vrac/ficheAnnexes', array('vrac' => $vrac, 'fiche' => false, 'edit' => false)); ?>
<?php endif; ?>

<?php if($vrac->isPapier()): ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 230px;">Contrat papier</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo $form['numero_papier']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['numero_papier']->renderError() ?></span>
				<?php echo $form['numero_papier']->render(array("autofocus" => "autofocus")) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $form['date_signature']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['date_signature']->renderError() ?></span>
				<?php echo $form['date_signature']->render(array('class' => 'datepicker')) ?>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>

<?php include_partial('vrac/chronologie_contrat', array('vrac' => $vrac)); ?>
<?php include_partial('vrac/historique', array('vrac' => $vrac)); ?>

<?php include_partial('vrac/popupConfirmeValidation', array('vrac' => $vrac)); ?>
