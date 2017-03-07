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
				<span><?php echo $form['identifiant']->renderError() ?></span>
				<?php echo $form['identifiant']->render() ?>
			</td>
		</tr>
	</tbody>
</table>