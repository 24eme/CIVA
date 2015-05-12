<form id="" action="<?php echo url_for('ds_edition_operateur', $ds); ?>" method="post">
	<!-- .header_ds -->
	<div class="header_ds clearfix">
		
		<ul id="etape_declaration" class="etapes_ds clearfix">
			<li class="passe">
				<a href="#"><span>Exploitation</span> <em>Etape 1</em></a>
			</li>
			<li class="passe">
				<a href="<?php echo url_for("ds_lieux_stockage", $tiers) ?>"><span>Lieux de stockage</span> <em>Etape 2</em></a>
			</li>
			<li class="actif sous_menu">
				<a href="#"><span>Stocks</span> <em>Etape 3 (lieu 3/6)</em></a>
				<ul>
					<li><a href="#">Lieu de stockage n°1</a></li>
					<li><a href="#">Lieu de stockage n°2</a></li>
					<li class="actif"><a href="#">Lieu de stockage n°3</a></li>
					<li><a href="#">Lieu de stockage n°4</a></li>
					<li><a href="#">Lieu de stockage n°5</a></li>
					<li><a href="#">Lieu de stockage n°6</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><span>Récapitulatif</span> <em>Etape 4</em></a>
			</li>
			<li>
				<a href="#"><span>Validation</span> <em>Etape 5</em></a>
			</li>
		</ul>
	
		<div class="progression_ds">
			<p>Vous avez saisi <span>40%</span> de votre DS</p>
	
			<div class="barre_progression">
				<div class="progression" style="width: 40%;"></div>
			</div>
		</div>
	</div>
	<!-- fin .header_ds -->
	
	<h2 class="titre_page"><?php echo $ds->declarant->cvi.' - '.$ds->getEtablissement()->getNom().' - '.$ds->getEtablissement()->getAdresse(); ?></h2>
	
	<!-- Enlever classe "avec_sous_onglets" quand il n'y a pas de sous-onglets -->
	<ul id="onglets_majeurs" class="clearfix onglets_stock avec_sous_onglets">
		<li>
			<a href="#"><span>AOC</span><br> Alsace blanc</a>
		</li>
		<li class="ui-tabs-selected">
			<a href="#"><span>AOC</span><br> Alsace Grand Cru</a>
			<ul class="sous_onglets">
				<li class="ui-tabs-selected premier"><a href="#">Brand</a></li>
				<li class=""><a href="#">Sommerberg</a></li>
				<li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
			</ul>
		</li>
		<li>
			<a href="#"><span>AOC</span><br> Crément d'Alsace</a>
		</li>
		<li>
			<a href="#"><span>AOC</span><br> Communales</a>
		</li>
		<li>
			<a href="#"><span>AOC</span><br> Alsace Lieu-dit</a>
		</li>
		<li>
			<a href="#"><span>&nbsp;</span><br> Autres</a>
		</li>
		<li class="recap_stock">
			<a href="#">Récapitulatif</a>
		</li>
	</ul>
	
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<div id="cont_gestion_stock">
			
			<!-- #gestion_stock -->
			<!-- Enlever classe "avec_sous_onglets" quand il n'y a pas de sous-onglets -->
			<div id="gestion_stock" class="clearfix avec_sous_onglets">
				<?php include_partial('dsEditionFormContentCiva', array('ds' => $ds, 'produits' => $ds->getProduits(),'form' => $form));?>

			    <div id="sous_total" class="ligne_total">
			        <h3>Sous total</h3>
			        
			        <ul>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_vt" /></li>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_sgn" /></li>
			        </ul>
			    </div>
			</div>
			<!-- fin #gestion_stock -->


			<div id="total" class="ligne_total">
				<h3>Total</h3>
	
				<ul>
					<li><input type="text" readonly="readonly" data-val-defaut="312.57" value="312.57" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="425.94" value="425.94" class="somme" data-somme-col="#col_vt" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="939.52" value="939.52" class="somme" data-somme-col="#col_sgn" /></li>
				</ul>
			</div>
	
			<ul id="btn_appelation" class="btn_prev_suiv clearfix">
				<li>
					<a href="#">
						<!--
							Pour le lieu de stockage précédent, utiliser :
							<img src="/images/boutons/btn_lieu_stockage_prec.png" alt="Retourner au lieu de stockage précédent" />
						-->
						<img src="/images/boutons/btn_appelation_prec.png" alt="Retourner à l'étape précédente" />
					</a>
				</li>
				<li>
					<a href="#">
						<img src="/images/boutons/btn_appelation_suiv.png" alt="Valider et passer à l'appellation suivante" />
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
		<a href="#">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>