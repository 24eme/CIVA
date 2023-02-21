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
            <?php if($vrac->needRetiraison()): ?>
			<th class="date_retiraison_limite" style="text-align: center; width: 100px;">Début de retiraison</th>
			<th class="date_retiraison_limite" style="text-align: center; width: 100px;">Limite de retiraison</th>
            <?php else :?>
            <th style=" width: 200px;"></th>
            <?php endif; ?>
		</tr>
	</thead>
	<tbody>
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
			<?php include_partial('vrac/produitsProduit', array('detail' => $detail, 'produits_hash_in_error' => isset($produits_hash_in_error) ? $produits_hash_in_error : null)) ?>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			    <?php include_partial('vrac/produitsNombreBouteilles', array('detail' => $detail)) ?>
                <?php include_partial('vrac/produitsCentilisation', array('detail' => $detail)) ?>
			<?php endif; ?>
            <?php include_partial('vrac/produitsVolumePropose', array('vrac' => $vrac, 'detail' => $detail, 'quantiteType' => $quantiteType)) ?>
			<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
			</td>
            <?php if($vrac->needRetiraison()): ?>
            <td class="date_retiraison_limite <?php echo isVersionnerCssClass($detail, 'retiraison_date_debut') ?>" style="text-align: center;">
				<?php if($detail->retiraison_date_debut && !$vrac->isPluriannuelCadre()): ?>
                <?php echo format_date($detail->retiraison_date_debut, 'dd/MM/yyyy') ?>
				<?php elseif($detail->retiraison_date_debut): ?>
					<?php echo format_date('1970-'.$detail->retiraison_date_debut, 'dd/MM') ?>
                <?php endif; ?>
			</td>
            <td class="date_retiraison_limite <?php echo isVersionnerCssClass($detail, 'retiraison_date_limite') ?>" style="text-align: center;">
                <?php if($detail->retiraison_date_limite && !$vrac->isPluriannuelCadre()): ?>
                    <?php echo format_date($detail->retiraison_date_limite, 'dd/MM/yyyy') ?>
                <?php elseif($detail->retiraison_date_limite): ?>
	                <?php echo format_date('1970-'.$detail->retiraison_date_limite, 'dd/MM') ?>
                <?php endif;  ?>
			</td>
            <?php else : ?>
            <td colspan="2"></td>
            <?php endif; ?>
		</tr>
		<?php
			$counter++;  endforeach;
			endforeach;
		?>
		<tr<?php if (!$alt): ?> class="alt"<?php endif; ?>>
			<td style="text-align: right;"<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?> colspan="3"<?php endif; ?>><strong>Total</strong></td>
			<td class="volume">
				<?php echoFloat($volumeTotal) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?>
			</td>
            <td colspan="3"></td>
		</tr>
	</tbody>
</table>

<?php if(!$vrac->isPapier()): ?>
<?php include_partial('vrac/ficheConditions', array('vrac' => $vrac, 'fiche' => false)); ?>
<?php endif; ?>

<?php if($vrac->isPapier()): ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Contrat papier</th>
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

<?php include_partial('vrac/popupConfirmeValidation', array('vrac' => $vrac)); ?>
