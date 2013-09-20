<tr class="retiraisons">
	<td></td>
	<td width="40px" class="alt"></td>
	<td width="40px"></td>
	<td width="40px"  class="alt">
		<span><?php echo $form['date']->renderError() ?></span>
		<?php echo $form['date']->render() ?>
	</td>
	<td width="40px">
		<span><?php echo $form['volume']->renderError() ?></span>
		<?php echo $form['volume']->render() ?> Hl
	</td>
	<td width="40px" class="alt"></td>
	<td width="40px"><a class="btn_supprimer_ligne_template" data-container="tr.retiraisons" href="#">X</a></td>
</tr>