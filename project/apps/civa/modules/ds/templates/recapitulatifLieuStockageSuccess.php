<?php 
use_helper('Float');
use_helper('ds');
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3));
?>

<h2 class="titre_page"><?php echo getTitleLieuStockageStock($ds); ?></h2>

	<!-- fin .header_ds -->
	<ul id="onglets_majeurs" class="clearfix onglets_stock">
            <?php foreach ($ds->declaration->getAppellationsSorted() as $app_key => $app):  ?>
            <li>
                <a href="<?php echo url_for('ds_edition_operateur', $app); ?>"><span>
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
	

	
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<div id="recap_lieu_stockage" class="page_recap">
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
					<?php foreach ($ds->declaration->getAppellationsSorted() as $appellation) : ?>
                                        
						<tr>
							<td><?php echo $appellation->getLibelle(); ?></td>
							<td><?php echoFloat($appellation->getTotalStock()); ?></td>
							<td><?php echoFloat($appellation->getTotalNormal()); ?></td>
							<td><?php echoFloat($appellation->getTotalVt()); ?></td>
							<td><?php echoFloat($appellation->getTotalSgn()); ?></td>
						</tr>
                                            
                        <?php endforeach; ?>
					</tbody>
				</table>
				
				<div id="total" class="ligne_total">
					<h3>Total AOC</h3>
					<input type="text" readonly="readonly" value="<?php echoFloat($ds->getTotalAOC()); ?>" />
				</div>
			</div>

			<?php if($ds->declaration->getAppellations()->exist('appellation_VINTABLE')): ?>
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
							<td>Vins de table sans IG</td>
							<td><?php echoFloat($ds->getTotalVinSansIg()); ?></td>
						</tr>        
						<tr>
							<td>Vins de table mousseux</td>
                                                        <td><?php echoFloat($ds->getTotalMousseuxSansIg()); ?></td>
						</tr>
					</tbody>
				</table>
				
				<div id="total" class="ligne_total">
					<h3>Total</h3>
					<input type="text" readonly="readonly" value="<?php echoFloat($ds->getTotalVinSansIg() + $ds->getTotalMousseuxSansIg()); ?>" />
				</div>

			</div>
			<?php endif; ?>
		</div>
	</div>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('ds_edition_operateur', $ds); ?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id, 'suivant' => true)); ?>">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>
