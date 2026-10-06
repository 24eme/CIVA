<?php

abstract class DeclarationSecurityUser extends TiersSecurityUser
{
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
    public function signOutDeclaration()
    {
        $this->_declaration = null;
        $this->_ds = array();
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
