<div class="clearfix">
	<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => 'soussignes')) ?>
</div>
<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_etape_soussignes', $vrac) ?>">
	<p>Saisissez ici les acteurs du contrat.<br />Vous pouvez ajouter des tiers via votre <a href="<?php echo url_for('@annuaire') ?>">ANNUAIRE</a></p><br />
	<?php echo $form->renderHiddenFields() ?>
	<?php echo $form->renderGlobalErrors() ?>
	<div class="clearfix">
		<?php include_partial('vrac/soussignesForm', array('form' => $form)); ?>
	</div>
	<ul class="btn_prev_suiv clearfix" id="btn_etape">
	    <li class="prec">
            <a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
                <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_annuler_ajout.png">
            </a>
	    </li>
	    <li class="suiv">
	    	<button type="submit" name="valider" style="cursor: pointer;">
	    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_passer_etape_suiv.png" />
	    	</button>
	    </li>
	</ul>
</form>

