<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions') ?>

<!-- #principal -->
			<!--<form id="principal" action="<?php // echo url_for('@recolte'); ?>" method="post">-->
                                <?php include_partial('ongletsAppellations', array('declaration' => $declaration,
                                                                                   'configuration' => $configuration,
                                                                                   'onglets' => $onglets)); ?>

				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
				
					<!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix">
						<?php include_partial('ongletsCepages', array('declaration' => $declaration,
                                                                                              'configuration' => $configuration,
                                                                                              'onglets' => $onglets)); ?>
                                                <a href="<?php echo url_for($onglets->getUrl('recolte_add')->getRawValue()) ?>">Ajouter</a>
						<div id="donnees_recolte_sepage" class="clearfix">
						
							<div id="colonne_intitules">
								<ul class="denomination_mention">
									<li>Dénomination complémentaire</li>
									<li>Mention VT/SGN</li>

								</ul>
								
								<p class="superficie">Superficie</p>


								<div class="vente_raisins">
									<h3>Ventes de Raisins</h3>
                                                                        <?php if ($acheteurs_negoce->count() > 0): ?>
									<ul>
                                                                            <?php foreach($acheteurs_negoce as $cvi): ?>
                                                                            <li><?php echo $list_acheteurs_negoce[$cvi]['nom'] ?></li>
                                                                            <?php endforeach; ?>
									</ul>
                                                                        <?php endif; ?>
								</div>

								
								<div class="caves">
									<h3>Caves Coopératives</h3>
									<?php if ($acheteurs_cave->count() > 0): ?>
									<ul>
                                                                            <?php foreach($acheteurs_cave as $cvi): ?>
                                                                            <li><?php echo $list_acheteurs_cave[$cvi]['nom'] ?></li>
                                                                            <?php endforeach; ?>
									</ul>
                                                                        <?php endif; ?>
								</div>
								
								<p class="vol_place">Volume sur place</p>

								<p class="vol_total_recolte">Volume Total Récolté</p>
								
								<ul class="vol_revendique_dplc">
									<li>Volume revendiqué</li>
									<li>DPLC</li>
								</ul>
							</div>

                                                        <?php echo include_partial('detailList', array('details' => $details, 
                                                                                                       'onglets' => $onglets,
                                                                                                       'detail_key' => $detail_key,
                                                                                                       'detail_action_mode' => $detail_action_mode,
                                                                                                       'form' => $form_detail)) ?>
						
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

				
			<!--</form>-->
			<!-- fin #principal -->
