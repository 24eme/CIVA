<?php

abstract class TiersSecurityUser extends CompteSecurityUser {

    protected $_tiers = null;
    const SESSION_TIERS = 'tiers';
    const NAMESPACE_TIERS = 'TiersSecurityUser';
    const CREDENTIAL_TIERS = 'tiers';
    const CREDENTIAL_RECOLTANT = 'recoltant';
    const CREDENTIAL_DECLARATION = 'declaration';
    const CREDENTIAL_METTEUR_EN_MARCHE = 'metteur_en_marche';
    const CREDENTIAL_GAMMA = 'gamma';
    const CREDENTIAL_ACHETEUR = 'acheteur';




    protected $_credentials_tiers = array(self::CREDENTIAL_TIERS,
        self::CREDENTIAL_RECOLTANT,
        self::CREDENTIAL_DECLARATION,
        self::CREDENTIAL_METTEUR_EN_MARCHE,
        self::CREDENTIAL_GAMMA,
        self::CREDENTIAL_ACHETEUR);

    /**
     *
     * @param sfEventDispatcher $dispatcher
     * @param sfStorage $storage
     * @param type $options
     */
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated()) {
            $this->signOutTiers();
        }
    }

    public function signInTiers($societe) {
        $this->requireCompte();
        $this->signOutTiers();
        $this->addCredential(self::CREDENTIAL_TIERS);
        $this->addCredential(self::CREDENTIAL_DECLARATION);

        $etablissements = array();
        $etablissementsObject = $societe->getEtablissementsObject(true, true);
        if (count($etablissementsObject) >= 1) {
    	    foreach ($etablissementsObject as $e) {
                if (isset($etablissements[$e->getFamille()])) {
                  continue;
                }
                $etablissements[$e->famille] = $e;
            }
        }

        $tiers = array_values($etablissements);

        if (!is_array($tiers))
            $tiers = array($tiers);
        foreach ($tiers as $t) {
            if ($t->type == "Recoltant") {
                $this->addCredential(self::CREDENTIAL_RECOLTANT);
                $this->addCredential(self::CREDENTIAL_DECLARATION);
            } elseif ($t->type == "MetteurEnMarche") {
                $this->addCredential(self::CREDENTIAL_METTEUR_EN_MARCHE);
                if ($t->no_accises) {
                    $this->addCredential(self::CREDENTIAL_GAMMA);
                }
            } elseif ($t->type == "Acheteur") {
                $this->addCredential(self::CREDENTIAL_ACHETEUR);
            }
            $ids[] = $t->_id;
        }
        $this->setAttribute(self::SESSION_TIERS, join(',', $ids), self::NAMESPACE_TIERS);

    }

    /**
     *
     */
    protected function clearCredentialsTiers() {
        foreach ($this->_credentials_tiers as $credential) {
            $this->removeCredential($credential);
        }
    }

    /**
     *
     */
    public function signOutTiers() {
        $this->_tiers = null;
        $this->getAttributeHolder()->removeNamespace(self::NAMESPACE_TIERS);
        $this->clearCredentialsTiers();
    }

    public function getTiers($type = null) {

        if (is_null($this->_tiers)) {
            $this->_tiers = array();
            if ($this->getAttribute(self::SESSION_TIERS, null, self::NAMESPACE_TIERS)) {
                foreach (explode(',', $this->getAttribute(self::SESSION_TIERS, null, self::NAMESPACE_TIERS)) as $id) {
                    $t = acCouchdbManager::getClient()->find($id);
                    if (isset($this->_tiers[$t->famille]))
                        throw new sfException('An user cannot have more than two tiers of the same type');
                    $this->_tiers[$t->famille] = $t;
                }
            } else {
            $this->_tiers = $this->getCompte()->getSociete()->getEtablissementsObject(true, true);
            }
            if (!$this->_tiers) {
                $this->signOutCompte();
                throw new sfException("The tiers does not exist");
            }
        }

        if (!$type) {
            if (array_key_exists(EtablissementFamilles::FAMILLE_PRODUCTEUR, $this->_tiers)) {
                $type = EtablissementFamilles::FAMILLE_PRODUCTEUR;
            } elseif (array_key_exists('Acheteur', $this->_tiers)) {
                $type = 'Acheteur';
            } elseif (array_key_exists('Courtier', $this->_tiers)) {
                $type = 'Courtier';
            } else {
                $type = EtablissementFamilles::FAMILLE_PRODUCTEUR;
            }
        }

        if (!isset($this->_tiers[$type])) {
            //throw new sfException('no tiers for type "' . $type . '"');
        }

        return $this->getDeclarant();
    }

    public function getDeclarant() {

        return DRClient::getInstance()->getEtablissement($this->getCompte()->getSociete());
    }

    public function getDeclarantDRAcheteur() {

        return DRClient::getInstance()->getEtablissementAcheteur($this->getCompte()->getSociete());
    }

    public function getDeclarantDS($type_ds = null) {

        return DSCivaClient::getInstance()->getEtablissement($this->getCompte()->getSociete(), $type_ds);
    }

    public function getDeclarantVrac() {
        $declarants = $this->getDeclarantsVrac();

        return current($declarants);
    }

    public function getDeclarantsVrac() {

        return VracClient::getInstance()->getEtablissements($this->getCompte()->getSociete());
    }

    /**
     *
     */
    protected function requireTiers() {
        $this->requireCompte();
        if (!$this->hasCredential(self::CREDENTIAL_TIERS)) {
            throw new sfException("you must be logged in with a tiers");
        }
    }

    /**
     *
     * @param string $namespace
     */
    public function signOutCompte($namespace = self::NAMESPACE_COMPTE_USED) {
        $this->signOutTiers();
        parent::signOutCompte($namespace);
    }

}
