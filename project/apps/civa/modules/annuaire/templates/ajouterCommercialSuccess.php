<div id="contrats_vrac">

	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('@annuaire_commercial_ajouter') ?>">
		<h2 class="titre_principal">Ajouter un interlocuteur commercial</h2>
		<div class="fond">
			<?php echo $form->renderHiddenFields() ?>
			<?php echo $form->renderGlobalErrors() ?>
			<p>Saisissez ici l'identité et l'email de l'interlocuteur commercial que vous souhaitez ajouter.</p><br />
			<table class="table_donnees" style="width: 465px;">
				<thead>
					<tr>
						<th>Identité</th>
						<th><span>Email</span></th>
						<th><span>Téléphone</span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<span><?php echo $form['identite']->renderError() ?></span>
							<?php echo $form['identite']->render() ?>
						</td>
						<td>
							<span><?php echo $form['email']->renderError() ?></span>
							<?php echo $form['email']->render() ?>
						</td>
						<td>
							<span><?php echo $form['telephone']->renderError() ?></span>
							<?php echo $form['telephone']->render() ?>
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
		        <button class="btn_image" type="submit" name="valider" style="cursor: pointer;">
		    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider_2.png" />
		    	</button>
		    </li>
		</ul>
	</form>
</div>