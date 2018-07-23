<?php
use_helper('Float');
use_helper('ds');
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3, 'recap' => 1));
?>


<h2 class="titre_page"><?php echo getTitleLieuStockageStock($ds); ?></h2>

	<?php include_partial('ds/onglets', array('ds' => $ds, 'recap' => true)) ?>

	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">

		<div id="recap_lieu_stockage" class="page_recap">
			<div id="recap_appellations">
				<table class="table_donnees pyjama_auto">
					<thead>
						<tr>
							<th class="appellation">Appellations</th>
							<th class="total">Total <span class="unites">(hl)</span></th>
							<th>hors VT/SGN <span class="unites">(hl)</span></th>
							<th>VT <span class="unites">(hl)</span></th>
							<th>SGN <span class="unites">(hl)</span></th>
						</tr>
					</thead>
					<tbody>
					<?php if($ds->hasAOC()): ?>
						<?php foreach ($ds->declaration->getAppellationsSorted() as $appellation_key => $appellation) :
		                    if(!preg_match('/(appellation_VINTABLE|genreVCI)/',$appellation_key)): ?>
		                        <tr>
		                                <td class="appellation"><?php echo $appellation->getLibelle(); ?></td>
		                                <td><?php echoFloat($appellation->getTotalStock()); ?></td>
		                                <td><?php echoFloat($appellation->getTotalNormal()); ?></td>
		                                <td><?php echoFloat($appellation->getTotalVt()); ?></td>
		                                <td><?php echoFloat($appellation->getTotalSgn()); ?></td>
		                        </tr>
		                    <?php endif; ?>
		                <?php endforeach; ?>
	            	<?php else: ?>
	            		<tr>
                            <td colspan="5" class="neant">Néant</td>
                    	</tr>
	            	<?php endif; ?>
					</tbody>
				</table>

				<div id="total" class="ligne_total">
					<h3>Total AOC</h3>
					<input type="text" readonly="readonly" value="<?php echoFloat($ds->getTotalAOC()); ?>" />
				</div>
			</div>

			<?php if($ds->declaration->exist('certification/genreVCI')): ?>
			<div id="recap_vins_sans_ig">
				<table class="table_donnees pyjama_auto">
					<thead>
						<tr>
							<th class="appellation">VCI</th>
							<th class="total">Total <span class="unites">(hl)</span></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($ds->getTotalVCI() as $libelle => $volume): ?>
						<tr>
							<td class="appellation"><?php echo $libelle; ?></td>
							<td><?php echoFloat($volume); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<div id="total" class="ligne_total">
					<h3>Total VCI</h3>
					<input type="text" readonly="readonly" value="<?php echoFloat($ds->getTotalVCIVolume()); ?>" />
				</div>

			</div>
			<?php endif; ?>

			<?php if($ds->declaration->getAppellations()->exist('appellation_VINTABLE')): ?>
			<div id="recap_vins_sans_ig">
				<table class="table_donnees pyjama_auto">
					<thead>
						<tr>
							<th class="appellation">Vins sans IG</th>
							<th class="total">Total <span class="unites">(hl)</span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="appellation">Vins sans IG</td>
							<td><?php echoFloat($ds->getTotalVinSansIg()); ?></td>
						</tr>
						<tr>
							<td class="appellation">Vins sans IG mousseux</td>
                            <td><?php echoFloat($ds->getTotalMousseuxSansIg()); ?></td>
						</tr>
					</tbody>
				</table>

				<div id="total" class="ligne_total">
					<h3>Total Vins sans IG</h3>
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
		<a autofocus="autofocus" tabindex="1" href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id, 'suivant' => true)); ?>">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>
