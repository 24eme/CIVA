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
                                                                                              'onglets' => $onglets,
                                                                                              'recapitulatif' => false)); ?>
                                            
                                                <!--<a href="<?php echo url_for($onglets->getUrl('recolte_add')->getRawValue()) ?>">Ajouter</a>-->
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

                                                       <?php echo include_partial('totalCepage', array('cepage' => $cepage,
                                                                                                       'onglets' => $onglets,
                                                                                                       'acheteurs_negoce' => $acheteurs_negoce,
                                                                                                       'acheteurs_cave' => $acheteurs_cave,
                                                                                                       'has_acheteurs_mout' => $has_acheteurs_mout,
                                                                                                       'acheteurs_mouts' => $acheteurs_mout)) ?>                                              
						
							<ul id="btn_cepage" class="btn_prev_suiv clearfix">
                                                                <?php if ($onglets->hasPreviousCepage()): ?>
                                                                    <li class="prec"><a href="<?php echo url_for($onglets->getPreviousUrlCepage()->getRawValue()) ?>"><img src="/images/boutons/btn_passer_cepage_prec.png" alt="Passer au cépage précédent" /></a></li>
                                                                <?php endif; ?>
                                                                <?php if ($onglets->hasNextCepage()): ?>
                                                                    <li class="suiv"><a href="<?php echo url_for($onglets->getNextUrlCepage()->getRawValue()) ?>"><img src="/images/boutons/btn_passer_cepage_suiv.png" alt="Passer au cépage précédent" /></a></li>
                                                                <?php endif; ?>
							</ul>
						
						</div>

                                                <?php echo include_partial('totalAppellation', array('appellation' => $appellation,
                                                                                                       'onglets' => $onglets,
                                                                                                       'acheteurs_negoce' => $acheteurs_negoce,
                                                                                                       'acheteurs_cave' => $acheteurs_cave,
                                                                                                       'has_acheteurs_mout' => $has_acheteurs_mout,
                                                                                                       'acheteurs_mouts' => $acheteurs_mout)) ?>
					
					</div>
					<!-- fin #gestion_recolte -->
					
					<ul id="btn_appelation" class="btn_prev_suiv clearfix">
                                                <?php if ($onglets->hasPreviousAppellation()): ?>
                                                    <li class="prec"><a href="<?php echo url_for($onglets->getPreviousUrl()->getRawValue()) ?>"><img src="/images/boutons/btn_appelation_prec.png" alt="Retour à l'appelation précédente" /></a></li>
                                                <?php endif; ?>
						<li class="suiv"><a href="<?php echo url_for($onglets->getUrlRecap()->getRawValue()) ?>"><img src="/images/boutons/btn_appelation_suiv.png" alt="Valider et Passer à l'appelation suivante" /></a></li>
					</ul>
					
				</div>
				<!-- fin #application_dr -->
				
				
				<?php include_partial('global/boutons', array('display' => array('precedent','suivant'))) ?>

				
			<!--</form>-->
			<!-- fin #principal -->
