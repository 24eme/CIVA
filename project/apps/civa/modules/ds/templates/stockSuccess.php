<form class="ajaxForm" id="form" action="<?php echo url_for('ds_edition_operateur', $noeud); ?>" method="post">

	<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3)); ?>
    <div id="ajax_error"></div>
	<h2 class="titre_page"><?php echo $ds->declarant->cvi.' - '.$ds->getEtablissement()->getNom().' - '.$ds->getEtablissement()->getAdresse(); ?></h2>
	
	<ul id="onglets_majeurs" class="clearfix onglets_stock">
		<?php foreach ($appellations as $app_key => $app):  ?>
		
		<?php $selected = ($app->getHash() == $appellation->getHash()); ?>
		<li class="<?php echo $selected ? 'ui-tabs-selected' : '' ; ?>">
			<a href="<?php echo url_for('ds_edition_operateur', $app->getRawValue()); ?>">
				<span><?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?></span> 
				<br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?>
			</a>
			
			<?php if($selected && $appellation->getConfig()->hasManyLieu()): ?>
			<?php $has_lieux = true; ?>
				<ul class="sous_onglets">
				  <?php foreach ($appellation->getLieux() as $lieu_key => $lieu): ?>
				  <li class="<?php echo ($noeud->getHash() == $lieu->getHash())? 'ui-tabs-selected' : ''; ?>">
					  <a href="<?php echo url_for('ds_edition_operateur', $lieu) ?>"><?php echo $lieu->getConfig()->getLibelle(); ?></a></li>
				  <?php endforeach; ?>
					<li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
				</ul>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
		<li>
			<a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>" style="height: 30px;">
			<br>Récapitulatif</a>
		</li>
	</ul>
		
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		<div id="aucun_produit">
			<p>Il n'y a pas de produit défini pour cette appellation</p>
		
			<div class="form_ligne">
				<label for="selectionner_cepage">Sélectionner un cépage :</label>
				<select id="selectionner_cepage">
					<option value="1">Chasselas</option>
					<option value="2">Sylvaner</option>
					<option value="3">Auxerrois</option>
				</select>
			</div>
			
			<div class="form_ligne">
				<label for="selectionner_lieu_dit">Sélectionner un lieu-dit :</label>
				<input type="text" id="selectionner_lieu_dit" />
			</div>
			
			<div class="form_btn">
				<input type="image" src="/images/boutons/btn_valider.png" alt="Valider" />
			</div>
		</div>
			
			
		<div id="cont_gestion_stock">
			
			<!-- #gestion_stock -->
			<div id="gestion_stock" class="clearfix gestion_stock_donnees <?php if(isset($has_lieux) && $has_lieux) echo 'avec_sous_onglets'; ?>">
				<?php include_partial('dsEditionFormContentCiva', array('ds' => $ds, 'form' => $form));?>
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

            <a href="<?php echo url_for('ds_ajout_produit', $appellation) ?>">Ajouter un produit</a>
	
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

