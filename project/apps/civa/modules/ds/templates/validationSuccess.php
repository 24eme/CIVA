<?php 
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds_principale, 'etape' => 5)); 
include_partial('recapitulatifDs', array('ds_principale' => $ds_principale, 'ds_client' => $ds_client)); 
?> 

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
            <a href="<?php echo url_for('ds_autre', $ds);?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="#">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>





