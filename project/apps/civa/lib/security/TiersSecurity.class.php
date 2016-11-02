<?php

class TiersSecurity implements SecurityInterface {

    const DR = 'DR';
    const DR_ACHETEUR = 'DR_ACHETEUR';
    const DS_PROPRIETE = 'DS_PROPRIETE';
    const DS_NEGOCE = 'DS_NEGOCE';
    const GAMMA = 'GAMMA';
    const VRAC = 'VRAC';

    protected $compte;

    public static function getInstance($compte) {

        return new TiersSecurity($compte);
    }

    public function __construct($compte) {
        $this->compte = $compte;
        if(!$this->compte) {

            throw new sfException("Le compte est nul");
        }
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        if(in_array(self::DR, $droits)) {

            return DRSecurity::getInstance(DRClient::getInstance()->getEtablissement($this->compte->getSociete()))->isAuthorized(DRSecurity::DECLARANT);
        }

        if(in_array(self::DR_ACHETEUR, $droits)) {

            return DRAcheteurSecurity::getInstance($this->compte)->isAuthorized(DRAcheteurSecurity::DECLARANT);
        }

        if(in_array(self::DS_PROPRIETE, $droits)) {

            return DSSecurity::getInstance(DSCivaClient::getInstance()->getEtablissement($this->compte->getSociete(), DSCivaClient::TYPE_DS_PROPRIETE), null, DSCivaClient::TYPE_DS_PROPRIETE)->isAuthorized(DSSecurity::DECLARANT);
        }

        if(in_array(self::DS_NEGOCE, $droits)) {

            return DSSecurity::getInstance(DSCivaClient::getInstance()->getEtablissement($this->compte->getSociete(), DSCivaClient::TYPE_DS_NEGOCE), null, DSCivaClient::TYPE_DS_NEGOCE)->isAuthorized(DSSecurity::DECLARANT);
        }

        if(in_array(self::VRAC, $droits)) {

            $isDeclarant = VracSecurity::getInstance($this->compte)->isAuthorized(VracSecurity::DECLARANT);

            if(!$isDeclarant) {

                return false;
            }

            if(VracSecurity::getInstance($this->compte, null)->isAuthorized(VracSecurity::CREATION)) {

                return true;
            }

            $tiersVrac = VracClient::getInstance()->getEtablissements($this->compte->getSociete());

            if($tiersVrac instanceof sfOutputEscaperArrayDecorator) {
                $tiersVrac = $tiersVrac->getRawValue();
            }

            if(!count(VracTousView::getInstance()->findSortedByDeclarants($tiersVrac))) {

                return false;
            }

            return true;
        }

        if(in_array(self::GAMMA, $droits)) {

            return GammaSecurity::getInstance(GammaClient::getInstance()->getEtablissement($this->compte))->isAuthorized(GammaSecurity::DECLARANT);
        }

        return false;
    }

    public function getBlocs() {
        $blocs = array();
        foreach($this->getDroitUrls() as $droit => $url) {
            $blocs[$droit] = $url;
        }

        return $blocs;
    }

    public function getDroitUrls() {
        $droits = array();
        if ($this->isAuthorized(TiersSecurity::DR)) {
            $droits[TiersSecurity::DR] = array('mon_espace_civa_dr', array('identifiant' => DRClient::getInstance()->getEtablissement($this->compte->getSociete())->getIdentifiant()));
        }

        if ($this->isAuthorized(TiersSecurity::DR_ACHETEUR )) {
            $droits[TiersSecurity::DR_ACHETEUR] = 'mon_espace_civa_dr_acheteur';
        }

        if ($this->isAuthorized(TiersSecurity::VRAC )) {
            $droits[TiersSecurity::VRAC] = array('mon_espace_civa_vrac', array('identifiant' => $this->compte->getIdentifiant()));
        }

        if ($this->isAuthorized(TiersSecurity::GAMMA )) {
            $droits[TiersSecurity::GAMMA] = array('mon_espace_civa_gamma', array('identifiant' => $this->compte->getIdentifiant()));
        }

        if ($this->isAuthorized(TiersSecurity::DS_PROPRIETE )) {
            $droits[TiersSecurity::DS_PROPRIETE] = array('mon_espace_civa_ds', array('identifiant' => DSCivaClient::getInstance()->getEtablissement($this->compte->getSociete(), DSCivaClient::TYPE_DS_PROPRIETE)->getIdentifiant(), 'type' => DSCivaClient::TYPE_DS_PROPRIETE));
        }

        if ($this->isAuthorized(TiersSecurity::DS_NEGOCE )) {
            $droits[TiersSecurity::DS_NEGOCE] = array('mon_espace_civa_ds', array('identifiant' => DSCivaClient::getInstance()->getEtablissement($this->compte->getSociete(), DSCivaClient::TYPE_DS_NEGOCE)->getIdentifiant(), 'type' => DSCivaClient::TYPE_DS_PROPRIETE));
        }

        return $droits;
    }

}
