<div class="clearfix">
	<table class="table_donnees" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th>Produit</th>
				<th><span>Dénomination</span></th>
				<th><span>Millésime</span></th>
				<th><span>Volume proposé</span></th>
				<th><span>Prix unitaire</span></th>
			</tr>
		</thead>
		<tbody>
		<?php 
			foreach ($form['produits'] as $key => $embedForm) :
				$detail = $vrac->get($key);
		?>
			<tr>
				<td><strong><?php echo $detail->getLibelle(); ?></strong><?php echo $detail->getComplementPartielLibelle(); ?></td>
				<td>
					<span><?php echo $embedForm['denomination']->renderError() ?></span>
					<?php echo $embedForm['denomination']->render() ?>
				</td>
				<td>
					<span><?php echo $embedForm['millesime']->renderError() ?></span>
					<?php echo $embedForm['millesime']->render() ?>
				</td>
				<td class="volume">
					<span><?php echo $embedForm['volume_propose']->renderError() ?></span>
					<?php echo $embedForm['volume_propose']->render() ?>&nbsp;Hl
				</td>
				<td class="prix_unitaire">
					<span><?php echo $embedForm['prix_unitaire']->renderError() ?></span>
					<?php echo $embedForm['prix_unitaire']->render() ?>&nbsp;&euro;/Hl
					<a href="#" class="balayette" title="Effacer les champs">Effacer les champs</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
</table>
<a href="<?php echo url_for('vrac_ajout_produit', array('sf_subject' => $vrac, 'etape' => $etape)) ?>" id="ajouter-produit">Ajouter un produit</a>
</div>
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
	

