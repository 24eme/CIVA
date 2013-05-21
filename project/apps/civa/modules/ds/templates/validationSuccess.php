<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds_principale, 'etape' => 5)); 
$appelations_agregee = $ds_client->getTotauxByAppellationsRecap($ds_principale);
?>

	
	<h2 class="titre_page">Récapitulatif de votre déclaration de Stocks</h2>
	
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<div id="recap_total_ds">
			<div id="recap_appellations">
				<table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Appellations</th>
							<th class="total">Total</th>
							<th>Stocks hors VT / SGN</th>
							<th>VT</th>
							<th>SGN</th>
						</tr>
					</thead>
					<tbody>
                                            <?php foreach ($appelations_agregee as $apellation_agregee_key => $apellation_agregee) : ?>
						<tr>
							<td> <?php echo $apellation_agregee->nom; ?></td>
							<td><?php echo $apellation_agregee->volume_total; ?></td>
							<td><?php echo $apellation_agregee->volume_normal; ?></td>
							<td><?php echo $apellation_agregee->volume_vt; ?></td>
							<td><?php echo $apellation_agregee->volume_sgn; ?></td>
						</tr>
                                            <?php endforeach; ?>
					</tbody>
				</table>
				
				<div id="total" class="ligne_total">
					<h3>Total AOC</h3>
					<input type="text" readonly="readonly" value="<?php echo $ds_client->getTotalAOC($ds_principale); ?>" />
				</div>				                           
			</div>
		</div>
                    
				<table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Vin sans IG</th>
							<th class="total">Volume</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>TOTAL Sans IG</td>
							<td><?php echo $ds_client->getTotalSansIG($ds_principale); ?></td>
						</tr>
						<tr>
							<td>TOTAL Mousseux</td>
							<td><?php echo '?'; ?></td>
						</tr>
                                       	</tbody>
				</table>  
                            
                                <table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Autres</th>
							<th class="total">Total</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>TOTAL Rebêches</td>
							<td><?php echo $ds_principale->getRebeches(); ?></td>
						</tr>
						<tr>
							<td>TOTAL Usages industiels</td>
							<td><?php echo $ds_principale->getUsagesIndustriels(); ?></td>
						</tr>
                                       	</tbody>
				</table>     
		
	</div>
	<!-- fin #application_ds -->


<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
            <a href="<?php echo url_for('ds_autre', array('cvi' => $tiers->cvi));?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="#">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>





