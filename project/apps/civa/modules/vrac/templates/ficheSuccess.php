<?php if ($vrac->isValide()): ?>
<h1>Contrat en vrac numéro <?php echo $vrac->numero_archive ?><span><?php echo VracClient::getInstance()->getStatutLibelle($vrac->valide->statut) ?></span></h1>
<?php else: ?>
<h1>Validation de votre contrat en vrac</h1>
<?php endif; ?>

<?php if ($vrac->isSupprimable($user->_id)): ?>
<a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
	<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_annuler_ajout.png">
</a>
<?php endif; ?>

<?php include_partial('vrac/soussignes', array('vrac' => $vrac)) ?>

<?php include_partial('vrac/produits', array('vrac' => $vrac, 'form' => $form)) ?>

<?php if (!$vrac->isValide()): ?>
	<?php if ($vrac->hasValide($user->_id)): ?>
		<p>Vous avez validé le contrat le <strong><?php echo $vrac->getUserDateValidation($user->_id) ?></strong></p>
	<?php else: ?>
		<a href="<?php echo url_for('vrac_fiche', array('sf_subject' => $vrac, 'validation' => 'validation')) ?>">
			<img alt="Valider le contrat" src="/images/boutons/btn_valider_final.png">
		</a>
		<p>En cas d'erreur sur le contrat, veuillez contacter votre interlocuteur commercial.</p>
	<?php endif; ?>
<?php endif; ?>