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
                <!-- #acheteurs_caves -->
                <div id="acheteurs_caves">
<?php
  if (count($volume_negoces)) :
?>
                        <div id="acheteurs_raisin">
                                <table cellpadding="0" cellspacing="0" class="table_donnees">
                                        <thead>
                                                <tr>
                                                        <th><img src="/images/textes/acheteurs_raisin.png" alt="Acheteurs de raisin" /></th>
                                                        <th class="cvi">n°CVI</th>
                                                        <th>Commune</th>
                                                        <th>Superficie</th>
                                                        <th>Volume total</th>
                                                        <th>dont DPLC</th>
                                                </tr>
                                        </thead>
                                        <tbody>
<?php foreach ($volume_negoces as $cvi => $v) : ?>
                                                <tr>
						<td class="nom"><?php echo $acheteurs[$cvi]->nom; ?></td>
						<td class="cvi"><?php echo $cvi; ?></td>
						<td class="rs"><?php echo $acheteurs[$cvi]->commune; ?></td>
						<td>????</td>
						<td><?php echo $v->volume; ?></td>
                                                        <td>????</td>
                                                </tr>
<?php endforeach; ?>
                                        </tbody>
                                </table>
                        </div>
<?php 
  endif; 
  if (count($volume_cooperatives)) :
?>

                        <div id="caves_cooperatives">
                                <table cellpadding="0" cellspacing="0" class="table_donnees">
                                        <thead>
                                                <tr>
                                                        <th><img src="/images/textes/caves_cooperatives.png" alt="Caves coopératives" /></th>
                                                        <th class="cvi">n°CVI</th>
                                                        <th>Raison sociale</th>
                                                        <th>Superficie</th>
                                                        <th>Volume total</th>
                                                        <th>dont DPLC</th>
                                                </tr>
                                        </thead>
                                        <tbody>
<?php foreach ($volume_cooperatives as $cvi => $v) : ?>
                                                <tr>
						<td class="nom"><?php echo $acheteurs[$cvi]->nom; ?></td>
						<td class="cvi"><?php echo $cvi; ?></td>
						<td class="rs"><?php echo $acheteurs[$cvi]->commune; ?></td>
                                                        <td>????</td>
						<td><?php echo $v->volume; ?></td>
                                                        <td>????</td>
                                                </tr>
<?php endforeach; ?>
                                        </tbody>
                                </table>
                        </div>
<?php endif; ?>
                </div>
                <!-- fin #acheteurs_caves -->

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
