<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions') ?>


<!-- #principal -->
<p class="intro_declaration_recolte">Pour chaque cépage de chaque appellation, veuillez saisir les informations demandées.</p>

<?php include_partial('global/errorMessages', array('form' => $form_detail)); ?>
					
			<!--<form id="principal" action="<?php // echo url_for('@recolte'); ?>" method="post">-->
                                <?php include_partial('ongletsAppellations', array('declaration' => $declaration,
                                                                                   'onglets' => $onglets)); ?>

				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
                                    <!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix">
						<?php include_partial('ongletsCepages', array('declaration' => $declaration,
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
                                                                                                       'has_acheteurs_mout' => $has_acheteurs_mout)) ?>                                              
						
							<ul id="btn_cepage" class="btn_prev_suiv clearfix">
                                                                <?php if ($onglets->hasPreviousCepage()): ?>
                                                                    <li class="prec"><a href="<?php echo url_for($onglets->getPreviousUrlCepage()->getRawValue()) ?>"><img src="/images/boutons/btn_passer_cepage_prec.png" alt="Passer au cépage précédent" /></a></li>
                                                                <?php endif; ?>
                                                                <?php if ($onglets->hasNextCepage()): ?>
                                                                    <li class="suiv"><a href="<?php echo url_for($onglets->getNextUrlCepage()->getRawValue()) ?>"><img src="/images/boutons/btn_passer_cepage_suiv.png" alt="Passer au cépage précédent" /></a></li>
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
				
				<ul id="btn_etape" class="btn_prev_suiv clearfix">
				<!-- InstanceBeginEditable name="btn_etape" -->
					<li class="prec"><a href="<?php echo url_for('@exploitation_autres') ?>"><img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" /></a></li>
					<li class="suiv"><a href="<?php echo url_for('@validation') ?>"><img src="/images/boutons/btn_passer_etape_suiv.png" alt="Passer à l'étape suivante" /></a></li>
				<!-- InstanceEndEditable -->
				</ul>
				

				
			<!--</form>-->
			<!-- fin #principal -->

        <script type="text/javascript">
            var_liste_acheteurs = <?php echo ListAcheteursConfig::getNegocesJson() ?>;
            var_liste_caves = <?php echo ListAcheteursConfig::getCooperativesJson() ?>;
            var_liste_mouts = <?php echo ListAcheteursConfig::getMoutsJson() ?>;
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

       <?php include_partial('popupMotifNonRecolte', array()) ?>
     
       <?php include_partial('emptyAcheteurs') ?>
	