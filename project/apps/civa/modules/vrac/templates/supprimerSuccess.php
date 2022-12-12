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
                    <button class="btn_majeur btn_rouge btn_grand" type="submit">
                        Supprimer
                        <svg style="color:white;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash" viewBox="0 -1 16 16">
                          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                          <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </button>
				</td>
			</tr>
		</table>
	</form>

</div>
