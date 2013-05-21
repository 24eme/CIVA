<?php 
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3));
$appellations = $ds->getAppellationsArray();
?>
	<!-- fin .header_ds -->
	<ul id="onglets_majeurs" class="clearfix onglets_stock">
            <?php foreach ($appellations as $app_key => $app):  ?>
            <li>
                <?php $app_libelle = $app->libelle; ?>
                <a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $ds->getAppellationLieuKey($app_key))); ?>"><span>
                    <?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?>
                    </span> 
                    <br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?>
                </a>
            </li>
            <?php 
            endforeach;
            ?>
                <li class="ui-tabs-selected">
                        <a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>" style="height: 30px;">
                        <br>Récapitulatif</a>
                </li>                
        </ul>
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
							<td><?php echo $appellation->getLibelle(); ?></td>
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
                    <br>
			<div id="blocs_autres">
                                <table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Vin sans IG</th>
							<th class="total">Total</th>
						</tr>
					</thead>
                                        <tbody>
						<tr>
							<td>Vin de table sans IG</td>
							<td><?php echo $ds->getTotalVinSansIg(); ?></td>
						</tr>  
                                                <tr>
							<td>Vin de table Mousseux</td>
							<td><?php echo "?"; ?></td>
						</tr>  
					</tbody>
				</table>
                                <div id="total" class="ligne_total">
					<h3>Total</h3>
					<input type="text" readonly="readonly" value="<?php echo $ds->getTotalVinSansIg(); ?>" />
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





