<div id="contrats_vrac" class="clearfix">
	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('annuaire_selectionner'); ?><?php if (isset($redirect)): ?>?redirect=<?php echo $redirect ?><?php endif; ?>">

		<div class="fond">
			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form->renderGlobalErrors() ?>
			<p>Saisissez ici le type et cvi du tiers que vous souhaitez ajouter à votre annuaire.</p><br />
			<div class="clearfix">
				<?php include_partial('annuaire/ajouterForm', array('form' => $form)); ?>
			</div>
		</div>
		<ul class="btn_prev_suiv clearfix" id="btn_etape">
		    <li class="prec">
	            <a id="btn_precedent" href="<?php echo url_for('@annuaire') ?>">
	                <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retour.png">
	            </a>
		    </li>
		    <li class="suiv">
		    	<button type="submit" name="valider" style="cursor: pointer;">
		    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider.png" />
		    	</button>
		    </li>
		</ul>
	</form>
</div>