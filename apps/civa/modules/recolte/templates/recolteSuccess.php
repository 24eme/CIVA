<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions') ?>

<!-- #principal -->
			<form id="principal" action="<?php echo url_for('@recolte'); ?>" method="post" style="opacity: 0.5">
                                <?php include_partial('ongletsAppellations', array('appellations' => $declaration->recolte,
                                                                                   'appellation_current_key' => $appellation_current_key,
                                                                                   'appellations_config' => $configuration->recolte
                                                                                   )); ?>

				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
				
					<!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix">
						<?php include_partial('ongletsCepages', array('cepages' => $declaration->recolte->get($appellation_current_key)->lieu,
                                                                                              'cepage_current_key' => $cepage_current_key,
                                                                                              'cepages_config' => $configuration->recolte->get($appellation_current_key)->lieu
                                                                                            )); ?>
					
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
