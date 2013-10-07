<?php use_helper('Date') ?>
<?php use_helper('Float') ?>
<?php if ($form): ?>
<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_fiche', array('sf_subject' => $vrac)) ?>">
	<?php echo $form->renderHiddenFields() ?>
	<?php echo $form->renderGlobalErrors() ?>
<?php endif; ?>
<table class="table_donnees">
	<thead>
		<tr>
			<th>Produit</th>
			<th><span>Volume proposé</span></th>
			<th><span>Prix unitaire</span></th>
			<?php if ($vrac->isCloture() || $form): ?>
			<th><span>Echéance</span></th>
			<th><span>Enlevé</span></th>
			<?php endif; ?>
			<?php if ($form): ?>
			<th><span>Cloturé</span></th>
			<th></th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
	<?php if ($form): ?>
		<?php 
			foreach ($form['produits'] as $key => $formProduit):
				$detail = $vrac->get($key);
		?>
		<tr class="produits">
			<td>
				<strong><?php echo $detail->getLibelle(); ?></strong><?php echo $detail->getComplementLibelle(); ?>
			</td>
			<td>
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td>
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
			<td class="echeance"><input type="text" /></td>
			<td class="enleve"><input type="text" /><?php if ($detail->volume_enleve): ?><strong><?php echo echoFloat($detail->volume_enleve) ?> Hl<?php endif; ?></strong></td>
			<td class="cloture">
				<span><?php echo $formProduit['cloture']->renderError() ?></span>
				<?php echo $formProduit['cloture']->render() ?>
			</td>
			<td>
				<?php if (!$detail->cloture): ?>
				<a class="btn_ajouter_ligne_template" data-container-last-brother=".produits" data-template="#template_form_<?php echo str_replace('/', '_', $key); ?>_retiraisons_item" href="#">Enlever</a>
				<script id="template_form_<?php echo str_replace('/', '_', $key); ?>_retiraisons_item" class="template_form" type="text/x-jquery-tmpl">
    					<?php echo include_partial('form_retiraisons_item', array('detail' => $detail, 'form' => $form->getFormTemplateRetiraisons($detail->getRawValue(), $key))); ?>
				</script>
				<?php endif; ?>
			</td>
		</tr>
			<?php 
				foreach ($formProduit['enlevements'] as $keySub => $formEnlevement): 
					$enlevement = $vrac->get($key)->retiraisons->get($keySub);
			?>
				<?php include_partial('vrac/form_retiraisons_item', array('detail' => $detail, 'form' => $formEnlevement)) ?>
			<?php endforeach; ?>
		<?php 
			endforeach;
		?>
	<?php elseif($vrac->isCloture()): ?>
		<?php 
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
		?>
		<tr>
			<td>
				<strong><?php echo $detail->getLibelle(); ?></strong><?php echo $detail->getComplementLibelle(); ?>
			</td>
			<td>
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td>
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
			<td></td>
			<td><strong><?php echoFloat($detail->volume_enleve) ?>&nbsp;Hl</strong></td>
		</tr>
		<?php foreach ($detail->retiraisons as $retiraison): ?>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo format_date($retiraison->date, 'p', 'fr'); ?></td>
			<td><?php echoFloat($retiraison->volume) ?>&nbsp;Hl</td>
		</tr>
		<?php endforeach; ?>
		<?php 
			endforeach;
			endforeach;
		?>
	
	<?php else: ?>
		<?php 
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
		?>
		<tr>
			<td>
				<strong><?php echo $detail->getLibelle(); ?></strong><?php echo $detail->getComplementLibelle(); ?>
			</td>
			<td>
				<?php echoFloat($detail->volume_propose) ?>&nbsp;Hl
			</td>
			<td>
				<?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/Hl
			</td>
		</tr>
		<?php 
			endforeach;
			endforeach;
		?>
	<?php endif; ?>
	</tbody>
</table>
<?php if ($form): ?>
	<ul class="btn_prev_suiv clearfix" id="btn_etape">
	    <li class="suiv">		
	    	<button type="submit" name="valider" style="cursor: pointer;">
	    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider.png" />
	    	</button>
	    </li>
	</ul>
</form>
<a href="<?php echo url_for('vrac_cloture', $vrac) ?>"><img src="/images/boutons/btn_cloturer_contrat.png" alt="Cloturer le contrat" /></a>
<?php endif; ?>