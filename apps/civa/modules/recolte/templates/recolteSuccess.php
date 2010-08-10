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
						
							<?php echo include_partial('detailHeader', array('acheteurs_negoce' => $acheteurs_negoce,
                                                                                                         'acheteurs_cave' => $acheteurs_cave,
                                                                                                         'has_acheteurs_mout' => $has_acheteurs_mout,
                                                                                                         'acheteurs_mout' => $acheteurs_mout,
                                                                                                         'list_acheteurs_negoce' => $list_acheteurs_negoce,
                                                                                                         'list_acheteurs_cave' => $list_acheteurs_cave,
                                                                                                         'list_acheteurs_mout' => $list_acheteurs_mout)) ?>

                                                        <?php echo include_partial('detailList', array('details' => $details, 
                                                                                                       'onglets' => $onglets,
                                                                                                       'detail_key' => $detail_key,
                                                                                                       'detail_action_mode' => $detail_action_mode,
                                                                                                       'form' => $form_detail,
                                                                                                       'acheteurs_negoce' => $acheteurs_negoce,
                                                                                                       'acheteurs_cave' => $acheteurs_cave,
                                                                                                       'has_acheteurs_mout' => $has_acheteurs_mout,
                                                                                                       'acheteurs_mout' => $acheteurs_mout)) ?>
						
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
