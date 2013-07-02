<?php 
use_helper('Float');
$appellations_agregee = $ds_client->getTotauxByAppellationsRecap($ds_principale);
$has_points = false;
if(isset($validation_dss)) { foreach ($validation_dss as $id_ds => $validation_ds) { if($validation_ds->hasPoints()) { $has_points = true; break; } }}
?>
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		<?php if(isset($validation_dss)) : ?>
			<?php if ($has_points): ?>
			<div id="validation_points_container">
			<?php foreach ($validation_dss as $id_ds => $validation_ds): ?>
				<?php if($validation_ds->hasPoints()): ?>
				<h2 class="lieu_stockage"><?php echo getTitleLieuStockageStock($ds_client->find($id_ds)); ?></h2>
				<?php endif; ?>
				<?php include_partial('global/validation', array('validation' => $validation_ds)); ?>
			<?php endforeach; ?>
        	</div>
        	<?php endif; ?>
        <?php endif; ?>
		<div id="recap_total_ds" class="page_recap">
			<p class="intro_declaration">Récapitulatif DRM <small>(tous lieux de stockage confondus)</small><a href="" class="msg_aide" rel="help_popup_ds_validation" title="Message aide"></a></p>
			<div id="recap_appellations">
				<table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Appellations</th>
							<th class="total">Total <span class="unites">(hl)</span></th>
							<th>Hors VT/SGN <span class="unites">(hl)</span></th>
							<th>VT <span class="unites">(hl)</span></th>
							<th>SGN <span class="unites">(hl)</span></th>
						</tr>
					</thead>
					<tbody>
                        <?php foreach ($appellations_agregee as $appellations_agregee_key => $appellations_agregee) : ?>
						<tr>
							<td class="appellation"><?php echo $appellations_agregee->nom; ?></td>
							<?php if(!is_null($appellations_agregee->volume_total)): ?>
                            <td><?php echoFloat($appellations_agregee->volume_total); ?></td>
							<td><?php echoFloat($appellations_agregee->volume_normal); ?></td>
							<td><?php echoFloat($appellations_agregee->volume_vt); ?></td>
							<td><?php echoFloat($appellations_agregee->volume_sgn); ?></td>
							<?php else: ?>
								<td colspan="4" class="neant neant_alt">Néant</td>
							<?php endif; ?>
						</tr>
                        <?php endforeach; ?>
					</tbody>
				</table>
				
				<div id="total" class="ligne_total">
					<h3>Total AOC</h3>
					<input type="text" readonly="readonly" value="<?php echoFloat($ds_client->getTotalAOC($ds_principale)); ?>" />
				</div>				                           
			</div>
                    
        	<div id="recap_autres">				
				<table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Autres</th>
							<th class="total">Total <span class="unites">(hl)</span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="appellation">Rebêches</td>
							<td><?php echoFloat($ds_principale->getRebeches()); ?></td>
						</tr>
						<tr>
							<td class="appellation">Usages industiels</td>
							<td><?php echoFloat($ds_principale->getUsagesIndustriels()); ?></td>
						</tr>
					</tbody>
				</table>     
			</div>	
			<div id="recap_vins_sans_ig">
			<table class="table_donnees">
				<thead>
					<tr>
                                            <th class="appellation">Vins sans IG&nbsp;<a title="Message aide" rel="help_popup_validation_vins_sans_ig" class="msg_aide" href=""></a></th>
						<th class="total">Total <span class="unites">(hl)</span></th>
					</tr>
				</thead>
				<tbody>        
					<tr>
						<td class="appellation">Vins Sans IG</td>
						<td><?php echoFloat($ds_client->getTotalSansIG($ds_principale)); ?></td>
					</tr>        
					<tr>
						<td class="appellation">Mousseux</td>
						<td><?php echoFloat($ds_client->getTotalSansIGMousseux($ds_principale)); ?></td>
					</tr>
				</tbody>
			</table>
                        </div>
		
			

		</div>
		
		
	</div>
	<!-- fin #application_ds -->