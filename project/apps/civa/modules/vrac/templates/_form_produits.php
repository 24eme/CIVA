<?php use_helper('vrac') ?>
<?php
    $quantiteType = ($vrac->isInModeSurface())? 'surface' : 'volume';
    $autreQuantiteType = ($quantiteType == 'volume')? 'surface' : 'volume';
?>
<p class="intro_contrat_vrac"><?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>Saisissez ici les produits concernés par le contrat et pour chacun le nombre de bouteille, la centilisation et le prix.<br />La saisie des zones "Dénomination", "Millésime" est facultative.<?php else: ?>Saisissez ici les produits concernés par le contrat et pour chacun, le label obligatoire, le prix à l'hectolitre et <?php echo ($vrac->isInModeSurface())? 'la surface engagée' : 'le volume estimé'; ?>.<?php endif; ?></p>
<table class="etape_produits produits table_donnees">
	<thead>
		<tr>
			<th class="produit">Produits</th>
			<th class="denomination<?php echo ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE)? 'Bouteille' : '' ?>"><span>Dénomination et précisions</span></th>
			<?php if ($form->hasBio()): ?>
			<th class="bio"><span>Labels</span></th>
			<?php endif; ?>
			<th class="millesime"><span>Millésime</span></th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille"><span>Nb bouteilles</span></th>
			<th class="centilisation"><span>Centilisation</span></th>
			<?php else: ?>
			<th class="volume"><span><?php echo ucfirst($quantiteType); ?></span></th>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
            <th class="volume"><span><?php echo ucfirst($autreQuantiteType); ?></span></th>
            <?php endif; ?>
			<?php endif; ?>
			<th class="prix"><span>Prix</span></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$counter = 0;
		foreach ($form['produits'] as $key => $embedForm) :
			$detail = $vrac->get($key);
			$alt = ($counter%2);
	?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td class="produit"><?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?></td>
			<td class="denomination<?php echo ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE)? 'Bouteille' : '' ?>">
				<span><?php echo $embedForm['denomination']->renderError() ?></span>
				<?php if ($counter == 0): ?>
				<?php echo $embedForm['denomination']->render(array("autofocus" => "autofocus")) ?>
				<?php else: ?>
				<?php echo $embedForm['denomination']->render() ?>
				<?php endif; ?>
			</td>
			<?php if(isset($embedForm['label'])): ?>
			<td class="bio">
				<span><?php echo $embedForm['label']->renderError() ?></span>
				<?php if ($counter == 0): ?>
				<?php echo $embedForm['label']->render(array("autofocus" => "autofocus")) ?>
				<?php else: ?>
				<?php echo $embedForm['label']->render() ?>
				<?php endif; ?>
			</td>
			<?php endif; ?>
			<td class="millesime">
                <?php if($vrac->isPluriannuelCadre()): $campagnes = VracSoussignesForm::getCampagnesChoices(); ?>
                <?php echo $campagnes[$vrac->campagne] ?>
                <?php else: ?>
				<span><?php echo $embedForm['millesime']->renderError() ?></span>
				<?php echo $embedForm['millesime']->render(array("maxlength" => 4)) ?>
                <?php endif; ?>
            </td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille">
				<span><?php echo $embedForm['nb_bouteille']->renderError() ?></span>
				<?php echo $embedForm['nb_bouteille']->render() ?>
			</td>
			<td class="centilisation">
				<span><?php echo $embedForm['centilisation']->renderError() ?></span>
				<?php echo $embedForm['centilisation']->render() ?>
			</td>
			<?php else: ?>
			<td class="volume">
				<span><?php echo $embedForm[$quantiteType.'_propose']->renderError() ?></span>
				<?php echo $embedForm[$quantiteType.'_propose']->render(array('class' => 'num')) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl'; ?>
			</td>
            <?php if($vrac->isType(VracClient::TYPE_MOUT)): ?>
			<td class="volume">
				<span><?php echo $embedForm[$autreQuantiteType.'_propose']->renderError() ?></span>
				<?php echo $embedForm[$autreQuantiteType.'_propose']->render(array('class' => 'num')) ?>&nbsp;<?php echo (!$vrac->isInModeSurface())? 'ares' : 'hl'; ?>
			</td>
            <?php endif; ?>
			<?php endif; ?>
			<td class="prix">
				<span><?php echo $embedForm['prix_unitaire']->renderError() ?></span>
				<?php echo $embedForm['prix_unitaire']->render(array('class' => 'num')) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?>
				<a href="#" class="balayette" title="Effacer les champs">Effacer les champs</a>
			</td>
		</tr>
	<?php $counter++; endforeach; ?>
	</tbody>
</table>
<a href="<?php echo url_for('vrac_ajout_produit', array('sf_subject' => $vrac, 'etape' => $etape)) ?>" id="ajouter-produit"><img src="/images/boutons/btn_ajouter_produit.png" alt="Ajouter un produit" /></a>

<script type="text/javascript">

	$(document).ready(function()
	{
		$("#ajouter-produit").click(function() {
			var lien = $(this);
			$.post($("#principal").attr('action'), $("#principal").serialize(), function(data){
	        	document.location.href = lien.attr('href');
	        });
			return false;
		});

		$.initChampsTableauProduits({ajoutProduit: <?php echo $referer ?>});
	});
</script>
