<?php use_helper('Float'); ?>
<?php printf("\xef\xbb\xbf");//UTF8 BOM (pour windows) ?>
<?php echo "campagne;identifiant interne;date saisie;numero visa;statut;vendeur cvi/civa;vendeur raison sociale;vendeur date signature;acheteur cvi/civa;acheteur raison sociale;acheteur date signature;courtier siret;courtier raison sociale;courtier date signature;produit;denomination;millesime;prix unitaire;volume estime;volume reel;date_enlevement;\n"; ?>
<?php foreach($vracs as $item): ?>
<?php $vrac = acCouchdbManager::getClient()->find($item->id) ?>
<?php foreach($vrac->declaration->getProduitsDetails() as $produit): ?>
<?php echo sprintf("\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";\"%s\";%s;%s;%s;\"%s\";\n", 
                       $vrac->campagne,
                       $vrac->_id,
                       $vrac->valide->date_saisie,
                       $vrac->numero_visa,
                       VracClient::getInstance()->getStatutLibelle($vrac->valide->statut),
                       ($vrac->vendeur->cvi) ? $vrac->vendeur->cvi : $vrac->vendeur->civaba,
                       ($vrac->vendeur->intitule) ? $vrac->vendeur->intitule . ' ' .$vrac->vendeur->raison_sociale : $vrac->vendeur->raison_sociale,
                       $vrac->valide->date_validation_vendeur,
                       ($vrac->acheteur->cvi) ? $vrac->acheteur->cvi : $vrac->acheteur->civaba,
                       ($vrac->acheteur->intitule) ? $vrac->acheteur->intitule . ' ' .$vrac->acheteur->raison_sociale : $vrac->acheteur->raison_sociale,
                       $vrac->valide->date_validation_acheteur,
                       $vrac->mandataire->siret,
                       ($vrac->mandataire->intitule) ? $vrac->mandataire->intitule . ' ' .$vrac->mandataire->raison_sociale : $vrac->mandataire->raison_sociale,
                       $vrac->valide->date_validation_mandataire,
                       $produit->getLibellePartiel(),
                       $produit->denomination,
                       $produit->millesime,
                       sprintFloat($produit->prix_unitaire),
                       sprintFloat($produit->volume_propose),
                       sprintFloat($produit->volume_enleve),
                       (count($produit->retiraisons) > 0) ? $produit->retiraisons[0]->date : null
                       ); ?>
<?php endforeach; ?>
<?php endforeach; ?>