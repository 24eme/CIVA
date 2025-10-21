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
			<th class="bio"><span>Certifications</span></th>
			<?php endif; ?>
			<th class="millesime"><span>Millésime</span></th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille"><span>Nb bouteilles</span></th>
			<th class="centilisation"><span>Centilisation</span></th>
			<?php else: ?>
			<th class="volume"><span><?php echo ucfirst($quantiteType); ?></span></th>
            <?php if(!$vrac->isPluriannuelCadre() && $vrac->type_contrat == VracClient::TYPE_VRAC): ?>
			<th class="volume_bloque"><span>Dont&nbsp;volume<br />&nbsp;en réserve</span></th>
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
	?>
		<tr>
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
                <?php if($vrac->isPluriannuelCadre()): ?>
                    <?php if (substr(key(VracSoussignesForm::getDureeContratCurrentMillesime()), 0, 4) === substr($vrac->campagne, 0, 4)): ?>
                        <?php $campagnes = VracSoussignesForm::getDureeContratNextMillesime(); ?>
                        <?php echo $vrac->campagne ?>
                    <?php else: ?>
                        <?php $campagnes = VracSoussignesForm::getDureeContratCurrentMillesime(); ?>
                        <?php echo $vrac->campagne ?>
                    <?php endif ?>
                <?php else: ?>
				<span><?php echo $embedForm['millesime']->renderError() ?></span>
				<?php echo $embedForm['millesime']->render(array("maxlength" => 4, 'checkmillesime' => (!$vrac->isPluriannuelCadre() && !in_array($vrac->type_contrat, [VracClient::TYPE_VRAC,VracClient::TYPE_BOUTEILLE]))? 1 : 0)) ?>
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
            <?php if(!$vrac->isPluriannuelCadre() && $vrac->type_contrat == VracClient::TYPE_VRAC): ?>
            <td class="volume_bloque" style="width: 60px">
                <?php if(isset($embedForm['dont_volume_bloque'])): ?>
				<span><?php echo $embedForm['dont_volume_bloque']->renderError() ?></span>
				<?php echo $embedForm['dont_volume_bloque']->render(array('class' => 'num', 'style' => 'width: 40px;')) ?>&nbsp;hl
                <?php endif; ?>
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
