<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions') ?>

<?php echo include_partial('global/errorMessages', array('form' => $form)); ?>

<!-- #principal -->
			<form id="principal" action="" method="post">
                                <?php include_partial('ongletsAppellations', array('declaration' => $declaration,
                                                                                   'onglets' => $onglets)); ?>

				<!-- #application_dr -->
				<div id="application_dr" class="clearfix">
				
					<!-- #gestion_recolte -->
					<div id="gestion_recolte" class="clearfix">
						<?php include_partial('ongletsCepages', array('declaration' => $declaration,
                                                                                              'onglets' => $onglets, 'recapitulatif' => true)); ?>
						<div class="recapitualtif clearfix" id="donnees_recolte_sepage">
						
							<p class="intro"></p>
							
							<div id="total_appelation">
								<h2 class="titre_section">Total Appelation</h2>
								<div class="contenu_section">
									<div class="bloc_gris">
										<table cellspacing="0" cellpadding="0" class="table_donnees">
											<tbody>
												<tr>
													<td>Superficie :</td>
    <td class="valeur alt"><?php echo $appellationlieu->getTotalSuperficie(); ?> ares</td>
												</tr>
												<tr>
													<td>Volume total récolté :</td>
													<td class="valeur alt"><?php echo $appellationlieu->getTotalVolume() ;?> hl</td>
												</tr>
												<tr>
													<td>Volume revendiqué :</td>
													<td class="valeur alt"><?php echo $appellationlieu->getVolumeRevendiqueFinal(); ?> hl</td>
												</tr>
												<tr>
													<td>dont DPLC :</td>
													<td class="valeur alt"><?php echo $appellationlieu->getDplcFinal(); ?> hl</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div id="recap_ventes">
								<h2 class="titre_section">Récapitulatif des ventes</h2>
								<div class="contenu_section">
        <?php echo $form->renderHiddenFields(); ?>
        <?php /*echo $form->renderGlobalErrors();
foreach($appellationlieu->acheteurs as $cvi => $info) {
  echo $form['cvi_'.$cvi]['superficie']->renderError();
  echo $form['cvi_'.$cvi]['dontdplc']->renderError();
} */
?>
									<div class="bloc_gris">
										<table cellspacing="0" cellpadding="0" class="table_donnees">
											<thead>
												<tr>
													<th><img alt="Acheteurs et caves" src="/images/textes/acheteurs_caves.png"></th>
													<th class="cvi">n°CVI</th>
													<th class="commune"><span>Commune</span></th>				
													<th><span>Superficie</span></th>
													<th><span>Volume total</span></th>
													<th><span>Dont DPLC</span></th>
												</tr>
											</thead>
											<tbody>
    <?php foreach($appellationlieu->acheteurs as $cvi => $info) : ?>
												<tr>
    <td class="nom"><?php echo $info->getNom();?></td>
													<td class="cvi alt"><?php echo $cvi; ?></td>
													<td class="commune"><?php echo $info->getCommune(); ?></td>
													<td class="superficie alt"><?php echo $form['cvi_'.$cvi]['superficie']->render(); ?> ha</td>
													<td><?php echo $info->getVolume(); ?> hl</td>
													<td class="dplc alt"><?php echo $form['cvi_'.$cvi]['dontdplc']->render(); ?> hl</td>
												</tr>
<?php endforeach; ?>
											</tbody>
										</table>
										<div class="btn">
											<input type="image" alt="Valider" src="/images/boutons/btn_valider_2.png">
										</div>
									
									</div>
								</div>
							</div>
						
						</div>					</div>	
					<!-- fin #gestion_recolte -->
					
  <?php include_partial('boutonAppellation', array('onglets' => $onglets, 'is_recap'=>true)) ?>
					
				</div>
				<!-- fin #application_dr -->
				
				
				<?php include_partial('global/boutons', array('display' => array('precedent','suivant'))) ?>

				
			</form>
			<!-- fin #principal -->
