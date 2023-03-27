<?php use_helper('Date') ?>
<?php use_helper('Text') ?>
<?php use_helper('vrac') ?>
<tr class="retiraisons">
	<td colspan="3"></td>
	<td class="echeance">
		<span><?php echo $form['date']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['date']->render(array('class' => 'input_date datepicker', 'required' => 'required')) : $form['date']->render(array('class' => 'input_date', 'readonly' => 'readonly')); ?>
	</td>
	<td class="enleve">
		<span><?php echo $form['volume']->renderError() ?></span>
		<?php echo (!$detail->cloture)? $form['volume']->render(array('class' => 'input_volume summable num ret'.renderProduitIdentifiant($detail), 'data-brother' => 'ret'.renderProduitIdentifiant($detail), 'data-mother' => 'vol'.renderProduitIdentifiant($detail))) : $form['volume']->render(array('class' => 'input_volume summable num ret'.renderProduitIdentifiant($detail), 'data-brother' => 'ret'.renderProduitIdentifiant($detail), 'data-mother' => 'vol'.renderProduitIdentifiant($detail), 'readonly' => 'readonly')); ?> hl
	</td>
	<td></td>
        <td><a data-brother="ret<?php echo renderProduitIdentifiant($detail) ?>"  data-mother="vol<?php echo renderProduitIdentifiant($detail) ?>" <?php if (!$detail->cloture): ?> class="btn_supprimer_ligne_template" data-container="tr.retiraisons" <?php endif; ?> href="#" ><?php if (!$detail->cloture): ?>X<?php endif; ?></a></td>
</tr>
