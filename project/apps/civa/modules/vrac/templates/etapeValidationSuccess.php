<div class="clearfix">
	<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => 'validation')) ?>
</div>
<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_etape_validation', $vrac) ?>">
	<?php echo $form->renderHiddenFields() ?>
	<?php echo $form->renderGlobalErrors() ?>
	<div class="clearfix">
		<h1>Vendeur</h1>
		<hr />
		<?php include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur)) ?>
		<br />
		<h1>Acheteur</h1>
		<hr />
		<?php include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur)) ?>
	</div>
	<br />
	<div class="clearfix">
			<table class="table_donnees" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th>Produit</th>
						<th width="40px"><span>Volume proposé</span></th>
						<th width="40px"><span>Prix unitaire</span></th>
					</tr>
				</thead>
				<tbody>
				<?php 
					foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
					foreach ($details as $detail):
				?>
				<tr>
					<td>
						<strong><?php echo $detail->getLibelle(); ?></strong><?php if ($detail->millesime) { echo ' '.$detail->millesime; } if ($detail->denomination) { echo ' '.$detail->denomination; } if ($detail->vtsgn) { echo ' '.$detail->vtsgn; } ?>
					</td>
					<td width="40px">
						<?php echo $detail->volume_propose ?>&nbsp;Hl
					</td>
					<td width="40px">
						<?php echo $detail->prix_unitaire ?>&nbsp;&euro;/Hl
					</td>
				</tr>
				<?php 
					endforeach;
					endforeach;
				?>
				</tbody>
			</table>
	</div>
	<ul class="btn_prev_suiv clearfix" id="btn_etape">
		<li class="prec">
	        <a id="btn_precedent" href="<?php echo url_for('vrac_etape_conditions', $vrac) ?>">
	        	<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retourner_etape_prec.png">
	    	</a>
		</li>
		<li class="suiv">			
	    	<button type="submit" name="valider" style="cursor: pointer;">
	    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider.png" />
	    	</button>
		</li>
	</ul>
</form>

