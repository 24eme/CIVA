<?php

class _CompteClient extends acCouchdbClient {

    const DROIT_DR_RECOLTANT = "DR_RECOLTANT";
    const DROIT_DR_ACHETEUR = "DR_ACHETEUR";
    const DROIT_VRAC_RESPONSABLE = "VRAC_RESPONSABLE";
    const DROIT_VRAC_SIGNATURE = "VRAC_SIGNATURE";
    const DROIT_DS_DECLARANT = "DS_DECLARANT";
    const DROIT_GAMMA = "GAMMA";
    const DROIT_ADMIN = "admin";
    const DROIT_OPERATEUR = "operateur";

    public static $droits = array(
        self::DROIT_DR_RECOLTANT => "DR Récoltant",
        self::DROIT_DR_ACHETEUR => "DR Acheteur",
        self::DROIT_VRAC_RESPONSABLE => "Contrat responsable",
        self::DROIT_VRAC_SIGNATURE => "Contrat signataire",
        self::DROIT_DS_DECLARANT => "DS Déclarant",
        self::DROIT_GAMMA => "Gamma",
    );

    public static $droits_type = array(
        'Recoltant' => array(self::DROIT_DR_RECOLTANT, self::DROIT_VRAC_SIGNATURE, self::DROIT_DS_DECLARANT),
        'MetteurEnMarche' => array(self::DROIT_GAMMA, self::DROIT_VRAC_SIGNATURE, self::DROIT_VRAC_RESPONSABLE, self::DROIT_DS_DECLARANT),
        'Acheteur' => array(self::DROIT_DR_ACHETEUR, self::DROIT_VRAC_SIGNATURE, self::DROIT_VRAC_RESPONSABLE, self::DROIT_DS_DECLARANT),
        'Courtier' => array(self::DROIT_VRAC_SIGNATURE, self::DROIT_VRAC_RESPONSABLE),
    );
    
    public static function getInstance() {
    
        return acCouchdbManager::getClient('_Compte'); 
    }

    /**
     *
     * @param string $login
     * @param integer $hydrate
     * @return Compte 
     */
    public function retrieveByLogin($login, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->findByLogin($login, $hydrate);
    }

    public function findByLogin($login, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::find('COMPTE-'.$login, $hydrate);
    }

    public function findByLoginMagic($login, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $compte = $this->findByLogin($login, $hydrate);

        if($compte) {

            return $compte;
        }

        $met = _TiersClient::getInstance()->findByCivaba(str_replace('C', '', $login));

        if($met) {

            return $met->getCompteObject();
        }

        return null;
    }

    public function getDroits() {

        return self::$droits;
    }

    public function getDroitsType($type) {

        return self::$droits_type[$type];
    }

    public function getDroitLibelle($key) {

        return self::$droits[$key];
    }
    
    /**
     *
     * @param integer $hydrate
     * @return acCouchdbDocumentCollection 
     */
    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('COMPTE-')->endkey('COMPTE-C999999999')->execute($hydrate);
    }

    public function getComptesPersonnes($compte, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->startkey($compte->_id)->endkey($compte->_id.'99')->execute($hydrate);
    }

    public function generateComptePersonne($compte) {
        
        $personne = new CompteTiers();
        $personne->login = sprintf("%s%02d", $compte->login, $this->getComptePersonneDernierNumero($compte) + 1);
        $personne->add("id_compte_societe", $compte->_id);
        $personne->constructId();
        return $personne;
    }

    public function getComptePersonneDernierNumero($compte) {
        $ids = $this->getComptesPersonnes($compte, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $last_numero = 0;
        foreach($ids as $id) {
            $c = _CompteClient::getInstance()->find($id);
            if($c->isCompteSociete()) {
                $numero = 0;
            } else {
                $numero = (int)substr($c->login, strlen($c->login)-2, 2);
            }
            if($last_numero < $numero) {
                $last_numero = $numero;
            }
        }

        return $last_numero;
    }
}
