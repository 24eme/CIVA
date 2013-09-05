<div class="clearfix">
	<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => 'validation')) ?>
</div>
<div class="clearfix">
		WESH MORRAY !
</div>
<ul class="btn_prev_suiv clearfix" id="btn_etape">
	<li class="prec">
        <a id="btn_precedent" href="<?php echo url_for('vrac_etape_conditions', $vrac) ?>">
        	<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retourner_etape_prec.png">
    	</a>
	</li>
	<li class="suiv">
        <a id="btn_suivant" href="#">
			<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider.png">
		</a>
	</li>
</ul>

