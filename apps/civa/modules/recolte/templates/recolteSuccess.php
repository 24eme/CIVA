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
		var_liste_acheteurs = ['Acheteur n°01|@1234556|@Commune 1', 'Acheteur n°02|@8963486|@Commune 2', 'Acheteur n°03|@0997322|@Commune 3', 'Acheteur n°04|@4625732|@Commune 4'];
		var_liste_caves = ['Cave n°01|@1234556|@Commune 1', 'Cave n°02|@8963486|@Commune 2', 'Cave n°03|@0997322|@Commune 3', 'Cave n°04|@4625732|@Commune 4'];
	</script>

        <div id="popup_ajout_acheteur" class="popup_ajout" title="Ajouter un acheteur">
		<form action="#" method="post">
			<label for="champ_acheteur_nom">Entrez le nom de l'acheteur, son CVI ou sa commune :</label>
			<input id="champ_acheteur_nom" class="nom" type="text" name="" />
			<input class="cvi" type="hidden" name="" />
			<input class="commune" type="hidden" name="" />
			<input type="image" name="" src="../images/boutons/btn_valider.png" alt="Valider" />
		</form>
	</div>

	<div id="popup_ajout_cave" class="popup_ajout" title="Ajouter une cave">
		<form action="#" method="post">
			<label for="champ_cave_nom">Entrez le nom de la cave, son CVI ou sa commune :</label>
			<input id="champ_cave_nom" class="nom" type="text" name="" />
			<input class="cvi" type="hidden" name="" />
			<input class="commune" type="hidden" name="" />
			<input type="image" name="" src="../images/boutons/btn_valider.png" alt="Valider" />
		</form>
	</div>

        <?php include_partial('recolte/ajoutPopupOnglets', array('onglets' => $onglets,
                                                                 'form_appellation' => $form_ajout_appellation,
                                                                 'form_lieu' => $form_ajout_lieu,
                                                                 'url_lieu' => $url_ajout_lieu)) ?>
	