Madame, Monsieur,<br /><br />
L'entreprise <?php echo ($createur = $vrac->getCreateurInformations())? $createur->raison_sociale : ''; ?> a saisi un contrat de vente vous concernant.<br /><br />
Ce contrat a été saisi le <?php echo strftime('%d/%m/%Y', strtotime($vrac->valide->date_saisie)) ?> et concerne les parties suivantes :<br /><br />
<ul>
	<?php if ($vrac->vendeur_identifiant): ?>
	<li>Vendeur : <strong><?php echo $vrac->vendeur->raison_sociale ?></strong></li>
	<?php endif; ?>
	<?php if ($vrac->acheteur_identifiant): ?>
	<li>Acheteur : <strong><?php echo $vrac->acheteur->raison_sociale ?></strong></li>
	<?php endif; ?>
	<?php if ($vrac->mandataire_identifiant): ?>
	<li>Courtier : <strong><?php echo $vrac->mandataire->raison_sociale ?></strong></li>
	<?php endif; ?>
</ul>
<br />
<strong>Pour visualiser et valider ce contrat, cliquez sur le lien suivant : <a href="<?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>">Visualiser le contrat</a></strong><br /><br />
Le contrat ne sera valable que lorsque vous aurez reçu la version pdf faisant figurer le numéro de VISA du contrat.<br /><br />
Attention si le contrat n'est pas validé dans les 5 jours à compter de sa date de saisie, il sera automatiquement supprimé et non valable.<br />
Pour toute information, vous pouvez contacter votre interprofession ou votre interlocuteur commercial.<br /><br />
Cordialement,<br />
Le CIVA

