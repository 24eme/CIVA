<table class="produits table_donnees">
	<thead>
		<tr>
			<th class="produit">Produit</th>
			<th class="denomination"><span>Dénomination</span></th>
			<th class="millesime"><span>Millésime</span></th>
			<th class="volume"><span>Volume proposé</span></th>
			<th class="prix"><span>Prix unitaire</span></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		foreach ($form['produits'] as $key => $embedForm) :
			$detail = $vrac->get($key);
	?>
		<tr>
			<td class="produit"><strong><?php echo $detail->getLibelle(); ?></strong><?php echo $detail->getComplementPartielLibelle(); ?></td>
			<td class="denomination">
				<span><?php echo $embedForm['denomination']->renderError() ?></span>
				<?php echo $embedForm['denomination']->render() ?>
			</td>
			<td class="millesime">
				<span><?php echo $embedForm['millesime']->renderError() ?></span>
				<?php echo $embedForm['millesime']->render() ?>
			</td>
			<td class="volume">
				<span><?php echo $embedForm['volume_propose']->renderError() ?></span>
				<?php echo $embedForm['volume_propose']->render(array('class' => 'num')) ?>&nbsp;Hl
			</td>
			<td class="prix">
				<span><?php echo $embedForm['prix_unitaire']->renderError() ?></span>
				<?php echo $embedForm['prix_unitaire']->render(array('class' => 'num')) ?>&nbsp;&euro;/Hl
				<a href="#" class="balayette" title="Effacer les champs">Effacer les champs</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<a href="<?php echo url_for('vrac_ajout_produit', array('sf_subject' => $vrac, 'etape' => $etape)) ?>" id="ajouter-produit">Ajouter un produit</a>

<script type="text/javascript">
$(document).ready(function () {
	$("#ajouter-produit").click(function() {
		var lien = $(this);
		$.post($("#principal").attr('action'), $("#principal").serialize(), function(data){
        	document.location.href = lien.attr('href');
        });
		return false;
	});
});
</script>
	

