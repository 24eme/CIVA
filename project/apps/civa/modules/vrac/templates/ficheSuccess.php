<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SUPPRESSION)): ?>
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
		<?php if ($vrac->isValide()): ?>
			Contrat <?php if ($vrac->numero_archive): ?> numéro de visa <?php echo $vrac->numero_archive ?><?php endif; ?>
		<?php else: ?>
			Validation de votre contrat
		<?php endif; ?>
		</a>
		<span class="statut"><?php echo VracClient::getInstance()->getStatutLibelle($vrac->valide->statut) ?></span>
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

		<?php if($sf_user->hasFlash('notice')) : ?>
			<p class="flash_message" style="margin-bottom: 20px;"><?php echo $sf_user->getFlash('notice'); ?></p>
		<?php endif; ?>

		<?php use_helper('Date') ?>

		<?php if (!$vrac->isValide() && !$vrac->hasValide($user->_id)): ?>
		<fieldset class="message">
		    <legend class="message_title">Points de vigilance <a href="#" class="msg_aide_ds" rel="help_popup_validation_log_vigilance_ds" title="Message aide"></a></legend>
		     <ul class="messages_log">
		        <li>
	                En cas d'erreur sur le contrat, veuillez contacter votre interlocuteur, le responsable du contrat.
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

		<?php include_partial('vrac/produits', array('vrac' => $vrac, 'form' => $form, 'produits_hash_in_error' => $validation->getProduitsHashInError())) ?>

		<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::FORCE_CLOTURE)): ?>
			<a style="float: right; bottom: 6px; color: #2A2A2A; text-decoration: none;" onclick="return confirm('Êtes vous sur de vouloir forcer la cloture de ce contrat ?');" class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('vrac_forcer_cloture', $vrac) ?>">Forcer la cloture</a>
		<?php endif; ?>

<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Conditions</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				Conditions de paiement
			</td>
			<td>
				<?php echo ($vrac->conditions_paiement)? $vrac->conditions_paiement : 'Aucunes'; ?>
			</td>
		</tr>
		<tr class="alt">
			<td>
				Conditions particulières
			</td>
			<td>
				<?php echo ($vrac->conditions_particulieres)? $vrac->conditions_particulieres : 'Aucunes'; ?>
			</td>
		</tr>

	</tbody>
</table>


	</div>

	<table id="actions_fiche">
		<tr>
			<td style="width: 40%"><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></td>
			<td align="center"><?php if ($vrac->isValide()): ?><input type="image" src="/images/boutons/btn_pdf_visualiser.png" alt="Visualiser" name="boutons[previsualiser]" id="previsualiserContrat"><?php endif; ?></td>
			<td style="width: 40%; text-align: right;">
				<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SIGNATURE)): ?>
					<a href="<?php echo url_for('vrac_validation', array('sf_subject' => $vrac)) ?>" id="signatureVrac">
						<img alt="Valider le contrat" src="/images/boutons/btn_signer.png">
					</a>
				<?php endif; ?>
				<?php if(!$vrac->isValide() && $vrac->hasValide($user->_id)): ?>
					<p>Vous avez signé le contrat le <strong><?php echo format_date($vrac->getUserDateValidation($user->_id), 'p', 'fr') ?></strong></p>
				<?php endif; ?>
				<?php if ($form): ?>
					<input type="image" src="/images/boutons/btn_valider_final.png" alt="Valider vos enlèvements" />
				<?php endif; ?>
				<?php if(!$form && $vrac->isCloture()): ?>
					<p>Contrat vrac numéro de visa <?php echo $vrac->numero_archive ?>, cloturé le <strong><?php echo format_date($vrac->valide->date_cloture, 'p', 'fr') ?></strong></p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php include_partial('popupConfirmeSignature'); ?>
	<?php include_partial('popupClotureContrat', array('vrac' => $vrac, 'validation' => $validation)); ?>
	<?php if ($form): ?>
	</form>
	<?php endif; ?>
	<?php include_partial('vrac/generationPdf', array('vrac' => $vrac)); ?>
</div>
<?php if (($vrac->valide->statut == Vrac::STATUT_ENLEVEMENT) && $vrac->allProduitsClotures() && !$validation->hasErreurs()): ?>
<script type="text/javascript">
$(document).ready(function()
{
	console.log("nop");
openPopup($("#popup_cloture_contrat"));
});
</script>
<?php endif; ?>
