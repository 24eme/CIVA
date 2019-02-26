<?php

abstract class DeclarationSecurityUser extends TiersSecurityUser
{
    const CREDENTIAL_DECLARATION_EN_COURS = 'declaration_en_cours';
    const CREDENTIAL_DECLARATION_VALIDE = 'declaration_valide';
    const CREDENTIAL_ETAPE_EXPLOITATION = 'declaration_etape_exploitation';
    const CREDENTIAL_ETAPE_REPARTITION_RECOLTE = 'declaration_etape_repartition_recolte';
    const CREDENTIAL_ETAPE_RECOLTE = 'declaration_etape_recolte';
    const CREDENTIAL_ETAPE_VALIDATION = 'declaration_etape_validation';

    protected $_etapes_credentials = array(DR::ETAPE_EXPLOITATION => self::CREDENTIAL_ETAPE_EXPLOITATION,
                                           DR::ETAPE_REPARTITION => self::CREDENTIAL_ETAPE_REPARTITION_RECOLTE,
                                           DR::ETAPE_RECOLTE => self::CREDENTIAL_ETAPE_RECOLTE,
                                           DR::ETAPE_VALIDATION => self::CREDENTIAL_ETAPE_VALIDATION);
    protected $_credentials_declaration = array(
        self::CREDENTIAL_DECLARATION_EN_COURS,
        self::CREDENTIAL_DECLARATION_VALIDE,
        self::CREDENTIAL_ETAPE_EXPLOITATION,
        self::CREDENTIAL_ETAPE_REPARTITION_RECOLTE,
        self::CREDENTIAL_ETAPE_RECOLTE,
        self::CREDENTIAL_ETAPE_VALIDATION);
    protected $_declaration = null;
    protected $_ds = array();

    /**
     *
     * @param sfEventDispatcher $dispatcher
     * @param sfStorage $storage
     * @param type $options
     */
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
    {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated()) {
            $this->signOutDeclaration();
        }
    }

    /**
     *
     */
    protected function clearCredentialsDeclaration()
    {
        foreach ($this->_credentials_declaration as $credential) {
            $this->removeCredential($credential);
        }
    }

    /**
     *
     */
    public function signOutDeclaration()
    {
        $this->_declaration = null;
        $this->_ds = array();
        $this->clearCredentialsDeclaration();
    }

    /**
     * @return DR
     */
    public function getDeclaration()
    {
        $this->requireDeclaration();
        $this->requireTiers();
        if (is_null($this->_declaration)) {
            if(!$this->getDeclarant()) {
                return null;
            }
            $this->_declaration = acCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($this->getDeclarant()->getIdentifiant(), $this->getCampagne());
            if (!$this->_declaration) {
                $declaration = new DR();
                $declaration->set('_id', 'DR-' . $this->getDeclarant()->cvi . '-' . $this->getCampagne());
                return $declaration;
            }
        }

        return $this->_declaration;
    }

    /**
     * @return string
     */
    public function getCampagne()
    {
        return CurrentClient::getCurrent()->campagne;
    }

    /**
     * @return string
     */
    public function getCampagneDS($type_ds = null)
    {
        return (int) (preg_replace("/^([0-9]{4})[0-9]{2}$/", '\1', $this->getPeriodeDS($type_ds)) - 1);
    }

    public function getPeriodeDS($type_ds = null){
        $declarant = $this->getDeclarantDS($type_ds);

        if(CurrentClient::getCurrent()->isDSDecembre() && $declarant && $declarant->exist('ds_decembre') && $declarant->ds_decembre) {

            return CurrentClient::getCurrent()->getPeriodeDS();
        }

        if(CurrentClient::getCurrent()->isDSDecembre()) {

            return CurrentClient::getCurrent()->getAnneeDS()."07";
        }

        return CurrentClient::getCurrent()->getPeriodeDS();
    }

    /**
     * @return string
     */
    public function getMonthDS($type_ds = null)
    {
        return substr($this->getPeriodeDS($type_ds), 4, 2);
    }

    /**
     * @return string
     */
    public function getAnneeDS($type_ds = null)
    {
        return substr($this->getPeriodeDS($type_ds), 0, 4);
    }

    /**
     *
     * @param string $etape
     */
    public function addEtapeDeclaration($etape)
    {
        $this->requireDeclaration();
        if ($etape == 'recolte') {

        }
        if ($this->getDeclaration()->addEtape($etape)) {
            $this->getDeclaration()->save();
            $this->addCredentialsEtapeDeclaration();
        }
    }

    /**
     *
     */
    protected function addCredentialsEtapeDeclaration()
    {
        $declaration = $this->getDeclaration();
        if ($declaration->exist('etape') && $declaration->etape) {
            $this->addCredential($this->_etapes_credentials[$declaration->etape]);
            foreach (DR::$_etapes_inclusion[$declaration->etape] as $etape) {
                $this->addCredential($this->_etapes_credentials[$etape]);
            }
        }
    }

    /**
     * returns trus if editable
     */
    public function isDrEditable()
    {
        if ($this->hasCredential(self::CREDENTIAL_OPERATEUR)) {
            return true;
        }

        return DRClient::getInstance()->isTeledeclarationOuverte();
    }

    /**
     * returns trus if validate
     */
    public function isDrValidee()
    {
        $declaration = $this->getDeclaration();

        if ($this->hasCredential(self::CREDENTIAL_ADMIN)) {
            return ($declaration->isValideeCiva());
        }

        return ($declaration->isValideeTiers() || $declaration->isValideeCiva());
    }

    /**
     *
     */
    public function initCredentialsDeclaration()
    {
        $this->requireDeclaration();
        $declaration = $this->getDeclaration();

        if(!$declaration) {

            return null;
        }

        $this->clearCredentialsDeclaration();
        if ($this->isDrEditable()) {
            if ($this->isDrValidee()) {
                $this->addCredential(self::CREDENTIAL_DECLARATION_VALIDE);
            } else {
                $this->addCredential(self::CREDENTIAL_DECLARATION_EN_COURS);
                $this->addCredentialsEtapeDeclaration();
            }
        }
    }

    /**
     *
     */
    protected function requireDeclaration()
    {
        $this->requireTiers();
        if (!$this->hasCredential(self::CREDENTIAL_DECLARATION)) {
            throw new sfException("you must be logged in with a tiers");
        }
    }


    /**
     * DS
     */

    public function isDeclarantDSDecembre($type_ds = null) {

        $declarant = $this->getDeclarantDS($type_ds);

        return $declarant && $declarant->exist('ds_decembre') && $declarant->ds_decembre;
    }

    public function isDsEditable($type_ds = null)
    {

        return DSCivaClient::getInstance()->isTeledeclarationOuverte();
    }

    public function isDsTerminee($type_ds = null)
    {

        if(CurrentClient::getCurrent()->isDSDecembre() && !$this->isDeclarantDSDecembre($type_ds)) {

            return true;
        }

        return DSCivaClient::getInstance()->getDateFermeture()->format('Y-m-d') > date('Y-m-d');
    }

    public function isDsNonOuverte($type_ds = null)
    {
        if(CurrentClient::getCurrent()->isDSDecembre() && !$this->isDeclarantDSDecembre($type_ds)) {

            return true;
        }

        return CurrentClient::getCurrent()->ds_non_ouverte == 1;
    }

    public function getDs($type_ds)
    {
        $declarant = $this->getDeclarantDS($type_ds);
        if(!$declarant->getFamille() == EtablissementFamilles::FAMILLE_PRODUCTEUR) {
            throw new sfException("Vous n'avez pas les droits pour créez une DS");
        }

        if (!$declarant->hasLieuxStockage() && !$declarant->isAjoutLieuxDeStockage()) {
            return null;
        }

        $this->requireTiers();
        if (!isset($this->_ds[$type_ds])) {
            $this->_ds[$type_ds] = DSCivaClient::getInstance()->findPrincipaleByEtablissementAndPeriode($type_ds, $declarant, $this->getPeriodeDS($type_ds));
        }

        return $this->_ds[$type_ds];
    }

    public function removeDs($type_ds = null)
    {
        $dss = DSCivaClient::getInstance()->findDssByDS($this->getDs($type_ds));
        foreach ($dss as $ds) {
            $ds->delete();
        }
        $this->signOutDeclaration();
    }

    public function signInTiers($tiers)
    {
        parent::signInTiers($tiers);
    }

    /**
     *
     * @param string $namespace
     */
    public function signOutCompte($namespace = self::NAMESPACE_COMPTE_USED)
    {
        $this->signOutDeclaration();
        parent::signOutCompte($namespace);
    }

    /**
     *
     */
    public function signOutTiers()
    {
        $this->signOutDeclaration();
        parent::signOutTiers();
    }

    public function removeDeclaration()
    {
        $this->getDeclaration()->delete();
        $this->signOutDeclaration();
        $this->initCredentialsDeclaration();
    }

}
