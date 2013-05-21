<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 4)); ?>


<p id="adresse_stock">7523700100111 15 rue des 3 épis 75230 Paris</p>

<!-- #application_ds -->
<form action="<?php echo url_for('ds_autre', array('cvi' => $tiers->cvi)); ?>" method="post" class="" >
<?php
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();
?>
<div id="application_ds" class="clearfix">
		
	<div class="bloc_autres">
		<h2>Autres</h2>
		
			<div class="ligne_form">
                                <?php echo $form['rebeches']->renderError() ?>
				<label><?php echo $form['rebeches']->renderLabel() ?></label>
                                <?php echo $form['rebeches']->render() ?>
			</div>
			
			<div class="ligne_form">
				 <?php echo $form['dplc']->renderError() ?>
				<label><?php echo $form['dplc']->renderLabel() ?></label>
                                <?php echo $form['dplc']->render() ?>
			</div>
			
			<div class="ligne_form">
				 <?php echo $form['lies']->renderError() ?>
				<label><?php echo $form['lies']->renderLabel() ?></label>
                                <?php echo $form['lies']->render() ?>
			</div>
			
			<div class="ligne_form">
				 <?php echo $form['mouts']->renderError() ?>
				<label><?php echo $form['mouts']->renderLabel() ?></label>
                                <?php echo $form['mouts']->render() ?>
			</div>
			
		
	</div>
	
</div>
<!-- fin #application_ds -->


<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $ds->getFirstAppellationLieu())); ?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="#">
                    <input type="image" src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>
</form>



