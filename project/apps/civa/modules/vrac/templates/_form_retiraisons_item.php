<?php use_helper('Date') ?>
<tr class="retiraisons">
	<td colspan="3"></td>
	<td class="alt echeance">
		<span><?php echo $form['date']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['date']->render() : $form['date']->render(array('readonly' => 'readonly')); ?>
	</td>
	<td class="enleve">
		<span><?php echo $form['volume']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['volume']->render() : $form['volume']->render(array('readonly' => 'readonly')); ?> Hl
	</td>
	<td class="alt"></td>
	<td><?php if (!$detail->cloture): ?><a class="btn_supprimer_ligne_template" data-container="tr.retiraisons" href="#">X</a><?php endif; ?></td>
</tr>