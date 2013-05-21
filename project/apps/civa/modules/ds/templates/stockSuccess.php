<?php 
$appellations = $ds->getAppellationsArray();
$appellation = $appellations[preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu)];
$current_lieu = null;
$firstAppellation = ($ds->getFirstAppellationLieu() == $appellation_lieu) && ($ds->isDsPrincipale());
?>
<form class="ajaxForm" id="form_<?php echo $ds->_id."_".$appellation_lieu; ?>" action="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $appellation_lieu)); ?>" method="post">
    <?php 
        echo $form->renderHiddenFields();
        echo $form->renderGlobalErrors();
        include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3));
    ?>
    <div id="ajax_error"></div>
	<p id="adresse_stock"><?php echo $ds->declarant->cvi.' - '.$ds->getEtablissement()->getNom().' - '.$ds->getEtablissement()->getAdresse(); ?></p>
	
	<ul id="onglets_majeurs" class="clearfix onglets_stock">
            <?php foreach ($appellations as $app_key => $app):  ?>
            <li <?php echo ($app_key==preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu))? 'class="ui-tabs-selected"' : '' ; ?> >
                     <a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $ds->getAppellationLieuKey($app_key))); ?>"><span>
                        <?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?>
                        </span> 
                        <br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?></a>
		</li>
                <?php 
                endforeach;
                ?>
                <li>
                        <a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>" style="height: 30px;">
                        <br>Récapitulatif</a>
		</li>
              <?php $appellation_k = preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu);
                if($ds->hasManyLieux($appellation_k))  : ?>
              <ul class="sous_onglets">
                  <?php foreach ($ds->getLieuxFromAppellation($appellation_k) as $lieu_key => $lieu) :  
                      $lieu_k = preg_replace('/^lieu/','', $lieu_key);
                      if(preg_replace('/^[A-Z]*-/', '', $appellation_lieu) == $lieu_k) $current_lieu = $lieu;
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
                            <?php if($ds->hasManyLieux($appellation_k)): 
                                ?>
			    <div id="sous_total" class="ligne_total">
			        <h2>Sous total</h2>
			        
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
				<h2>Total <?php echo $appellation->libelle; ?></h2>
				<ul>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echo getDefaultTotal('total_normal',$appellation, $current_lieu); ?>" value="0.00" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echo getDefaultTotal('total_vt',$appellation, $current_lieu); ?>" value="0.00" class="somme" data-somme-col="#col_vt" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echo getDefaultTotal('total_sgn',$appellation, $current_lieu); ?>" value="0.00" class="somme" data-somme-col="#col_sgn" /></li>
				</ul>
			</div>

            <a href="<?php echo url_for('ds_ajout_produit', array('id' => $ds->_id, 'appellation_lieu' => $appellation_lieu)) ?>">Ajouter un produit</a>
	
			<ul id="btn_appelation" class="btn_prev_suiv clearfix">
                            
				<li>
                                <?php if(!$firstAppellation): ?>
                                    <a class="ajax" href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $ds->getPreviousAppellationLieu($appellation_lieu))); ?>">
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

