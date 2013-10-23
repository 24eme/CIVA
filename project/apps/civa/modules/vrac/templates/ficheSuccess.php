<?php if ($vrac->isSupprimable($user->_id)): ?>
	<div class="btn_header">
		<a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
			<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_supprimer_contrat.png">
		</a>
	</div>
<?php endif; ?>
<div id="contrat_onglet">
<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected">
		<a href="#" style="height: 18px;">
		<?php if ($vrac->isValide() || $vrac->isAnnule()): ?>
			Contrat en vrac<?php if ($vrac->numero_archive): ?> numéro <?php echo $vrac->numero_archive ?><?php endif; ?>
		<?php else: ?>
			Validation de votre contrat en vrac
		<?php endif; ?>
		</a>
		<?php if ($vrac->isValide() || $vrac->isAnnule()): ?>
			<span class="statut"><?php echo VracClient::getInstance()->getStatutLibelle($vrac->valide->statut) ?></span>
		<?php endif; ?>
	</li>
</ul>
</div>
<div id="contrats_vrac" class="fiche_contrat">
	<?php if ($form): ?>
	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_fiche', array('sf_subject' => $vrac)) ?>">
		<?php echo $form->renderHiddenFields() ?>
		<?php echo $form->renderGlobalErrors() ?>
	<?php endif; ?>
	<div class="fond">		
		<?php use_helper('Date') ?>
		
		<?php if (!$vrac->isValide() && !$vrac->hasValide($user->_id)): ?>
		<fieldset class="message">
		    <legend class="message_title">Points de vigilance <a href="#" class="msg_aide_ds" rel="help_popup_validation_log_vigilance_ds" title="Message aide"></a></legend>
		     <ul class="messages_log">
		        <li>
	                En cas d'erreur sur le contrat, veuillez contacter votre interlocuteur commercial.
		        </li>
			</ul>
		</fieldset>
		<?php endif; ?>
		
		<?php include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => true)) ?>

		<?php 
			if($validation->hasPoints()) {
				include_partial('global/validation', array('validation' => $validation)); 
			}
		?>
		
		<?php include_partial('vrac/produits', array('vrac' => $vrac, 'form' => $form)) ?>
		
		
	</div>
	
	<table id="actions_fiche">
		<tr>
			<td><input type="image" src="/images/boutons/btn_previsualiser.png" alt="Prévisualiser" name="boutons[previsualiser]" id="previsualiserContrat"></td>
			<td>
				<?php if (!$vrac->isValide()): ?>
					<?php if ($vrac->hasValide($user->_id)): ?>
						<p>Vous avez validé le contrat le <strong><?php echo format_date($vrac->getUserDateValidation($user->_id), 'p', 'fr') ?></strong></p>
					<?php else: ?>
						<a href="<?php echo url_for('vrac_validation', array('sf_subject' => $vrac)) ?>" id="signatureVrac">
							<img alt="Valider le contrat" src="/images/boutons/btn_signer.png">
						</a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ($form): ?>
					<input type="image" src="/images/boutons/btn_valider_final.png" alt="Valider vos enlèvements" />
				<?php endif; ?>
				<?php if($vrac->isCloture()): ?>
					<p>Contrat en vrac numéro <?php echo $vrac->numero_archive ?> cloturé le <strong><?php echo format_date($vrac->valide->date_cloture, 'p', 'fr') ?></strong></p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php include_partial('popupConfirmeSignature'); ?>
	<?php if ($form): ?>
	</form>
	<?php endif; ?>
	<?php include_partial('vrac/generationPdf', array('vrac' => $vrac)); ?>
</div>