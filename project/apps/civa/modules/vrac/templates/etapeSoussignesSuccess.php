<div class="clearfix">
	<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => 'soussignes')) ?>
</div>
<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_etape_soussignes', $vrac) ?>">
	<?php //echo $form->renderHiddenFields() ?>
	<?php //echo $form->renderGlobalErrors() ?>
	<p>Saisissez ici les volumes estimés et les prix pour chaque produit.</p><br />
	<div class="clearfix">
		
	</div>
	<ul class="btn_prev_suiv clearfix" id="btn_etape">
	    <li class="prec">
            <a id="btn_precedent" href="<?php echo url_for('@mon_espace_civa') ?>">
                <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retourner_etape_prec.png">
            </a>
	    </li>
	    <li class="suiv">
	    	<button type="submit" name="valider" style="cursor: pointer;">
	    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_passer_etape_suiv.png" />
	    	</button>
	    </li>
	</ul>
</form>

