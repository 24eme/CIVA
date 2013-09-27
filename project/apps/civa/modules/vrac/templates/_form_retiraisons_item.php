<?php use_helper('Date') ?>
<tr class="retiraisons">
	<td></td>
	<td width="40px" class="alt"></td>
	<td width="40px"></td>
	<td width="40px"  class="alt">
		<span><?php echo $form['date']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['date']->render() : $form['date']->render(array('readonly' => 'readonly')); ?>
	</td>
	<td width="40px">
		<span><?php echo $form['volume']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['volume']->render() : $form['volume']->render(array('readonly' => 'readonly')); ?> Hl
	</td>
	<td width="40px" class="alt"></td>
	<td width="40px"><?php if (!$detail->cloture): ?><a class="btn_supprimer_ligne_template" data-container="tr.retiraisons" href="#">X</a><?php endif; ?></td>
</tr>