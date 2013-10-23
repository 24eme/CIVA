<div id="contrats_vrac">

	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('@annuaire_commercial_ajouter') ?>">

		<div class="fond">
			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form->renderGlobalErrors() ?>
			<p>Saisissez ici l'identité du commercial de vous souhaitez ajouter.</p><br />
			<table class="table_donnees" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th><span>Identité</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<span><?php echo $form['identite']->renderError() ?></span>
							<?php echo $form['identite']->render() ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<ul class="btn_prev_suiv clearfix" id="btn_etape">
		    <li class="prec">
	            <a id="btn_precedent" href="<?php echo url_for('@annuaire_retour') ?>">
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