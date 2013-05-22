<form class="ajaxForm" id="form_stock" action="<?php echo url_for('ds_edition_operateur', $noeud); ?>" method="post">
	<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3)); ?>
    <?php 
        echo $form->renderHiddenFields();
        echo $form->renderGlobalErrors();
    ?>
    <div id="ajax_error"></div>
	<h2 class="titre_page"><?php echo getTitleLieuStockageStock($ds); ?></h2>

	<?php include_partial('ds/onglets', array('noeud' => $noeud)) ?>
		
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">

            <div id="cont_gestion_stock">
			
			<!-- #gestion_stock -->

			<div id="gestion_stock" class="clearfix gestion_stock_donnees <?php if(isset($has_lieux) && $has_lieux) echo 'avec_sous_onglets'; ?>">
				<?php include_partial('dsEditionFormContentCiva', array('ds' => $ds, 'form' => $form, 'noeud' => $noeud));?>
                <?php if($appellation->getConfig()->hasManyLieu()):  ?>
			    <div id="sous_total" class="ligne_total">
			        <h3>Sous total</h3>
			        
			        <ul>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_vt" /></li>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_sgn" /></li>
			        </ul>
			    </div>
               <?php endif; ?>
			</div>
			<!-- fin #gestion_stock -->


			<div id="total" class="ligne_total">
				<h3>Total <?php echo $appellation->libelle; ?></h3>
				<ul>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echo getDefaultTotal('total_normal',$appellation, $current_lieu); ?>" value="0.00" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echo getDefaultTotal('total_vt',$appellation, $current_lieu); ?>" value="0.00" class="somme" data-somme-col="#col_vt" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echo getDefaultTotal('total_sgn',$appellation, $current_lieu); ?>" value="0.00" class="somme" data-somme-col="#col_sgn" /></li>
				</ul>
			</div>
	
			<ul id="btn_appelation" class="btn_prev_suiv clearfix">
				<li>
					<?php if(!$isFirstAppellation): ?>
						<a class="ajax" href="<?php echo url_for('ds_edition_operateur', $ds->getPreviousAppellation($appellation)); ?>">
							<img src="/images/boutons/btn_appelation_prec.png" alt="Retourner à l'étape précédente"/>
						</a>
					<?php endif; ?>
				</li>
                                
				<li>
                       <input type="image" src="/images/boutons/btn_appelation_suiv.png" alt="Valider et passer à l'appellation suivante" />
				</li>
			</ul>
		</div>
		
	</div>
	<!-- fin #application_ds -->

	<ul id="btn_etape" class="btn_prev_suiv clearfix">
		<li class="prec ajax">
			<a href="<?php echo url_for("ds_lieux_stockage", $tiers) ?>">
				<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente"  />
			</a>
		</li>
			<li class="suiv ajax">
			<a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>">
				<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante"  />
			</a>
		</li>
	</ul>
</form>
