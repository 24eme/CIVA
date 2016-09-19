<table class="table_donnees">
	<thead>
		<tr>
			<th>Type</th>
			<th><span>CVI</span></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<span><?php echo $form['type']->renderError() ?></span>
				<?php echo $form['type']->render() ?>
			</td>
			<td>
				<span><?php echo $form['identifiant_ajout']->renderError() ?></span>
				<?php echo $form['identifiant_ajout']->render() ?>
			</td>
		</tr>
	</tbody>
</table>