<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3)); ?>
	<!-- fin .header_ds -->
	
	<h2 class="titre_page"><?php echo $tiers->getCvi();?> : Récapitulatif</h2>

	
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<div id="recap_lieu_stockage">
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
                                        <?php foreach ($ds->getAppellations() as $appellation) : ?>
                                        
						<tr>
							<td><?php echo $appellation->getAppellation(); ?></td>
							<td><?php echo $appellation->getTotalStock(); ?></td>
							<td><?php echo $appellation->getTotalNormal(); ?></td>
							<td><?php echo $appellation->getTotalVt(); ?></td>
							<td><?php echo $appellation->getTotalSgn(); ?></td>
						</tr>
                                            
                                        <?php endforeach; ?>
					</tbody>
				</table>
				
				<div id="total" class="ligne_total">
					<h3>Total AOC</h3>
					<input type="text" readonly="readonly" value="<?php echo $ds->getTotalAOC(); ?>" />
				</div>
			</div>
			
			<div id="blocs_autres">
				<div id="bloc_autres_sans_aoc" class="bloc_autres">
					<h2 class="titre_section">Sans AOC</h2>
					<div class="contenu_section">
						<ul class="bloc_vert">
							<li>
								<label>Vins de table - Vins sans IG</label>
								<input type="text" readonly="readonly" value="<?php echo $ds->getTotalVinSansIg(); ?>" />
							</li>
							
							<li>
								<label>Vins de table - Mousseux</label>
								<input type="text" readonly="readonly" value="?" />
							</li>
						</ul>
					</div>
				</div>			

			</div>
		</div>
		
	</div>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $ds->getFirstAppellationLieu())); ?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id, 'suivant' => true)); ?>">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>





