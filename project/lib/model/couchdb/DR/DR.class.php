<?php
class DR extends BaseDR implements InterfaceProduitsDocument, IUtilisateursDocument, InterfaceDeclarantDocument {

    const ETAPE_EXPLOITATION = 'exploitation';
    const ETAPE_REPARTITION = 'repartition';
    const ETAPE_RECOLTE = 'recolte';
    const ETAPE_VALIDATION = 'validation';

    public static $_etapes = array(DR::ETAPE_EXPLOITATION, DR::ETAPE_REPARTITION, DR::ETAPE_RECOLTE, DR::ETAPE_VALIDATION);
    public static $_etapes_inclusion = array(self::ETAPE_EXPLOITATION => array(),
                                             self::ETAPE_REPARTITION => array(self::ETAPE_EXPLOITATION),
                                             self::ETAPE_RECOLTE => array(self::ETAPE_EXPLOITATION, self::ETAPE_REPARTITION),
                                             self::ETAPE_VALIDATION => array(self::ETAPE_EXPLOITATION, self::ETAPE_REPARTITION, self::ETAPE_RECOLTE));


    protected $utilisateurs_document = null;
    protected $declarant_document = null;


    public function  __construct() {
        parent::__construct();
        $this->initDocuments();
    }

    public function __clone() {
        parent::__clone();
        $this->identifiant = $this->cvi;
        $this->initDocuments();
    }

    protected function initDocuments() {
        $this->utilisateurs_document = new UtilisateursDocument($this);
        $this->declarant_document = new DeclarantDocument($this);
    }

    public function constructId() {
        $this->set('_id', 'DR-' . $this->cvi . '-' . $this->campagne);
    }

    /**
     *
     * @param string $etape
     * @return boolean
     */
    public function addEtape($etape) {
        if (!in_array($etape, self::$_etapes)) {
            throw new sfException("etape does not exist");
        }
        if ($this->checkEtape($etape)) {
            $this->add('etape');
            $this->etape = $etape;
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $etape
     * @return boolean
     */
    protected function checkEtape($etape) {
        if ($this->exist('etape') && $this->etape && !in_array($this->etape, self::$_etapes_inclusion[$etape])) {
            return false;
        }
        if ($etape == self::ETAPE_EXPLOITATION) {
            return true;
        } elseif ($etape == self::ETAPE_REPARTITION) {
            return true;
        } elseif ($etape == self::ETAPE_RECOLTE) {
            return true;
        } elseif ($etape == self::ETAPE_VALIDATION) {
            return true;
        }
        return true;
    }

    /**
     *
     */
    public function removeVolumes() {
        $this->lies = null;
        if($this->exist('recolte/certification/genre')) {
            $this->recolte->certification->genre->removeVolumes();
        }
    }

    public function getProduits() {
        return $this->recolte->getProduits();
    }

    public function getProduitsDetails() {

        return $this->recolte->getProduitsDetails();
    }

    /**
     *
     * @return float
     */
    public function getTotalSuperficie() {
        $v = 0;
        foreach($this->recolte->getNoeudAppellations()->filter('^appellation_') as $appellation) {
            $v += $appellation->getTotalSuperficie();
        }
        return $v;
    }

    /**
     *
     * @return float
     */
    public function getTotalVolume() {
        $v = 0;
        foreach($this->recolte->getNoeudAppellations()->filter('^appellation_') as $appellation) {
            $v += $appellation->getTotalVolume();
        }
        return $v;
    }

    /**
     *
     * @return float
     */
    public function getVolumeRevendique() {
        $v = 0;
        foreach($this->recolte->getNoeudAppellations()->filter('^appellation_') as $appellation) {
            $v += $appellation->getVolumeRevendique();
        }
        return $v;
    }

    /**
     *
     * @return float
     */
    public function getDplc() {
        $v = 0;
        foreach($this->recolte-getNoeudAppellations()->filter('^appellation_') as $appellation) {
            $v += $appellation->getDplc();
        }
        return $v;
    }

    /**
     *
     * @return float
     */
    public function getTotalCaveParticuliere() {
        $v = 0;
        foreach($this->recolte->certification->genre->filter('^appellation_') as $appellation) {
            $v += $appellation->getTotalCaveParticuliere();
        }
        return $v;
    }

    /**
     *
     * @return float
     */
    public function getRatioLies() {
        if (!($v = $this->getTotalCaveParticuliere())) {
            return 0;
        }

        if ($this->recolte->certification->genre->exist('appellation_VINTABLE')) {
            $v -= $this->recolte->certification->genre->get('appellation_VINTABLE')->getTotalCaveParticuliere();
        }

        if($v <= 0) {
            return 0;
        }

        return $this->lies / $v;
    }

    /**
     *
     * @return float
     */
    public function getLies(){
        $v = $this->_get('lies');
        if(!$v)
            return 0;
        else
            return $v;
    }

    /**
     *
     * @return boolean
     */
    public function canUpdate() {
        return !$this->exist('modifiee') || !$this->modifiee;
    }

    /**
     *
     * @return boolean
     */
    public function isValideeCiva() {
        if ($this->exist('modifiee')) {
            return $this->modifiee;
        }
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isValideeTiers() {
        if ($this->exist('validee')) {
            return $this->validee;
        }
        return false;
    }

    /**
     *
     * @param Tiers $tiers
     */
    public function validate($date = null, $compte_id = null){
        $this->update();
        $this->cleanNoeuds();
        $this->remove('etape');
        $this->storeDeclarant();
        if(!$date) {
            $date = date('Y-m-d');
        }

        $dateObject = new DateTime($date);

        $this->add('modifiee', $dateObject->format('Y-m-d'));
        if($compte_id) {
            $this->add("modifiee_par", $this->getValideeParByCompteId($compte_id));
        }
        if (!$this->exist('validee') || !$this->validee) {
            $this->add('validee', $dateObject->format('Y-m-d'));
            if($compte_id) {
                $this->add("validee_par", $this->getValideeParByCompteId($compte_id));
            }
        }

        if ($compte_id) {
            $this->utilisateurs_document->addValidation($compte_id, $dateObject->format('d/m/Y'));
        }
    }

    protected function getValideeParByCompteId($compte_id) {
        if($this->hasDateDepotMairie()) {

            return DRClient::VALIDEE_PAR_CIVA;
        }

        if($compte_id == "COMPTE-auto") {

            return DRClient::VALIDEE_PAR_AUTO;
        }

        $compte = CompteClient::getInstance()->find($compte_id);

        if(!$compte) {
            $compte = CompteClient::getInstance()->find($compte_id);
        }

        if(!$compte) {

            return null;
        }

        $compte_dr = $this->getEtablissementObject()->getMasterCompte();

        if($compte instanceof Compte && $compte->hasDroit(Roles::OPERATEUR)) {

            return DRClient::VALIDEE_PAR_CIVA;
        }

        if($compte instanceof Compte && $compte->getSociete()->_id != $compte_dr->getSociete()->_id) {

            return $compte->getNom();
        }

        return DRClient::VALIDEE_PAR_RECOLTANT;
    }

    public function devalidate(){
        $this->remove('validee');
        $this->remove('modifiee');
    }

    public function emailSended(){
        if($this->exist('en_attente_envoi') && $this->en_attente_envoi){
            $this->remove('en_attente_envoi');
        }
    }

    public function isHumanlyModifiee() {
        return ($this->exist('modifiee') && $this->get('modifiee') && count($this->utilisateurs_document->getLastEdition()) && $this->get('modifiee') != $this->get('validee'));
    }

    /**
     *
     * @return string
     */
    public function getDateModifieeFr() {
        return preg_replace('/(\d+)\-(\d+)\-(\d+)/', '\3/\2/\1', $this->get('modifiee'));
    }

    /**
     *
     * @return string
     */
    public function getDateValideeFr() {
        return preg_replace('/(\d+)\-(\d+)\-(\d+)/', '\3/\2/\1', $this->get('validee'));
    }

    /**
     *
     * @return float
     */
    public function getJeunesVignes(){
        $v = $this->_get('jeunes_vignes');
        if(!$v)
            return 0;
        else
            return $v;
    }

    /**
     *
     * @return boolean
     */
    public function cleanNoeuds() {

        return $this->recolte->cleanAllNodes();
    }

    /**
     *
     * @param array $params
     */
    public function update($params = array()) {
        parent::update($params);
        $u = $this->add('updated', 1);
    }

    public function getConfigurationCampagne() {

        return ConfigurationClient::getConfiguration($this->getDateConfiguration());
    }

    public function getDateConfiguration() {

        return $this->campagne."-10-01";
    }

    public function getConfig() {

        return $this->getConfigurationCampagne();
    }

    public function getConfiguration() {

        return $this->getConfigurationCampagne();
    }

    public function setCampagne($campagne) {
        $produits_to_remove = array();
        foreach($this->recolte->getProduits() as $produit) {
            if(!$this->getConfig()->exist(HashMapper::convert($produit->getHash()))) {
                $produits_to_remove[$produit->getHash()] = $produit->getHash();
            }
        }
        foreach($produits_to_remove as $hash) {
            $this->remove($hash);
        }
        $details_to_remove = array();
        foreach($this->recolte->getProduitsDetails() as $detail) {
            if($detail->vtsgn && !$detail->getCepage()->getConfig()->hasVtsgn()) {
                $details_to_remove[$detail->getHash()] = $detail->getHash();
            }
        }
        foreach($details_to_remove as $hash) {
            $this->remove($hash);
        }
        $this->cleanNoeuds();

        return $this->_set('campagne', $campagne);
    }

    public function getRecoltantObject() {
        return $this->getEtablissement();
    }

    public function save() {
        parent::save();
    }

    public function generateUrl($route, $params) {

        return sfContext::getInstance()->getRouting()->generate($route, $params);
    }

    public function check() {
        $validLogVigilance = array();
        $validLogErreur = array();
        foreach ($this->recolte->getAppellations() as $appellation) {
            if($appellation->getKey() != "appellation_VINTABLE") {
                foreach($appellation->getAcheteursArray() as $typeCvi =>$volume) {
                    if($volume > 0 && !preg_match("/^.+_6/", $typeCvi)) {
                        array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $appellation->getHash())), 'log' => $appellation->getLibelle(), 'info' => "Vous ne pouvez pas vendre de volume à un acheteur hors de la région Alsace pour cette appellation :"));
                    }
                }
            }

            foreach ($appellation->getMentions() as $mention) {
                if($mention->getTotalSuperficie() != 0) {
                    $appellation_key = $mention->getAppellation()->getKey();
                    if($mention->getKey() != "mention") {
                        $appellation_key = $mention->getKey();
                    }
                    $acheteursByType = $this->get('acheteurs')->getNoeudAppellations()->get($appellation_key);
                    foreach($acheteursByType as $type => $cvis) {
                        if(!$cvis instanceof acCouchdbJson) {
                            continue;
                        }
                        foreach($cvis as $cvi) {
                            if(round($mention->getVolumeAcheteur($cvi, $type), 2) == 0) {
                                $acheteur = EtablissementClient::getInstance()->findByCvi($cvi, acCouchdbClient::HYDRATE_JSON);
                                array_push($validLogVigilance, array('url' => $this->generateUrl("dr_recolte_noeud", array("id" => $this->_id, "hash" => $mention->getHash())), 'log' => sprintf("%s / %s", $appellation->getLibelle(), ($acheteur) ? $acheteur->nom : $cvi), 'info' => "Vous n'avez déclaré aucune vente pour cette appellation / acheteur"));
                            }
                        }
                    }
                }

                if($mention->getRendementRecoltant() >= 1000) {
                    array_push($validLogErreur, array('url' => $this->generateUrl("dr_recolte_noeud", array("id" => $this->_id, "hash" => $mention->getHash())), 'log' => $appellation->getLibelle(), 'info' => "Vérifiez votre saisie, nous avons constaté un rendement excessif dans l'appellation"));
                }

              foreach ($mention->getLieux() as $lieu) {
                if ($lieu->getTotalSuperficie() == 0 && $lieu->getTotalVolume()) {
                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $lieu->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_volume_sans_superfice')));
                } elseif($lieu->getTotalSuperficie() == 0 && ($mention->getConfig()->hasManyLieu() || $mention->getKey() == 'mention')) {
                    array_push($validLogVigilance, array('url' => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $lieu->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero')));
                }

                //check le lieu
                if ($lieu->isNonSaisie()) {
                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $lieu->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_lieu_non_saisie')));

                    continue;
                }

                $this->checkNoeudVci($lieu, $validLogErreur, $validLogVigilance);
                $this->checkNoeudRecapitulatif($lieu, $validLogErreur, $validLogVigilance);
                $this->checkNoeudRecapitulatifVentes($lieu, $validLogErreur, $validLogVigilance);

                foreach ($lieu->getCouleurs() as $couleur) {
                    $this->checkNoeudRecapitulatif($couleur, $validLogErreur, $validLogVigilance);
                    $this->checkNoeudRecapitulatifVentes($couleur, $validLogErreur, $validLogVigilance);

                    foreach ($couleur->getConfig()->getCepages() as $cepage_config) {
                        $hashCepage = HashMapper::inverse($cepage_config->getHash());
                        if ($cepage_config->hasMinQuantite() && $lieu->getTotalVolumeForMinQuantite() > 0) {
                            if(!$this->exist($hashCepage)) {
                                array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => HashMapper::inverse($cepage_config->getHash()))), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage_config->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_pas_rebeches')));
                            }
                        }

                        if(!$this->exist($hashCepage)) {
                            continue;
                        }

                        $cepage = $this->get($hashCepage);
                        $totalVolRevendique = $cepage->getTotalVolume(true);

                        if($totalVolRevendique == 0 && $cepage->getConfig()->hasMinQuantite() && $lieu->getTotalVolumeForMinQuantite() == 0) {
                            $couleur->remove($key);
                            continue;
                        }

                        $bloquant_rebeche = false;

                        //Vérifie le min rebeche autorisé
                        if ($cepage->getConfig()->hasMinQuantite()) {
                            $totalVolRatioMin = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->get('attributs/min_quantite'), 2);
                            if ($totalVolRatioMin > $totalVolRevendique) {
                                array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite')));
                                $bloquant_rebeche = true;
                            }
                        }

                        //Vérifie le max rebeche autorisé
                        if ($cepage->getConfig()->hasMaxQuantite()) {
                            $totalVolRatioMax = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->get('attributs/max_quantite'), 2);
                            if ($totalVolRatioMax < $totalVolRevendique) {
                                array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_max_quantite')));
                                $bloquant_rebeche = true;
                            }
                        }

                        if ($cepage->getConfig()->hasMinQuantite() || $cepage->getConfig()->hasMaxQuantite()) {
                            $volume_acheteurs = $cepage->getVolumeAcheteurs('cooperatives', false);
                            foreach($lieu->getVolumeAcheteursForMinQuantite() as $cvi => $volume) {
                                $volume_min = round($volume * $cepage->getConfig()->get('attributs/min_quantite'), 2);
                                $volume_max = round($volume * $cepage->getConfig()->get('attributs/max_quantite'), 2);
                                $volume_acheteur = (isset($volume_acheteurs[$cvi])) ? $volume_acheteurs[$cvi] : 0;
                                if (!$bloquant_rebeche && $cepage->getConfig()->hasMinQuantite() && $volume_acheteur < $volume_min) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_rebeches_repartition')));
                                    $bloquant_rebeche = true;
                                    break;
                                }
                                if (!$bloquant_rebeche && $cepage->getConfig()->hasMaxQuantite() && $volume_acheteur > $volume_max) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_rebeches_repartition')));
                                    $bloquant_rebeche = true;
                                    break;
                                }
                            }

                            if(!$bloquant_rebeche && count($volume_acheteurs) != count($lieu->getVolumeAcheteursForMinQuantite())) {
                                array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_rebeches_repartition')));
                                    $bloquant_rebeche = true;
                                    break;
                            }

                            $volume_cave_particuliere_min = round($lieu->getTotalCaveParticuliereForMinQuantite() * $cepage->getConfig()->get('attributs/min_quantite'), 2);
                            $volume_cave_particuliere_max = round($lieu->getTotalCaveParticuliereForMinQuantite() * $cepage->getConfig()->get('attributs/max_quantite'), 2);

                            if(!$bloquant_rebeche && $cepage->getConfig()->hasMinQuantite() && $cepage->getTotalCaveParticuliere() < $volume_cave_particuliere_min) {
                                array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_rebeches_repartition')));
                                $bloquant_rebeche = true;
                            }

                            if(!$bloquant_rebeche && $cepage->getConfig()->hasMaxQuantite() && $cepage->getTotalCaveParticuliere() > $volume_cave_particuliere_max) {
                                array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_rebeches_repartition')));
                                $bloquant_rebeche = true;
                            }
                        }

                        //Vérifie si aucune des colonnes d'un cépage est saisi
                        if ($cepage->isNonSaisie()) {
                            array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_cepage_non_saisie')));

                            continue;
                        }

                        // vérifie le trop plein de DPLC
                        if (preg_match("|appellation_ALSACEBLANC/mention$|", $mention->getHash()) && $cepage->getConfig()->hasRendementCepage() && round(($cepage->getDplc() - $cepage->getLies()),2) > 0) {
                            array_push($validLogVigilance, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_dplc')));
                        }

                        foreach ($cepage->filter('detail') as $details) {
                            foreach ($details as $detail) {
                                $detail_nom = '';
                                if ($detail->denomination != '' || $detail->vtsgn != '') {
                                    $detail_nom .= ' - ';
                                }
                                if ($detail->denomination != '')
                                    $detail_nom .= $detail->denomination . ' ';
                                if ($detail->vtsgn != '')
                                    $detail_nom .= $detail->vtsgn . ' ';

                                if ($cepage_config->isSuperficieRequired() && $detail->superficie <= 0) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom, 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero_detail')));
                                }

                                if($cepage_config->hasLieuEditable() && !$detail->lieu) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom, 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_lieudit_non_saisie_detail')));
                                }

                                if ($detail->lies > ($detail->cave_particuliere + $detail->getTotalVolumeAcheteurs('mouts'))) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom, 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_usages_industriels_superieur_volume_sur_place')));

                                    continue;
                                }

                                if ($detail->isNonSaisie()) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom, 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_detail_non_saisie')));

                                    continue;
                                }

                                if ($detail->vci > 0 && !$detail->canHaveVci()) {
                                    array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom, 'info' => "Le VCI n'est pas autorisé pour ce produit"));

                                    continue;
                                }

                                if ($detail->hasMotifNonRecolteLibelle() && $detail->getMotifNonRecolteLibelle() == "Assemblage Edelzwicker") {
                                    if (!$couleur->exist('cepage_ED') || !$couleur->cepage_ED->getTotalVolume()) {
                                        array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $cepage->getHash())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom, 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_ED_non_saisie')));
                                    }
                                }
                            }
                        }
                    }
                }
              }
            }
        }

        if($this->exist('jus_raisin_superficie') && $this->exist('jus_raisin_volume') && $this->jus_raisin_superficie > 0 && !$this->jus_raisin_volume) {
            array_push($validLogErreur, array('url' => $this->generateUrl("dr_autres", array("id" => $this->_id)), 'log' => "Jus de raisin", 'info' => "Vous n'avez pas saisi le volume"));
        }

        if($this->exist('jus_raisin_superficie') && $this->exist('jus_raisin_volume') && $this->jus_raisin_volume > 0 && !$this->jus_raisin_superficie) {
            array_push($validLogErreur, array('url' => $this->generateUrl("dr_autres", array("id" => $this->_id)), 'log' => "Jus de raisin", 'info' => "Vous n'avez pas saisi la superficie"));
        }

        return array('erreur' => $validLogErreur, 'vigilance' => $validLogVigilance);
    }

    protected function checkNoeudVci($noeud, &$validLogErreur, &$validLogVigilance) {
        if(!$noeud->canHaveVci()) {
            return;
        }
        if(round($noeud->getTotalVci(), 2) > round($noeud->getVolumeVciMax(), 2)) {
            array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $noeud->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => "Trop de vci déclaré, maximum pour cette appellation ".sprintf("%.2f", $noeud->getVolumeVciMax())." hl"));
            return;
        }
    }

    protected function checkNoeudRecapitulatifVentes($noeud, &$validLogErreur, &$validLogVigilance) {
        if(!$noeud->hasRecapitulatifVente()) {
            return;
        }

        $appellation = $noeud->getAppellation();
        $lieu = $noeud->getLieu();

        $has_no_complete = $noeud->hasNoCompleteRecapitulatifVente();

        //Vérifie que le récap des ventes a commencé a être saisi
        if ($has_no_complete) {
            if($noeud->canHaveVci() && $noeud->getTotalVci() > 0) {
                array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie')));
            } else {
                array_push($validLogVigilance, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie')));

            }
        }

        //Vérifie que tous les dont_dplc, dont_vci et superficie dans le recapitulatif des ventes est rempli
        if (!$has_no_complete && !$noeud->hasCompleteRecapitulatifVente()) {
            if($noeud->canHaveVci() && $noeud->getTotalVci() > 0) {
                array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => "Dans le récapitulatif des ventes vous n'avez pas complété toutes les superficies et/ou tous les volumes en dépassement et/ou tous les volumes de vci"));
            } else {
                array_push($validLogVigilance, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie_superficie_dplc')));
            }
        }

        if (!$noeud->isValidRecapitulatifVente()) {
            array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_invalide')));
            return;
        }

        $recap_is_ok = true;

        if(round($noeud->getTotalDontDplcVendus(),2) > round($noeud->getDontDplcVendusMax(), 2)) {

            array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_dontdplc_trop_eleve')));
            return;
        }

        if(round($noeud->getTotalDontVciVendus(), 2) > round($noeud->getDontVciVendusMax(), 2)) {

            array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $noeud->getLibelleWithAppellation(), 'info' => "Dans le récapitulatif des ventes, la somme des volumes en \"dont vci\" des acheteurs ne peut pas être supérieure au \"volume de vci\" attribuable aux acheteurs"));
            return;
        }

        //Vérifie que chacun des dont dplc saisie dans le récaptitulatif des ventes est inférieur au volume déclaré
        foreach($noeud->acheteurs as $type => $acheteurs) {
            foreach($acheteurs as $cvi => $acheteur) {
                $volume = round($noeud->getVolumeAcheteur($cvi, $type), 2);
                $libelle = $noeud->getLibelleWithAppellation() . ", " . $acheteur->nom . ' (' . $acheteur->type_acheteur. ')';

                if($acheteur->dontdplc > $volume) {
                    array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $libelle, 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_dontdplc_superieur_volume')));
                        $recap_is_ok = false;
                        continue;
                }

                if($acheteur->dontvci > $volume) {
                    array_push($validLogErreur, array('url' => $this->generateUrl('dr_recolte_recapitulatif', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'log' => $libelle, 'info' => "Dans le récapitulatif des ventes, le volume en \"dont vci\" d'un acheteur doit être inférieur à son volume vendu"));
                        $recap_is_ok = false;
                        continue;
                }
            }
        }

        if(!$recap_is_ok) {
            return;
        }

        if($noeud->getLies() > $noeud->getLiesMax()) {

            return;
        }

        if($noeud->canCalculVolumeRevendiqueSurPlace() && $noeud->getVolumeRevendiqueCaveParticuliere() < 0) {
            array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'url'=> $this->generateUrl('dr_recolte_recapitulatif', $this), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_revendique_sur_place_negatif')));
        }
    }

    protected function checkNoeudRecapitulatif($noeud, &$validLogErreur, &$validLogVigilance) {
        if(!$noeud->hasRecapitulatif()) {
            return;
        }

        $appellation = $noeud->getAppellation();
        $lieu = $noeud->getLieu();

        // Verifie que les lies sont inférieur au volume sur place
        if(!$noeud->getLiesMax() && $noeud->getLies() > 0) {
            array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'url'=> $this->generateUrl('dr_recolte_recapitulatif', $this), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_usages_industriels_pas_volume_sur_place')));

        } elseif($noeud->getLies() > $noeud->getLiesMax()) {
            array_push($validLogErreur, array("url" => $this->generateUrl('dr_recolte_noeud', array('id' => $this->_id, 'hash' => $lieu->getHash())), 'url'=> $this->generateUrl('dr_recolte_recapitulatif', $this), 'log' => $noeud->getLibelleWithAppellation(), 'info' => acCouchdbManager::getClient('Messages')->getMessage('err_log_usages_industriels_superieur_volume_sur_place')));
        }
    }

    public function hasVolumeSurPlace() {
        foreach ($this->recolte->getAppellations() as $appellation) {
            if(($appellation->getVolumeRevendiqueCaveParticuliere() !== null) && ($appellation->getVolumeRevendiqueCaveParticuliere())){
                return true;
            }
        }
        return false;
    }

    public function getPremiereModificationDr() {
        if(!$this->utilisateurs_document->getPremiereModification()) {

            return null;
        }

        preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $this->utilisateurs_document->getPremiereModification(), $matches);

        return $matches[3]."-".$matches[2]."-".$matches[1];
    }

    public function addEdition($id_user, $date) {
        return $this->utilisateurs_document->addEdition($id_user, $date);
    }

    public function addValidation($id_user, $date) {
        return $this->utilisateurs_document->addValidation($id_user, $date);
    }

    public function getLastEdition() {
        return $this->utilisateurs_document->getLastEdition();
    }

    public function getLastValidation() {
        return $this->utilisateurs_document->getLastValidation();
    }

    public function removeValidation() {
       return $this->utilisateurs_document->removeValidation();
    }

    public function getEtablissementObject() {

        return $this->getEtablissement();
    }

    public function getEtablissement() {
        return EtablissementClient::getInstance()->find($this->cvi);
    }

    public function storeDeclarant() {
        $this->identifiant = $this->cvi;
        $this->declarant_document->storeDeclarant();

        $tiers = $this->getEtablissement();

        $this->declaration_commune = $tiers->declaration_commune;
        $this->declaration_insee = $tiers->declaration_insee;

        if(!$this->declarant->email) {
            $this->declarant->email = $tiers->getEmailTeledeclaration();
        }

        $this->declarant->exploitant->sexe = $tiers->exploitant->civilite;
        $this->declarant->exploitant->nom = $tiers->exploitant->nom;
        $this->declarant->exploitant->adresse = $tiers->exploitant->adresse;
        $this->declarant->exploitant->code_postal = $tiers->exploitant->code_postal;
        $this->declarant->exploitant->commune = $tiers->exploitant->commune;
        //$this->declarant->exploitant->date_naissance = $tiers->exploitant->date_naissance;
        $this->declarant->exploitant->telephone = $tiers->exploitant->telephone;
    }

    public function hasDateDepotMairie() {
        return $this->exist('date_depot_mairie');
    }

    public function getDateDepotMairieFr() {
       return Date::francizeDate($this->date_depot_mairie);
    }

    public function setDepotmairie($date_iso) {
        if($this->modifiee == $this->validee){
            $this->modifiee = $date_iso;
        }
        $this->validee = $date_iso;
        $this->add('date_depot_mairie',$date_iso);
    }

    public function getDateValidationFr() {
        return $this->getDateFromUtilisateurs('validation');
    }

    public function getDateEditionFr() {
       return $this->getDateFromUtilisateurs('edition');
    }

    public function getValidateurCompteId() {
        if(!$this->exist("utilisateurs")) {
            return null;
        }

        foreach($this->utilisateurs->validation as $compte_id) {

            return $compte_id;
        }

        return null;
    }

    private function getDateFromUtilisateurs($editOrValid)
    {
        $date = date('Y_m-d');
        if($this->exist('utilisateurs') && $this->utilisateurs->exist($editOrValid)){
            $node = $this->utilisateurs->$editOrValid->toSimpleFields();
            if(count($node)){
                $date = $node[key($node)];
            }
        }
       return Date::francizeDate($date);
    }

    public function hasAutorisation($autorisation) {


        return $this->exist('autorisations') &&
               $this->autorisations->exist($autorisation) &&
               $this->autorisations->get($autorisation);
    }

    public function getDRMEdiProduitRows(DRMGenerateCSV $drmGenerateCSV){
      $lignesEdi = "";
      foreach ($this->getProduitsDetails() as $hashProduit => $produit) {
        if($produit->getCepage()->getKey() == 'cepage_RB') {
            continue;
        }
        if(!$produit->getTotalCaveParticuliere()) {
            continue;
        }
        $cepageNode = $produit->getParent()->getParent();
        $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit($cepageNode);
      }
        $recap = DRClient::getInstance()->getTotauxByAppellationsRecap($this);

       if($this->recolte->getLiesTotal()) {
          $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit("Lies et Bourbes");
       }

       if($this->recolte->getSurPlaceRebeches()) {
           $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit("Rebêches ");
       }

      $dplcRouge = $recap["ALSACEROUGEROSE"]->dplc_sur_place;
      $dplcBlanc = $recap["ALSACEBLANC"]->dplc_sur_place + $recap["GRDCRU"]->dplc_sur_place + $recap["CREMANT"]->dplc_sur_place;

      if($dplcRouge) {
        $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit("DRA/DPLC Rouge");
      }
      if($dplcBlanc) {
        $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit("DRA/DPLC Blanc");
      }
      if($this->exist('recolte/certification/genre/appellation_ALSACEBLANC') && $this->get('recolte/certification/genre/appellation_ALSACEBLANC')->getVciCaveParticuliere()) {
          $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit("VCI Alsace blanc");
      }

      if($this->exist('recolte/certification/genre/appellation_CREMANT') && $this->get('recolte/certification/genre/appellation_CREMANT')->getVciCaveParticuliere()) {
          $lignesEdi.= $drmGenerateCSV->createRowStockNullProduit("VCI Crémant d'Alsace");
      }

      return $lignesEdi;
    }

    public function getDRMEdiMouvementRows(DRMGenerateCSV $drmGenerateCSV){
     $lignesEdi = "";
     foreach ($this->getProduits() as $hashProduit => $produit) {
           $noeud = $produit;
           if($produit->getCepage()->getKey() == 'cepage_RB') {
               continue;
           }
           if(in_array($produit->getCepage()->getConfig()->getAppellation()->getKey(), array('PINOTNOIR', 'PINOTNOIRROUGE'))) {
               $noeud = $produit->getLieu();
           }
           if(!$noeud->getVolumeRevendiqueCaveParticuliere() && !($noeud->getTotalVolumeAcheteurs('mouts'))) {
               continue;
           }
           if($noeud->getVolumeRevendiqueCaveParticuliere()) {
               $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail($produit, "entrees", "recolte", $noeud->getVolumeRevendiqueCaveParticuliere());
           }
           if($noeud->getTotalVolumeAcheteurs('mouts')) {
               $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail($produit, "entrees", "recolte", $noeud->getTotalVolumeAcheteurs('mouts'));
               $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail($produit, "sorties", "vrac", $noeud->getTotalVolumeAcheteurs('mouts'));
           }
     }

      $recap = DRClient::getInstance()->getTotauxByAppellationsRecap($this);

       if($this->recolte->getLiesTotal()) {
          $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail("Lies et Bourbes", "entrees", "recolte", $this->recolte->getLiesTotal());
       }

       if($this->recolte->getSurPlaceRebeches()) {
           $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail("Rebêches ", "entrees", "recolte", $this->recolte->getSurPlaceRebeches());
       }

      $dplcRouge = $recap["ALSACEROUGEROSE"]->dplc_sur_place;
      $dplcBlanc = $recap["ALSACEBLANC"]->dplc_sur_place + $recap["GRDCRU"]->dplc_sur_place + $recap["CREMANT"]->dplc_sur_place;

      if($dplcRouge) {
        $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail("DRA/DPLC Rouge", "entrees", "recolte", $recap["ALSACEROUGEROSE"]->dplc_sur_place);
      }
      if($dplcBlanc) {
        $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail("DRA/DPLC Blanc", "entrees", "recolte", $dplcBlanc);
      }
      if($this->exist('recolte/certification/genre/appellation_ALSACEBLANC') && $this->get('recolte/certification/genre/appellation_ALSACEBLANC')->getVciCaveParticuliere()) {
          $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail("VCI Alsace blanc", "entrees", "recolte", $this->get('recolte/certification/genre/appellation_ALSACEBLANC')->getVciCaveParticuliere());
      }

      if($this->exist('recolte/certification/genre/appellation_CREMANT') && $this->get('recolte/certification/genre/appellation_CREMANT')->getVciCaveParticuliere()) {
          $lignesEdi.= $drmGenerateCSV->createRowMouvementProduitDetail("VCI Crémant d'Alsace", "entrees", "recolte", $this->get('recolte/certification/genre/appellation_CREMANT')->getVciCaveParticuliere());
      }

      return $lignesEdi;
    }

    public function hasAppellationsAvecVtsgn() {

        return count($this->getAppellationsAvecVtsgn()) >= count($this->getConfigAppellationsAvecVtsgn());
    }

    public function getAppellationsAvecVtsgn() {

        return $this->recolte->getAppellationsAvecVtsgn();
    }

    public function getConfigAppellationsAvecVtsgn() {

        return DRClient::getInstance()->getConfigAppellationsAvecVtsgn($this->getConfig());
    }
}
