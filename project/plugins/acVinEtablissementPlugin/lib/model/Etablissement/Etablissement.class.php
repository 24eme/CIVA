<?php

class Etablissement extends BaseEtablissement implements InterfaceCompteGenerique {

    protected $_interpro = null;
    protected $droit = null;
    protected $_id_societe_origine = null;

    /**
     * @return _Compte
     */
    public function getInterproObject() {
        if (is_null($this->_interpro)) {
            $this->_interpro = InterproClient::getInstance()->find($this->interpro);
        }

        return $this->_interpro;
    }

    public function constructId() {
        $this->set('_id', 'ETABLISSEMENT-' . $this->identifiant);
        if ($this->isViticulteur()) {
            if($this->exist('raisins_mouts')) {
                $this->raisins_mouts = is_null($this->raisins_mouts) ? EtablissementClient::RAISINS_MOUTS_NON : $this->raisins_mouts;
            }
            if($this->exist('exclusion_drm')) {
                $this->exclusion_drm = is_null($this->exclusion_drm) ? EtablissementClient::EXCLUSION_DRM_NON : $this->exclusion_drm;
            }
            if($this->exist('type_dr')) {
                $this->type_dr = is_null($this->type_dr) ? EtablissementClient::TYPE_DR_DRM : $this->type_dr;
            }
        }

        if ($this->isViticulteur() || $this->isNegociant()) {
            if($this->exist('relance_ds')) {
                $this->relance_ds = is_null($this->relance_ds) ? EtablissementClient::RELANCE_DS_OUI : $this->relance_ds;
            }
        }

        $this->statut = is_null($this->statut) ? EtablissementClient::STATUT_ACTIF : $this->statut;
    }

    public function setRelanceDS($value) {
        if (!($this->isViticulteur() || $this->isNegociant())) {
            throw new sfException("Le champs 'relance_ds' n'est valable que pour les viticulteurs ou les négociants");
        }

        $this->_set('relance_ds', $value);
    }

    public function setExclusionDRM($value) {
        if (!($this->isViticulteur())) {
            throw new sfException("Le champs 'exclusion_drm' n'est valable que pour les viticulteurs");
        }

        $this->_set('exclusion_drm', $value);
    }

    public function setRaisinsMouts($value) {
        if (!($this->isViticulteur())) {
            throw new sfException("Le champs 'raisins_mouts' n'est valable que pour les viticulteurs");
        }

        $this->_set('raisins_mouts', $value);
    }

    public function setTypeDR($value) {
        if (!($this->isViticulteur())) {
            throw new sfException("Le champs 'type_dr' n'est valable que pour les viticulteurs");
        }

        $this->_set('type_dr', $value);
    }

    public function getAllDRM() {
        return acCouchdbManager::getClient()->startkey(array($this->identifiant, null))
                        ->endkey(array($this->identifiant, null))
                        ->getView("drm", "all");
    }

    public function getMasterCompte() {
        if ($this->compte) {
            return CompteClient::getInstance()->find($this->compte);
        }
        return CompteClient::getInstance()->find($this->getSociete()->compte_societe);
    }

    /*public function getExploitant() {

        return $this->getCompteExploitantObject();
    }*/

    public function getCompteExploitantObject() {
        if(!$this->getCompteExploitant()) {

            return $this->getCompte();
        }

        return CompteClient::getInstance()->find($this->getCompteExploitant());;
    }

    public function getExploitant() {
        if(!$this->exist('exploitant')) {
            $this->add('exploitant');
        }

        return $this->_get('exploitant');
    }

    public function getContact() {

        return $this->getMasterCompte();
    }

    public function getSociete() {
        return SocieteClient::getInstance()->find($this->id_societe);
    }

    public function isSameAdresseThanSociete() {

        return $this->isSameAdresseThan($this->getSociete()->getContact());
    }

    public function isSameContactThanSociete() {

        return $this->isSameContactThan($this->getSociete()->getContact());
    }

    public function isSameCompteThanSociete() {

        return ($this->compte == $this->getSociete()->compte_societe);
    }

    public function getNumCompteEtablissement() {
        if (!$this->compte)
            return null;
        if ($this->compte != $this->getSociete()->compte_societe)
            return $this->compte;
        return null;
    }

    public function getNoTvaIntraCommunautaire() {
        $societe = $this->getSociete();

        if (!$societe) {

            return null;
        }

        return $societe->no_tva_intracommunautaire;
    }

    public function setIntitule($intitule) {
        $this->_set('nom', $this->getNomWithoutIntitule());
        $this->_set('intitule', $intitule);
        if($intitule) {
            $this->_set('nom', $intitule.' '.$this->nom);
        }
    }

    public function setNom($nom) {
	$this->_set('nom', trim($this->intitule.' '.$nom));
    }

    public function getNomWithoutIntitule() {

        return self::transformNomWithoutIntitule($this->nom, $this->intitule);
    }

    public static function transformNomWithoutIntitule($nom, $intitule) {

        return preg_replace('|^'.str_replace('.', '\.', $intitule).' |', '', $nom);
    }


    public function getDenomination() {

        return ($this->nom) ? $this->nom : $this->raison_sociale;
    }

    public function addLiaison($type, $etablissement) {
        if (!in_array($type, EtablissementClient::listTypeLiaisons()))
            throw new sfException("liaison type \"$type\" unknown");
        $liaison = $this->liaisons_operateurs->add($type . '_' . $etablissement->_id);
        $liaison->type_liaison = $type;
        $liaison->id_etablissement = $etablissement->_id;
        $liaison->libelle_etablissement = $etablissement->nom;
        return $liaison;
    }

    public function isNegociant() {
        return ($this->famille == EtablissementFamilles::FAMILLE_NEGOCIANT);
    }

    public function isViticulteur() {
        return ($this->famille == EtablissementFamilles::FAMILLE_PRODUCTEUR);
    }

    public function isCourtier() {
        return ($this->famille == EtablissementFamilles::FAMILLE_COURTIER);
    }

    public function getFamilleType() {
        $familleType = array(EtablissementFamilles::FAMILLE_PRODUCTEUR => 'vendeur',
            EtablissementFamilles::FAMILLE_NEGOCIANT => 'acheteur',
            EtablissementFamilles::FAMILLE_COURTIER => 'mandataire');
        return $familleType[$this->famille];
    }

    public function getDepartement() {
        if ($this->siege->code_postal) {
            return substr($this->siege->code_postal, 0, 2);
        }
        return null;
    }

    public function getDroit() {
        if (is_null($this->droit)) {

            $this->droit = new EtablissementDroit($this);
        }

        return $this->droit;
    }

    public function hasDroit($droit) {

        return $this->getDroit()->has($droit);
    }

    public function getDroits() {

        return EtablissementDroits::getDroits($this);
    }

    public function isInterpro() {
        return ($this->region != EtablissementClient::REGION_HORS_CVO);
    }

    protected function initFamille() {
        if (!$this->famille) {
            $this->famille = EtablissementFamilles::FAMILLE_PRODUCTEUR;
        }
    }

    public function isSameIdentifiantConstruction() {

        return preg_match("/^".$this->getSociete()->getIdentifiant()."/", $this->getIdentifiant());
    }

    public function isSynchroAutoActive() {

        return false;
    }

    public function changeSociete($new_id) {
        if($this->isNew()) {
            return;
        }
        if($this->_id == $new_id) {
            return;
        }
        $this->_id_societe_origine = $this->id_societe;
        $this->id_societe = $new_id;
    }

    public function save() {
        $this->add('date_modification', date('Y-m-d'));

        if($this->isSynchroAutoActive()) {
            if(!$this->getCompte()){
                $this->setCompte($this->getSociete()->getMasterCompte()->_id);
            }
        }

        $societe = $this->getSociete();
        $needSaveSociete = false;

        if($this->isSynchroAutoActive()) {
            if(!$this->isSameAdresseThanSociete() || !$this->isSameContactThanSociete()){
                if ($this->isSameCompteThanSociete()) {
                    $compte = CompteClient::getInstance()->createCompteFromEtablissement($this);
                    $compte->addOrigine($this->_id);
                } else {
                    $compte = $this->getMasterCompte();
                }

                $this->pushContactAndAdresseTo($compte);

                $compte->id_societe = $this->getSociete()->_id;
                $compte->nom_a_afficher = $this->nom;

                $compte->save();

                $this->setCompte($compte->_id);
            } else if(!$this->isSameCompteThanSociete()){
                $compteEtablissement = $this->getMasterCompte();
                $compteSociete = $this->getSociete()->getMasterCompte();

                $this->setCompte($compteSociete->_id);

                CompteClient::getInstance()->find($compteEtablissement->_id)->delete();
            }

            if($this->isSameAdresseThanSociete()) {
                $this->pullAdresseFrom($this->getSociete()->getMasterCompte());
            }

            if($this->isSameContactThanSociete()) {
                $this->pullContactFrom($this->getSociete()->getMasterCompte());
            }
        }

        $this->initFamille();
        $this->raison_sociale = $societe->raison_sociale;
        $this->interpro = "INTERPRO-declaration";

        if($this->region != EtablissementClient::REGION_HORS_CVO) {
            $this->region = EtablissementClient::getInstance()->calculRegion($this);
        }

        if($this->isNew() || $this->_id_societe_origine) {
            $societe->addEtablissement($this);
            $needSaveSociete = true;
        }

        parent::save();

        if($needSaveSociete) {
            $societe->save();
        }

        if($this->_id_societe_origine) {
            $societeOrigine = SocieteClient::getInstance()->find($this->_id_societe_origine);
            if($societeOrigine) {
                $societeOrigine->cleanEtablissements($this);
                $societeOrigine->save();
            }
            $this->_id_societe_origine = null;
        }

        if($this->isSynchroAutoActive()) {
            $societe->getMasterCompte()->save();
        }
    }

    public function isActif() {
        return $this->statut && ($this->statut == EtablissementClient::STATUT_ACTIF);
    }

     public function isSuspendu() {
        return $this->statut && ($this->statut == SocieteClient::STATUT_SUSPENDU);
    }


    public function setIdSociete($id) {
        $soc = SocieteClient::getInstance()->find($id);
        if (!$soc)
            throw new sfException("$id n'est pas une société connue");
        $this->_set("id_societe", $id);
    }

    public function __toString() {

        return sprintf('%s (%s)', $this->nom, $this->identifiant);
    }

    public function getBailleurs() {
        $bailleurs = array();
        if (!(count($this->liaisons_operateurs)))
            return $bailleurs;
        $liaisons = $this->liaisons_operateurs;
        foreach ($liaisons as $key => $liaison) {
            if ($liaison->type_liaison == EtablissementClient::TYPE_LIAISON_BAILLEUR)
                $bailleurs[$key] = $liaison;
        }
        return $bailleurs;
    }

    public function findBailleurByNom($nom) {
        $bailleurs = $this->getBailleurs();
        foreach ($bailleurs as $key => $liaison) {
            if ($liaison->libelle_etablissement == str_replace("&", "", $nom))
                return EtablissementClient::getInstance()->find($liaison->id_etablissement);
            if ($liaison->exist('aliases'))
                foreach ($liaison->aliases as $alias) {
                    if (strtoupper($alias) == strtoupper(str_replace("&", "", $nom)))
                        return EtablissementClient::getInstance()->find($liaison->id_etablissement);
                }
        }
        return null;
    }

    public function addAliasForBailleur($identifiant_bailleur, $alias) {
        $bailleurNameNode = EtablissementClient::TYPE_LIAISON_BAILLEUR . '_' . $identifiant_bailleur;
        if (!$this->liaisons_operateurs->exist($bailleurNameNode))
            throw new sfException("La liaison avec le bailleur $identifiant_bailleur n'existe pas");
        if (!$this->liaisons_operateurs->$bailleurNameNode->exist('aliases'))
            $this->liaisons_operateurs->$bailleurNameNode->add('aliases');
        $this->liaisons_operateurs->$bailleurNameNode->aliases->add(str_replace("&amp;", "", $alias), str_replace("&amp;", "", $alias));
    }

    public function getSiegeAdresses() {
        $a = $this->siege->adresse;
        if ($this->siege->exist("adresse_complementaire")) {
            $a .= ' ; ' . $this->siege->adresse_complementaire;
        }
        return $a;
    }

    public function findEmail() {
        $etablissementPrincipal = $this->getSociete()->getEtablissementPrincipal();
        if ($this->_get('email')) {
            return $this->get('email');
        }
        if (($etablissementPrincipal->identifiant == $this->identifiant) || !$etablissementPrincipal->exist('email') || !$etablissementPrincipal->email) {
            return false;
        }
        return $etablissementPrincipal->get('email');
    }

    public function getEtablissementPrincipal() {
        return SocieteClient::getInstance()->find($this->id_societe)->getEtablissementPrincipal();
    }

    public function hasCompteTeledeclarationActivate() {
        return $this->getSociete()->getMasterCompte()->isTeledeclarationActive();
    }

    public function updateTeledeclarationEmailFromCompte() {
        $compte = $this->getMasterCompte();

        if(!$compte || !$compte->isInscrit()) {

            return;
        }

        $this->setEmailTeledeclaration($compte->email);
    }

    public function getEmailTeledeclaration() {
        if ($this->exist('teledeclaration_email') && $this->teledeclaration_email) {
            return $this->teledeclaration_email;
        }
    	if ($compteSociete = $this->getMasterCompte()) {
	        if ($compteSociete->exist('societe_information') && $compteSociete->societe_information->exist('email') && $compteSociete->societe_information->email) {
	            return $compteSociete->societe_information->email;
	        }
	        return $compteSociete->email;
        }
        if ($this->exist('email') && $this->email) {
            return $this->email;
        }
        return null;
    }

    public function setEmailTeledeclaration($email) {
        $this->add('teledeclaration_email', $email);
    }

    public function hasRegimeCrd() {
        return $this->exist('crd_regime') && $this->crd_regime;
    }

    public function getCrdRegime() {
        return $this->_get('crd_regime');
    }

    public function addCommentaire($s) {
        $c = $this->get('commentaire');
        if ($c) {
            return $this->_set('commentaire', $c . "\n" . $s);
        }
        return $this->_set('commentaire', $s);
    }

    public function getNatureLibelle() {
        if(!$this->exist('nature_inao') || !$this->nature_inao){
            return null;
        }
        return EtablissementClient::getInstance()->getNatureInaoLibelle($this->nature_inao);
    }

    public function hasLieuxStockage() {

        return $this->exist('lieux_stockage') && count($this->lieux_stockage) > 0;
    }

    public function removeLieuxStockage($identifiant = null) {
        $lieuxToRemove = array();
        if(!$this->exist('lieux_stockage')) {
            return;
        }
        foreach($this->_get('lieux_stockage') as $lieuStockage) {
            if($identifiant && !preg_match("/".$identifiant."/", $lieuStockage->getKey())) {
                continue;
            }
            $lieuxToRemove[$lieuStockage->getKey()] = $lieuStockage->getKey();
        }

        foreach($lieuxToRemove as $key) {
            $this->add('lieux_stockage')->remove($key);
        }
    }

    public function getLieuxStockage($ajoutLieuxStockage = false, $identifiant = null)
    {
        if($ajoutLieuxStockage && $this->isAjoutLieuxDeStockage() &&
                (!$this->exist('lieux_stockage') || (!count($this->getLieuxStockage(false, $identifiant))))){
            $lieu_stockage = $this->storeLieuStockage($this->adresse,
                                                    $this->commune,
                                                   $this->code_postal);
            $this->lieux_stockage->add($lieu_stockage->numero, $lieu_stockage);

            return $this->_get('lieux_stockage');
        }
        if(!$this->exist('lieux_stockage')){

            return array();
        }

        if(is_null($identifiant)) {

            return $this->_get('lieux_stockage');
        }
        $lieuxStockage = array();
        foreach($this->_get('lieux_stockage') as $lieuStockage) {
            if(!preg_match("/".$identifiant."/", $lieuStockage->getKey())) {
                continue;
            }
            $lieuxStockage[$lieuStockage->getKey()] = $lieuStockage;
        }

        return $lieuxStockage;
    }

    public function getLieuStockagePrincipal($ajoutLieuxStockage = false, $identifiant = null) {
        foreach($this->getLieuxStockage($ajoutLieuxStockage, $identifiant) as $lieu_stockage) {

            return $lieu_stockage;
        }

        return null;
    }

    public function isAjoutLieuxDeStockage(){

        return ($this->getFamille() != EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR);
    }

    public function storeLieuStockage($adresse,$commune,$code_postal)
    {
        $newId = 0;
        $identifiant = $this->getIdentifiant();
        if(!$this->exist('lieux_stockage')){
            $this->add('lieux_stockage');
        }
        $lieux_stockage = $this->getLieuxStockage(false, $identifiant);
        foreach ($lieux_stockage as $key => $value) {
            $current_id = intval(str_replace($identifiant, '', $key));
            if($current_id > $newId){
                $newId = $current_id;
            }
        }
        $newId = $identifiant.sprintf('%03d',$newId+1);
        $lieu_stockage = new stdClass();
        $lieu_stockage->numero = $newId;
        $lieu_stockage->nom = $this->nom;
        $lieu_stockage->adresse = $adresse;
        $lieu_stockage->commune = $commune;
        $lieu_stockage->code_postal = $code_postal;
        $this->_get('lieux_stockage')->add($newId, $lieu_stockage);
        return $lieu_stockage;
    }

}
