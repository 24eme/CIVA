<!-- #principal -->
			<form id="principal" action="<?php echo url_for('@recolte'); ?>" method="post" style="opacity: 0.5">
			
				<ul id="onglets_majeurs" class="clearfix onglets_courts">
					<li class="ui-tabs-selected"><a href="#">AOC<br /> <span>Alsace Blanc</span></a></li>
					<li><a href="#"><span>Klevener de<br /> Helligenstein</span></a></li>

					<li><a href="#">AOC<br /> <span>Pinot Noir</span></a></li>
					<li><a href="#">AOC<br /> <span>Pinot Noir Rouge</span></a></li>
					<li><a href="#">AOC <span>Alsace<br /> Grand Cru</span></a></li>
					<li><a href="#">AOC<br /> <span>Crémant d'alsace</span></a></li>

					<li><a href="#"><span>Vins de table</span></a></li>
				</ul>
			
				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
				
					<!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix">
						<ul id="liste_sepages">
							<li class="ui-tabs-selected"><a href="#">Sylvaner <span>(2)</span></a></li>

							<li><a href="#">Riesling <span></span></a></li>
							<li><a href="#">Chasselas <span></span></a></li>
							<li><a href="#">Pinot Blanc <span>(1)</span></a></li>
							<li><a href="#">Muscat <span></span></a></li>
							<li><a href="#">Pinot Gris <span></span></a></li>
							<li><a href="#">Gewurztraminer <span></span></a></li>

							<li><a href="#">Edel <span></span></a></li>
						</ul>
					
						<div id="donnees_recolte_sepage" class="clearfix">
						
							<div id="colonne_intitules">
								<ul class="denomination_mention">
									<li>Dénomination complémentaire</li>
									<li>Mention VT/SGN</li>

								</ul>
								
								<p class="superficie">Superficie</p>
								
								<div class="vente_raisins">
									<h3>Ventes de Raisins</h3>
									<ul>
										<li>Acheteur 1</li>
										<li>Acheteur 2</li>

										<li>Acheteur 3</li>
										<li>Acheteur 4</li>
									</ul>
								</div>
								
								<div class="caves">
									<h3>Caves Coopératives</h3>
									<ul>

										<li>Cave 1</li>
										<li>Cave 2</li>
										<li>Cave 3</li>
										<li>Cave 4</li>
									</ul>
								</div>
								
								<p class="vol_place">Volume sur place</p>

								<p class="vol_total_recolte">Volume Total Récolté</p>
								
								<ul class="vol_revendique_dplc">
									<li>Volume revendiqué</li>
									<li>DPLC</li>
								</ul>
							</div>
						
							<ul id="btn_cepage" class="btn_prev_suiv clearfix">

								<li class="prec"><input type="image" src="/images/boutons/btn_passer_cepage_prec.png" alt="Passer au cépage précédent" name="retourner_cepage" /></li>
								<li class="suiv"><input type="image" src="/images/boutons/btn_passer_cepage_suiv.png" alt="Passer au cépage précédent" name="passer_cepage" /></li>
							</ul>
						
						</div>
					
						<div id="recolte_totale_aoc">
						
						</div>
					</div>
					<!-- fin #gestion_recolte -->
					
					<ul id="btn_appelation" class="btn_prev_suiv clearfix">

						<li class="prec"><input type="image" src="/images/boutons/btn_appelation_prec.png" alt="Retour à l'appelation précédente" name="retourner_appelation" /></li>
						<li class="suiv"><input type="image" src="/images/boutons/btn_appelation_suiv.png" alt="Valider et Passer à l'appelation suivante" name="passer_appelation" /></li>
					</ul>
					
				</div>
				<!-- fin #application_dr -->
				
				
				<?php include_partial('global/boutons', array('display' => array('precedent','suivant'))) ?>

				
			</form>
			<!-- fin #principal -->
