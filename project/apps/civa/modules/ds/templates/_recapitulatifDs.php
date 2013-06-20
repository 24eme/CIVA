<?php 
use_helper('Float');
$appelations_agregee = $ds_client->getTotauxByAppellationsRecap($ds_principale);
?>
	<ul id="onglets_majeurs" class="clearfix">
		<li class="ui-tabs-selected"><a href="#recap_total_ds">Récapitulatif DRM</a></li>
	</ul>

	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<p class="intro_declaration">Récapitulatif DRM <small>(tous lieux de stockage confondus)</small></p>
		
		<?php
			foreach ($validation_dss as $id_ds => $validation_ds):
				if($validation_ds->isPoints()):
		?>
			<h2><?php echo getTitleLieuStockageStock($ds_client->find($id_ds)); ?></h2>
		<?php 
			endif;
			include_partial('document_validation/validation', array('validation' => $validation_ds));
			endforeach; 
		?>
            
		<div id="recap_total_ds" class="page_recap">
			<div id="recap_appellations">
				<table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Appellations</th>
							<th class="total">Total</th>
							<th>Hors VT/SGN</th>
							<th>VT</th>
							<th>SGN</th>
						</tr>
					</thead>
					<tbody>
                        <?php foreach ($appelations_agregee as $apellation_agregee_key => $apellation_agregee) : ?>
						<tr>
							<td class="appellation"><?php echo $apellation_agregee->nom; ?></td>
                                                        <td><?php echoFloat($apellation_agregee->volume_total); ?></td>
							<td><?php echoFloat($apellation_agregee->volume_normal); ?></td>
							<td><?php echoFloat($apellation_agregee->volume_vt); ?></td>
							<td><?php echoFloat($apellation_agregee->volume_sgn); ?></td>
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
							<th class="total">Total</th>
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
						<th class="appellation">Vins sans IG</th>
						<th class="total">Total</th>
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