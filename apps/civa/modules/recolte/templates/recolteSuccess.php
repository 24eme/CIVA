<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions', array('etape' => 2)) ?>


<!-- #principal -->
<p class="intro_declaration_recolte"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_declaration_recolte'); ?></p>


<?php if ($sf_user->hasFlash('msg_info')): ?>
<p><?php echo $sf_user->getFlash('msg_info') ; ?></p>
<?php endif; ?>

<?php include_partial('global/errorMessages', array('form' => $form_detail)); ?>
					
			<!--<form id="principal" action="<?php // echo url_for('@recolte'); ?>" method="post">-->
                                <?php include_partial('ongletsAppellations', array('declaration' => $declaration,
                                                                                   'onglets' => $onglets)); ?>
				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
                                    <!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix gestion_recolte_donnees">
						<?php include_partial('ongletsCepages', array('declaration' => $declaration,
                                                                                              'nb_details_current' => $nb_details_current,
                                                                                              'onglets' => $onglets,
                                                                                              'recapitulatif' => false)); ?>
                                            
                                                <!--<a href="<?php echo url_for($onglets->getUrl('recolte_add')->getRawValue()) ?>">Ajouter</a>-->
						<div id="donnees_recolte_sepage" class="clearfix">
						
							<?php echo include_partial('detailHeader', array('acheteurs' => $acheteurs,
                                                                                                         'has_acheteurs_mout' => $has_acheteurs_mout,
                                                                                                         'onglets' => $onglets)) ?>

                                                        <?php echo include_partial('detailList', array('details' => $details, 
                                                                                                       'onglets' => $onglets,
                                                                                                       'detail_key' => $detail_key,
                                                                                                       'detail_action_mode' => $detail_action_mode,
                                                                                                       'form' => $form_detail,
                                                                                                       'acheteurs' => $acheteurs,
                                                                                                       'has_acheteurs_mout' => $has_acheteurs_mout)) ?>
                                                   
                                                        <?php echo include_partial('totalCepage', array('cepage' => $onglets->getCurrentCepage(),
                                                                                                    'onglets' => $onglets,
                                                                                                    'acheteurs' => $acheteurs,
                                                                                                    'has_acheteurs_mout' => $has_acheteurs_mout)) ; ?>
						
							<ul id="btn_cepage" class="btn_prev_suiv clearfix">
                                                                <?php if ($onglets->hasPreviousCepage()): ?>
                                                                    <li class="prec"><a href="<?php echo url_for($onglets->getPreviousUrlCepage()->getRawValue()) ?>" class="btn_recolte_can_be_inactif"><img src="/images/boutons/btn_passer_cepage_prec.png" alt="Passer au cépage précédent" /></a></li>
                                                                <?php endif; ?>
                                                                <?php if ($onglets->hasNextCepage()): ?>
                                                                    <li class="suiv"><a href="<?php echo url_for($onglets->getNextUrlCepage()->getRawValue()) ?>" class="btn_recolte_can_be_inactif"><img src="/images/boutons/btn_passer_cepage_suiv.png" alt="Passer au cépage précédent" /></a></li>
                                                                <?php endif; ?>
							</ul>
						
						</div>

                                                <?php echo include_partial('totalAppellation', array('lieu' => $onglets->getCurrentLieu(),
                                                                                                    'onglets' => $onglets,
                                                                                                    'acheteurs' => $acheteurs,
                                                                                                    'has_acheteurs_mout' => $has_acheteurs_mout)) ?>
					
					</div>
					<!-- fin #gestion_recolte -->

  <?php include_partial('boutonAppellation', array('onglets' => $onglets)) ?>

				</div>
				<!-- fin #application_dr -->

                                <?php include_partial('boutons') ?>
				
			<!--</form>-->
			<!-- fin #principal -->
        <script type="text/javascript">
            var_liste_acheteurs = <?php echo ListAcheteursConfig::getNegocesJson(null, $acheteurs->negoces->toArray()->getRawValue()) ?>;
            var_liste_acheteurs_using = <?php echo ListAcheteursConfig::getNegocesJson($acheteurs->negoces->toArray()->getRawValue(), null) ?>;
            var_liste_caves = <?php echo ListAcheteursConfig::getCooperativesJson(null, $acheteurs->cooperatives->toArray()->getRawValue()) ?>;
            var_liste_caves_using = <?php echo ListAcheteursConfig::getCooperativesJson($acheteurs->cooperatives->toArray()->getRawValue(), null) ?>;
            var_liste_acheteurs_mouts = <?php echo ListAcheteursConfig::getMoutsJson(null,  $acheteurs->mouts->toArray()->getRawValue()) ?>;
            var_liste_acheteurs_mouts_using = <?php echo ListAcheteursConfig::getMoutsJson($acheteurs->mouts->toArray()->getRawValue(), null) ?>;
            var_config_popup_ajout_motif = { ajax: true , auto_open: false};
            <?php if ($sf_user->hasFlash('open_popup_ajout_motif')): ?>
                var_config_popup_ajout_motif.auto_open = true;
                var_config_popup_ajout_motif.auto_open_url = '<?php echo url_for(array_merge($onglets->getUrl('recolte_motif_non_recolte')->getRawValue(), array('detail_key' => $sf_user->getFlash('open_popup_ajout_motif')))) ?>';
            <?php endif; ?>
	</script>

        <?php include_partial('popupAjoutOnglets', array('onglets' => $onglets,
                                                                 'form_appellation' => $form_ajout_appellation,
                                                                 'form_lieu' => $form_ajout_lieu,
                                                                 'url_lieu' => $url_ajout_lieu)) ?>

        <?php include_partial('popupAjoutAcheteur', array('id' => 'popup_ajout_acheteur',
                                                          'title' => 'Ajouter un acheteur',
                                                          'action' => url_for($onglets->getUrl('recolte_add_acheteur')->getRawValue()),
                                                          'name' => 'negoces',
                                                          'cssclass' => 'vente_raisins')) ?>
        <?php include_partial('popupAjoutAcheteur', array('id' => 'popup_ajout_cave',
                                                          'title' => 'Ajouter une cave',
                                                          'action' => url_for($onglets->getUrl('recolte_add_acheteur')->getRawValue()),
                                                          'name' => 'cooperatives',
                                                          'cssclass' => 'caves')) ?>
        <?php include_partial('popupAjoutAcheteur', array('id' => 'popup_ajout_mout',
                                                          'title' => 'Ajouter un acheteur de mout',
                                                          'action' => url_for($onglets->getUrl('recolte_add_acheteur')->getRawValue()),
                                                          'name' => 'mouts',
                                                          'cssclass' => 'mouts')) ?>

       <?php include_partial('popupMotifNonRecolte') ?>
     
       <?php include_partial('emptyAcheteurs') ?>

       <?php include_partial('popupRendementsMax' , array('rendement'=>$rendement, 'min_quantite'=>$min_quantite, 'max_quantite'=>$max_quantite)) ?>

       <?php include_partial('popupDrPrecedentes' , array('campagnes'=>$campagnes)) ?>

<?php if($sf_user->hasFlash('flash_message')):?>
    <?php include_partial('popupRappelLog' , array('flash_message'=>$sf_user->getFlash('flash_message'))) ?>
<?php endif; ?>