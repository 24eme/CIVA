<?php

class VracCsvExport
{

    public static function header() {
        $f = fopen('php://memory', 'r+');
        fputcsv($f, VracCsvImport::$headers, ';');
        rewind($f);
        return "\xef\xbb\xbf".stream_get_contents($f);
    }

    public static function contrat(Vrac $contrat) {
        $f = fopen('php://memory', 'r+');
        foreach($contrat->declaration->getProduitsDetails() as $produit) {
            $fields = [
             "CONTRAT",
             $contrat->campagne,
             $contrat->getTemporaliteContrat(),
             $contrat->getDureeAnnee(),
             $contrat->numero_contrat,
             $contrat->exist('reference_contrat_pluriannuel') ? str_replace('VRAC-', '', $contrat->reference_contrat_pluriannuel) : null,
             $contrat->type_contrat,
             ($contrat->acheteur->cvi) ? $contrat->acheteur->cvi : $contrat->acheteur->civaba,
             ($contrat->acheteur->intitule) ? $contrat->acheteur->intitule . ' ' .$contrat->acheteur->raison_sociale : $contrat->acheteur->raison_sociale,
             $contrat->acheteur_assujetti_tva,
            ($contrat->vendeur->cvi) ? $contrat->vendeur->cvi : $contrat->vendeur->civaba,
             ($contrat->vendeur->intitule) ? $contrat->vendeur->intitule . ' ' .$contrat->vendeur->raison_sociale : $contrat->vendeur->raison_sociale,
             $contrat->vendeur_assujetti_tva,
             $contrat->mandataire->siret,
             ($contrat->mandataire->intitule) ? $contrat->mandataire->intitule . ' ' .$contrat->mandataire->raison_sociale : $contrat->mandataire->raison_sociale,
             $produit->getConfig()->getCertification()->getLibelle(),
             $produit->getConfig()->getGenre()->getLibelle(),
             str_replace($produit->getConfig()->getCertification()->getLibelle().' ', '', $produit->getConfig()->getAppellation()->getLibelle()),
             $produit->getConfig()->getMention()->getLibelle(),
             $produit->lieu_dit ? $produit->lieu_dit : $produit->getConfig()->getLieu()->getLibelle(),
             $produit->getConfig()->getCouleur()->getLibelle(),
             $produit->getConfig()->getCepage()->getLibelle(),
             $produit->getConfig()->getCodeDouane(),
             $produit->getLibelle(),
             $produit->getLabel(),
             $produit->vtsgn,
             $produit->denomination,
             $produit->millesime,
             $produit->getQuantitePropose(),
             $produit->getQuantiteType(),
             $produit->prix_unitaire,
             $contrat->getPrixUniteLibelle(),
             $contrat->exist('vendeur_frais_annexes') ? $contrat->vendeur_frais_annexes : null,
             $contrat->exist('acheteur_primes_diverses') ? $contrat->acheteur_primes_diverses : null,
             $contrat->exist('clause_reserve_propriete') ? $contrat->clause_reserve_propriete : null,
             $contrat->conditions_paiement,
             $contrat->exist('clause_resiliation') ? $contrat->clause_resiliation : null,
             $contrat->exist('clause_mandat_facturation') ? $contrat->clause_mandat_facturation : null,
             $contrat->exist('clause_evolution_prix') ? $contrat->clause_evolution_prix : null,
             $contrat->exist('clause_renegociation_prix') ? $contrat->clause_renegociation_prix : null,
             $contrat->exist('suivi_qualitatif') ? $contrat->suivi_qualitatif : null,
             $contrat->exist('delais_retiraison') ? $contrat->delais_retiraison : null,
             $contrat->conditions_particulieres,
             strtoupper($contrat->getCreateurInformations()->getKey()),
             $contrat->valide->date_saisie,
             $contrat->valide->date_validation_vendeur,
             $contrat->valide->date_validation_acheteur,
             $contrat->valide->date_validation_mandataire,
             $contrat->valide->date_validation,
             $contrat->valide->date_cloture,
             $contrat->numero_visa,
             $contrat->valide->statut,
             ($produit->exist('centilisation'))? VracClient::getLibelleCentilisation($produit->centilisation) : null,
             $produit->getQuantiteEnleve(),
             (count($produit->retiraisons) > 0) ? $produit->retiraisons[0]->date : null,
             $contrat->_id,
            ];
            fputcsv($f, $fields, ';');
        }
        rewind($f);
        return stream_get_contents($f);
    }

}
