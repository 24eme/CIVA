<!-- #application_ds -->
<form action="<?php echo url_for('ds_autre', array('cvi' => $tiers->cvi)); ?>" id="form_autre_<?php echo $ds->_id; ?>" method="post" >
<?php
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 4));
?>

<h2 class="titre_page">7523700100111 15 rue des 3 épis 75230 Paris</h2>

<!-- #application_ds -->
<div id="ajax_error"></div>


	<div id="application_ds" class="clearfix">
		
		<div class="bloc_autres">
				
		<!-- #gestion_stock -->
		<div id="gestion_stock" class="clearfix gestion_stock_donnees">
			<ul id="liste_cepages">
				<li><?php echo $form['rebeches']->renderLabel() ?></li>
				<li><?php echo $form['dplc']->renderLabel() ?></li>
				<li><?php echo $form['lies']->renderLabel() ?></li>
				<li><?php echo $form['mouts']->renderLabel() ?></li>
			</ul>
			
			<div id="donnees_stock_cepage">
				<div id="col_hl" class="colonne">
					<h2>hl</h2>
	
					<div class="col_cont">
						<ul>
							<li><?php echo $form['rebeches']->render(array('class' => 'num')) ?></li>
							<li><?php echo $form['dplc']->render(array('class' => 'num')) ?></li>
							<li><?php echo $form['lies']->render(array('class' => 'num')) ?></li>
							<li><?php echo $form['mouts']->render(array('class' => 'num')) ?></li>
						</ul>
					</div>
				</div>
			</div>
			
		</div>
		<!-- fin #gestion_stock -->
		
		
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
			<input type="image" src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</li>
	</ul>
	
</div>
<!-- fin #application_ds -->


</form>
