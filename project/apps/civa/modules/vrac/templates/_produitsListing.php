<table class="table_donnees" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th>Produit</th>
			<th><span>Millésime</span></th>
			<th><span>Dénomination</span></th>
			<th><span>VT/SGN</span></th>
			<th><span>Volume proposé</span></th>
			<th><span>Prix unitaire</span></th>
		</tr>
	</thead>
	<tbody>
	
	<?php 
		foreach ($form['produits'] as $key => $embedForm) {
			include_partial('vrac/produitsItemForm', array('detail' => $vrac->get($key),'form' => $embedForm));
		}
	?>
	</tbody>
</table>