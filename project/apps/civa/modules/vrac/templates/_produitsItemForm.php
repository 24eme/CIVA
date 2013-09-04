<tr>
	<td><strong><?php echo $detail->getLibelle(); ?></strong></td>
	<td>
		<span><?php echo $form['millesime']->renderError() ?></span>
		<?php echo $form['millesime']->render() ?>
	</td>
	<td>
		<span><?php echo $form['denomination']->renderError() ?></span>
		<?php echo $form['denomination']->render() ?>
	</td>
	<td>
		<?php if(isset($form['vtsgn'])): ?>
		<span><?php echo $form['vtsgn']->renderError() ?></span>
		<?php echo $form['vtsgn']->render() ?>
		<?php endif; ?>
	</td>
	<td>
		<span><?php echo $form['volume_propose']->renderError() ?></span>
		<?php echo $form['volume_propose']->render() ?>&nbsp;Hl
	</td>
	<td>
		<span><?php echo $form['prix_unitaire']->renderError() ?></span>
		<?php echo $form['prix_unitaire']->render() ?>&nbsp;&euro;/Hl
	</td>
</tr>