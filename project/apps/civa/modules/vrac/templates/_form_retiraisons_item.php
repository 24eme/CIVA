<?php use_helper('Date') ?>
<?php use_helper('Text') ?>
<?php use_helper('vrac') ?>
<tr class="retiraisons<?php if (isset($alt) && $alt): ?> alt<?php endif; ?>">
	<td colspan="3"></td>
	<td class="echeance">
		<span><?php echo $form['date']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['date']->render(array('class' => 'input_date datepicker')) : $form['date']->render(array('class' => 'input_date', 'readonly' => 'readonly')); ?>
	</td>
	<td class="enleve">
		<span><?php echo $form['volume']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['volume']->render(array('class' => 'input_volume summable num ret'.renderProduitIdentifiant($detail), 'data-brother' => 'ret'.renderProduitIdentifiant($detail), 'data-mother' => 'vol'.renderProduitIdentifiant($detail))) : $form['volume']->render(array('class' => 'input_volume num ret'.renderProduitIdentifiant($detail), 'readonly' => 'readonly')); ?> Hl
	</td>
	<td></td>
	<td><?php if (!$detail->cloture): ?><a class="btn_supprimer_ligne_template" data-brother="ret<?php echo renderProduitIdentifiant($detail) ?>" data-mother="vol<?php echo renderProduitIdentifiant($detail) ?>" data-container="tr.retiraisons" href="#">X</a><?php endif; ?></td>
</tr>