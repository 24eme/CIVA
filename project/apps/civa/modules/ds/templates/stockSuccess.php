<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3)); ?>
<form class="ajaxForm" id="form_<?php echo $ds->_id."_".$appellation_lieu; ?>" action="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $appellation_lieu)); ?>" method="post">
	
	<p id="adresse_stock"><?php echo $ds->declarant->cvi.' - '.$ds->getEtablissement()->getNom().' - '.$ds->getEtablissement()->getAdresse(); ?></p>
	
	<ul id="onglets_majeurs" class="clearfix onglets_stock">
            <?php foreach ($ds->getAppellationsArray() as $app_key => $app): 
                ?>
            <li class="<?php echo ($app_key==preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu))? 'ui-tabs-selected' : '' ; ?>">
                    <?php $app_libelle = $app->appellation; ?>
                    <a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $ds->getAppellationLieuKey($app_key))); ?>"><span>
                        <?php echo (preg_match('/^AOC/', $app_libelle))? 'AOC ' : ''; ?>
                        </span> 
                        <br><?php echo (preg_match('/^AOC/', $app_libelle))? substr($app_libelle, 4) : $app_libelle; ?></a>
		</li>
                <?php 
                endforeach;
                ?>
              <?php $appellation_k = preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu);
                if($ds->hasManyLieux($appellation_k))  : ?>
              <ul class="sous_onglets">
                  <?php foreach ($ds->getLieuxFromAppellation($appellation_k) as $lieu_key => $lieu) :  
                      $lieu_k = preg_replace('/^lieu/','', $lieu_key);
                  ?>
                  <li class="<?php echo (preg_replace('/^[A-Z]*-/', '', $appellation_lieu) == $lieu_k)? 'ui-tabs-selected' : ''; ?>">
                      <a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $appellation_k.'-'.$lieu_k)); ?>"><?php echo $lieu->getLieuLibelle(); ?></a></li>
                  <?php endforeach; ?>
                    <li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
	      </ul>
                <?php endif; ?>
	</ul>
		
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<div id="cont_gestion_stock">
			
			<!-- #gestion_stock -->
			<div id="gestion_stock" class="clearfix gestion_stock_donnees">
				<?php include_partial('dsEditionFormContentCiva', array('ds' => $ds, 'form' => $form));?>

			    <div id="sous_total" class="ligne_total">
			        <h2>Sous total</h2>
			        
			        <ul>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_vt" /></li>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_sgn" /></li>
			        </ul>
			    </div>
			</div>
			<!-- fin #gestion_stock -->


			<div id="total" class="ligne_total">
				<h2>Total</h2>
	
				<ul>
					<li><input type="text" readonly="readonly" data-val-defaut="0.00" value="0.00" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="0.00" value="0.00" class="somme" data-somme-col="#col_vt" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="0.00" value="0.00" class="somme" data-somme-col="#col_sgn" /></li>
				</ul>
			</div>
	
			<ul id="btn_appelation" class="btn_prev_suiv clearfix">
				<li>
                                    <a href="<?php echo url_for('ds_edition_retour_etape', array('id' => $ds->_id, 'appellation_lieu' => $appellation_lieu, 'retour' => true)); ?>">
						<img src="/images/boutons/btn_appelation_prec.png" alt="Retourner à l'étape précédente" />
					</a>
				</li>
				<li>
					<a href="#">
                                            <input type="image" src="/images/boutons/btn_appelation_suiv.png" alt="Valider et passer à l'appellation suivante" />
					</a>
				</li>
			</ul>
		</div>
		
	</div>
	<!-- fin #application_ds -->

</form>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for("ds_lieux_stockage", $tiers) ?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>