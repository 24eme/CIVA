<?php

class GammaClient
{
    public static function getInstance() {

        return new GammaClient();
    }

    public function getEtablissement($compte) {
        $etablissement = $compte->getEtablissementOrigineObject();
        if($etablissement && $etablissement->no_accises) {

            return $etablissement;
        }

        $societe = $compte->getSociete();
        foreach($societe->getEtablissementsObject() as $etablissement) {
            if($compte->getIdentifiant() == $etablissement->cvi && $etablissement->hasDroit(Roles::TELEDECLARATION_GAMMA)) {

                return $etablissement;
            }
        }

        foreach($societe->getEtablissementsObject() as $etablissement) {
            if($etablissement->hasDroit(Roles::TELEDECLARATION_GAMMA)) {

                return $etablissement;
            }
        }

        return null;
    }

    public function findByCompte($compte) {
        $etablissement = $this->getEtablissement($compte);

        if(!$etablissement) {

            return null;
        }

        return $this->findByEtablissement($etablissement);
    }

    public function findByEtablissement($etablissement) {
        try {
            return acCouchdbManager::getClient()->getDoc("GAMMA-".$etablissement->getIdentifiant());
        } catch(couchException $e) {
        }

        return null;
    }

    public function createOrFind($etablissement, $compte) {
        $gamma = $this->findByEtablissement($etablissement);

        if(!$gamma) {
            $gamma = new stdClass();
            $gamma->_id = "GAMMA-".$etablissement->getIdentifiant();
        }

        $gamma->identifiant_inscription = $compte->identifiant;
        $gamma->no_accises = $etablissement->no_accises;

        return $gamma;
    }

    public function getGecos($compte, $etablissement = null) {
        if($etablissement === null) {
            $etablissement = GammaClient::getInstance()->getEtablissement($compte);
        }

        if(!$etablissement) {
            return sprintf("%s,%s,%s,%s", $compte->identifiant, null, ($compte->getNom()) ? $compte->getNom() : $compte->nom_a_afficher, $compte->nom_a_afficher);
        }

        $gamma = $this->findByEtablissement($etablissement);

        if(!$gamma) {

            return sprintf("%s,%s,%s,%s", $compte->identifiant, $etablissement->no_accises, ($compte->getNom()) ? $compte->getNom() : $compte->nom_a_afficher, $compte->nom_a_afficher);
        }

        return sprintf("%s,%s,%s,%s", $gamma->identifiant_inscription, $gamma->no_accises, ($compte->getNom()) ? $compte->getNom() : $compte->nom_a_afficher, $compte->nom_a_afficher);
    }

    public function storeDoc($doc) {

        return acCouchdbManager::getInstance()->getClient()->storeDoc($doc);
    }
}
