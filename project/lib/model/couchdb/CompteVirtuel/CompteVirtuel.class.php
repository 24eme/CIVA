<?php
class CompteVirtuel extends BaseCompteVirtuel {
    public function getTiers() {
        if (in_array('metteur_en_marche', $this->droits->toArray())) {
            $tiers = new TiersFictif('MetteurEnMarche');
            $tiers->nom = $this->nom;
            $tiers->commune = $this->commune;
            $tiers->code_postal = $this->code_postal;
            $tiers->no_accises = '1';
            return array("MetteurEnMarche" => $tiers) ;
        }
        return false;
    }

    public function getStatutTeledeclarant() {

        return $this->getStatut();
    }

    public function getNomAAfficher() {

        return $this->getNom();
    }

    public function getNom() {
        return $this->_get('nom');
    }

    public function getNoAccises() {
        return '1';
    }

    public function getGecos() {
        return $this->getLogin() . ',' . $this->getNoAccises() . ',' . $this->getNom(). ',';
    }

    public function getSociete() {
	return null;
    }

    public function isSocieteContact() {

	return false;
    }

    public function getEtablissementOrigineObject() {
	
	return null;
    }

    public function getPrenom() {

	return null;
    }

    public function getAdresse() {

        return null;
    }
    
    public function getTelephoneBureau() {

        return null;
    }

    public function getTelephoneMobile() {

        return null;
    }

    public function getFax() {

        return null;
    }

    public function getIdentifiant() {

	return $this->login;
    }

    public function isActif() {
	
	return true;
    }

    public function updateLdap($verbose = 0) {
        $ldap = new CompteLdap();
        $ldap->saveCompte($this, $verbose);
    }

}
