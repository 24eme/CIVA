<?php echo $vrac->getTypeDocumentLibelle() ?> <?php echo strtolower($vrac->getTypeDureeLibelle()) ?> <?php echo strtolower($vrac->type_contrat)?> du <?php echo strftime('%d/%m/%Y', strtotime($vrac->valide->date_saisie)) ?>

<?php if($vrac->isPluriannuelCadre()): ?>Campagnes d'application : <?php echo VracSoussignesForm::getCampagnesChoices()[$vrac->campagne]; ?><?php endif; ?>
<?php if($vrac->isApplicationPluriannuel()): ?>Contrat d'application : <?php echo $vrac->campagne; ?><?php endif; ?>

Vendeur : <?php echo ($vrac->vendeur->intitule)? $vrac->vendeur->intitule.' '.$vrac->vendeur->raison_sociale : $vrac->vendeur->raison_sociale ?>

Acheteur : <?php echo ($vrac->acheteur->intitule)? $vrac->acheteur->intitule.' '.$vrac->acheteur->raison_sociale : $vrac->acheteur->raison_sociale ?><?php if (!$vrac->hasCourtier() && $vrac->interlocuteur_commercial->nom): ?> (votre interlocuteur : <?php echo $vrac->interlocuteur_commercial->nom ?>)<?php endif; ?>

<?php if ($vrac->hasCourtier()): ?>
Courtier : <?php echo ($vrac->mandataire->intitule)? $vrac->mandataire->intitule.' '.$vrac->mandataire->raison_sociale : $vrac->mandataire->raison_sociale ?><?php if ($vrac->interlocuteur_commercial->nom): ?> (votre interlocuteur : <?php echo $vrac->interlocuteur_commercial->nom ?>)<?php endif; ?>

<?php endif; ?>
