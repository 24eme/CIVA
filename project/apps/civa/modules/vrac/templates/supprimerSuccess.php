<div id="contrat_onglet">
<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected">
		<a href="#" style="height: 18px;">
			Suppression de votre contrat vrac<?php if ($vrac->numero_archive): ?> numéro de visa <?php echo $vrac->numero_archive ?><?php endif; ?>
		</a>
	</li>
</ul>
</div>
<div id="contrats_vrac" class="fiche_contrat">

	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_supprimer', array('sf_subject' => $vrac)) ?>">
		<?php echo $form->renderHiddenFields() ?>
		<?php echo $form->renderGlobalErrors() ?>

		<div class="fond">		
		
			<p>Vous êtes sur le point de supprimer votre contrat vrac<?php if ($vrac->numero_archive): ?> numéro de visa <?php echo $vrac->numero_archive ?><?php endif; ?>.</p>
			<br />
			
			<table class="validation table_donnees">
				<thead>
					<tr>
						<th style="width: 212px;">Informations</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?php echo $form['motif_suppression']->renderLabel() ?>
						</td>
						<td>
							<span><?php echo $form['motif_suppression']->renderError() ?></span>
							<?php echo $form['motif_suppression']->render(array('rows' => 6, 'cols' => 60)) ?>	
						</td>
					</tr>			
				</tbody>
			</table>		
		</div>
		
		<table id="actions_fiche">
			<tr>
				<td style="width: 33%">&nbsp;</td>
				<td align="center"></td>
				<td style="width: 33%; text-align: right;">
					<input type="image" src="/images/boutons/btn_supprimer_contrat_rouge.png" alt="Supprimer le contrat" />
				</td>
			</tr>
		</table>
	</form>
	
</div>