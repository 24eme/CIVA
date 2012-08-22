<?php use_helper('civa') ?>
<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions', array('etape' => 2, 'help_popup_action'=>$help_popup_action)) ?>

<?php include_partial('global/errorMessages', array('form' => $form)); ?>

<!-- #principal -->
			<form id="principal" action="" method="post" onsubmit="return valider_can_submit();">
                                <?php echo $form->renderHiddenFields(); ?>
                                <?php include_partial('ongletsAppellations', array('declaration' => $declaration,
                                                                                   'onglets' => $onglets)); ?>

				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
				
					<!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix gestion_recolte_recapitulatif">
						<?php include_partial('ongletsCepages', array('declaration' => $declaration,
                                                                                              'onglets' => $onglets,
                                                                                              'recapitulatif' => true)); ?>

						<div class="recapitualtif clearfix" id="donnees_recolte_sepage">
						
							<p class="intro"></p>
							
							<div id="total_appelation">
								<h2 class="titre_section">
                                                                    <?php if($isGrandCru){ ?>
                                                                    Total Lieu-dit
                                                                    <?php }else{ ?>
                                                                    Total Appellation
                                                                    <?php } ?>
                                                                </h2>
								<div class="contenu_section">
									<div class="bloc_gris">
										<table cellspacing="0" cellpadding="0" class="table_donnees">
											<tbody>

												<tr>
													<td>Superficie <span class="unites">(ares)</span> :</td>
                                                    <td class="valeur alt"><?php echoFloat($appellationlieu->getTotalSuperficie()); ?> ares</td>
												</tr>
												<tr>
													<td>Volume total récolté <span class="unites">(hl)</span> :</td>
                                                                                                        <td class="valeur alt"><?php echoFloat($appellationlieu->getTotalVolume()) ;?> hl</td>
												</tr>
                                                                                                <?php if($appellationlieu->getConfig()->hasRendement()): ?>
												<tr>
													<td>Volume revendiqué <span class="unites">(hl)</span> :</td>
												   <td class="valeur alt"><?php echoFloat($appellationlieu->getVolumeRevendique()); ?> hl</td>
												</tr>

                                                <?php if( isset($form['usages_industriels_saisi'] )) :?>
												<tr>
													<td>Usages industriels <span class="unites">(hl)</span> :</td>
                                                    <td class="valeur alt">
                                                        <?php echo $form['usages_industriels_saisi'] ?> hl
                                                    </td>
												</tr>
                                                <?php endif; ?>                                                                <?php endif; ?>
											</tbody>
										</table>

                                    <?php if( isset($form['usages_industriels_saisi'])
                                           && !$form['usages_industriels_saisi']->getWidget()->getAttribute('readonly')) :?>
                                        <div class="btn">
                                            <input type="image" src="/images/boutons/btn_valider_2.png" alt="Valider" type="submit">
                                        </div>
                                    <?php endif; ?>

									</div>
								</div>
							</div>
                                                       
                                                        
							<div id="recap_ventes">
								<h2 class="titre_section">Récapitulatif des ventes <a href="" class="msg_aide" rel="help_popup_DR_recap_vente" title="Message aide"></a></h2>
								<div class="contenu_section">
									<div class="bloc_gris">
                                                                        <?php if($appellationlieu->hasAcheteurs() > 0): ?>
										<table cellspacing="0" cellpadding="0" class="table_donnees">
											<thead>
												<tr>
													<th><img alt="Acheteurs et caves" src="/images/textes/acheteurs_caves.png"></th>
													<th class="cvi">n°CVI</th>
													<th class="commune"><span>Commune</span></th>				
													<th><span>Superficie</span></th>
													<th><span>Volume total</span></th>
													<th><span>dont DPLC</span></th>
												</tr>
											</thead>
											<tbody>
                                                                                            <?php foreach($appellationlieu->acheteurs as $type => $acheteurs_type) : ?>
                                                                                                <?php foreach($acheteurs_type as $cvi => $info): ?>
                                                                                                    <tr>
                                                                                                            <td class="nom">
                                                                                                                <?php echo $info->getNom();?>
                                                                                                                <?php if ($type == 'mouts'): ?>
                                                                                                                <br />
                                                                                                                <small>(Acheteur de mouts)</small>
                                                                                                                <?php endif; ?>
                                                                                                            </td>
                                                                                                            <td class="cvi alt"><?php echo $cvi; ?></td>
                                                                                                            <td class="commune"><?php echo $info->getCommune(); ?></td>
                                                                                                            <?php if($appellationlieu->getConfig()->hasRendement()): ?>
                                                                                                                <td class="superficie alt <?php echo ($form[$type.'_cvi_'.$cvi]['superficie']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>"><?php echo $form[$type.'_cvi_'.$cvi]['superficie']->render(array("class" => 'num')); ?> ares</td>
                                                                                                            <?php else: ?>
                                                                                                                <td class="superficie"></td>
                                                                                                            <?php endif; ?>
                                                                                                            <td><?php echoFloat( $info->getVolume()); ?> hl</td>
                                                                                                            <?php if($appellationlieu->getConfig()->hasRendement()) : ?>
                                                                                                                <td class="dplc alt <?php echo ($form[$type.'_cvi_'.$cvi]['dontdplc']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>"><?php echo $form[$type.'_cvi_'.$cvi]['dontdplc']->render(array("class" => 'num')); ?> hl</td>
                                                                                                            <?php else: ?>
                                                                                                                <td class="dplc"></td>
                                                                                                            <?php endif; ?>
                                                                                                    </tr>
                                                                                                <?php endforeach; ?>
                                                                                            <?php endforeach; ?>
											</tbody>
										</table>
										<div class="btn">
											<input type="image" alt="Valider" src="/images/boutons/btn_valider_2.png">
										</div>
                                                                                <?php else: ?>
                                                                                <p> Aucune vente </p>
                                                                                <?php endif; ?>
									</div>
								</div>
							</div>
                                                        
						
						</div>					</div>	
					<!-- fin #gestion_recolte -->
					
                                                <?php include_partial('boutonAppellation', array('onglets' => $onglets, 'is_recap'=>true)) ?>
					
				</div>
				<!-- fin #application_dr -->
				
				<?php include_partial('boutons') ?>

                                <?php include_partial('initRendementsMax') ?>
                                
                                <?php include_partial('popupDrPrecedentes' , array('campagnes'=>$campagnes)) ?>

				
			</form>
			<!-- fin #principal -->

                        <?php include_partial('recolte/popupAjoutOnglets', array('onglets' => $onglets,
                                                         'form_appellation' => $form_ajout_appellation,
                                                         'form_lieu' => $form_ajout_lieu,
                                                         'url_lieu' => $url_ajout_lieu)) ?>

                        <script type="text/javascript">
                            function valider_can_submit()
                            {
                                <?php if($appellationlieu->acheteurs->count() > 0 && $appellationlieu->getConfig()->hasRendement()): ?>
                                var total_superficie = <?php echoFloat( $appellationlieu->getTotalSuperficie()); ?>;
                                var total_dontdplc = <?php echoFloat( $appellationlieu->getDplc()); ?>;
                                var sum_superficie = 0;
                                var sum_dont_dplc = 0;
                                $('#recap_ventes table.table_donnees tr td.superficie input.num').each(function() {
                                    if ($(this).val()) {
                                        sum_superficie += parseFloat($(this).val());
                                    }
                                });
                                sum_superficie = trunc(sum_superficie, 2);

                                $('#recap_ventes table.table_donnees tr td.dplc input.num').each(function() {
                                    if ($(this).val()) {
                                        sum_dont_dplc += parseFloat($(this).val());
                                    }
                                });
                                sum_dont_dplc = trunc(sum_dont_dplc, 2);

                                if (sum_superficie > total_superficie) {
                                    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_recap_vente_popup_superficie_trop_eleve')); ?></p>');
                                    openPopup($('#popup_msg_erreur'), 0);
                                    return false;
                                }
                                if (sum_dont_dplc > total_dontdplc) {
                                    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_recap_vente_popup_dplc_trop_eleve')); ?></p>');
                                    openPopup($('#popup_msg_erreur'), 0);
                                    return false;
                                }
                                <?php endif; ?>
                                return true;
                            }
                        </script>
                        <div id="popup_msg_erreur" class="popup_ajout" title="Erreur !">
                        </div>
