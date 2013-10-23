<div id="contrats_vrac" class="clearfix">

	<div class="ajout_annuaire">

		<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('annuaire_ajouter', array('identifiant' => $identifiant, 'type' => $type)) ?>">

			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form->renderGlobalErrors() ?>

			<p>Saisissez ici le type et cvi du tiers que vous souhaitez ajouter à votre annuaire.</p>
			<?php include_partial('annuaire/ajouterForm', array('form' => $form)); ?>

			<?php if ($tiers): ?>
				<div class="informations">
					<?php include_partial('annuaire/tiersInformations', array('tiers' => $tiers)); ?>
				</div>
			<?php endif; ?>

			<ul class="btn_prev_suiv clearfix" id="btn_etape">
			    <li class="prec">
		            <a id="btn_precedent" href="<?php echo url_for('annuaire_ajouter', array('identifiant' => $identifiant, 'type' => $type)) ?>">
		                <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retour.png">
		            </a>
			    </li>
			    <li class="suiv">
			    	<button type="submit" name="valider" style="cursor: pointer;">
			    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider_2.png" />
			    	</button>
			    </li>
			</ul>
		</form>

	</div>

</div>