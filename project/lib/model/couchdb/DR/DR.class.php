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
        $this->utilisateurs_document = new UtilisateursDocument($this);
        $this->declarant_document = new DeclarantDocument($this);
    }
    
    public function setDeclarantForUpdate() {
        $this->identifiant = $this->cvi;
        $this->declarant_document = new DeclarantDocument($this);
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
            //return ($this->recolte->hasOneOrMoreAppellation());
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
        $this->recolte->certification->genre->removeVolumes();
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
    public function validate($tiers, $compte = null, $compteValidateurId = null){
        $this->update();
        $this->cleanNoeuds();
        $this->remove('etape');
        $this->add('modifiee', date('Y-m-d'));
        if (!$this->exist('validee') || !$this->validee) {
            $this->add('validee', date('Y-m-d'));
            if(!$this->hasDateDepotMairie()){
                $this->add('en_attente_envoi', true);
            }
        }
        $this->declarant->nom =  $tiers->get('nom');
        if ($compte) {
            $this->declarant->email =  $compte->email;
        } else {
            $this->declarant->email =  $tiers->get('email');
        }
        $this->declarant->telephone =  $tiers->get('telephone');
        if ($compteValidateurId) {
            $this->utilisateurs_document->addValidation($compteValidateurId, date('d/m/Y'));
        }
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
        return acCouchdbManager::getClient('Configuration')->retrieveConfiguration($this->campagne);
    }

    public function setCampagne($campagne) {
        $nextCampagne = acCouchdbManager::getClient('Configuration')->retrieveConfiguration($campagne);
        foreach ($this->recolte->getAppellations() as $k => $a) {
            if (!$nextCampagne->get($a->getParent()->getHash())->exist($k)) {
                $this->recolte->getNoeudAppellations()->remove($k);
                continue;
            }
            foreach ($a->getLieux() as $k => $l) {
                if (!$nextCampagne->get($l->getParent()->getHash())->exist($k)) {
                    $this->recolte->getNoeudAppellations()->remove($k);
                    continue;
                }
                foreach ($l->getCouleurs() as $k => $co) {
                    if (!$nextCampagne->get($co->getParent()->getHash())->exist($k)) {
                        $this->recolte->getNoeudAppellations()->remove($k);
                        continue;
                    }
                    foreach ($co->getCepages() as $k => $c) {
                        if (!$nextCampagne->get($c->getParent()->getHash())->exist($k)) {
                            $this->recolte->getNoeudAppellations()->remove($k);
                            continue;
                        }
                    }
                }
            }
        }
        return $this->_set('campagne', $campagne);
    }

    public function getRecoltantObject() {
        return acCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->cvi);
    }

    public function save() {
        parent::save();
    }

    public function check() {
        $onglet = new RecolteOnglets($this);
        $validLogVigilance = array();
        $validLogErreur = array();
        foreach ($this->recolte->getAppellations() as $appellation) {
              foreach ($appellation->getLieux() as $lieu) {
                //check le total superficie
                if ($lieu->getTotalSuperficie() == 0) {
                    array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero')));
                }

                //check le lieu
                if ($lieu->isNonSaisie()) {
                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_lieu_non_saisie')));

                    continue;
                }

                $this->checkNoeudRecapitulatif($onglet, $lieu, $validLogErreur, $validLogVigilance);
                $this->checkNoeudRecapitulatifVentes($onglet, $lieu, $validLogErreur, $validLogVigilance);

                //check les cepages
                foreach ($lieu->filter('couleur') as $couleur) {
                    $this->checkNoeudRecapitulatif($onglet, $couleur, $validLogErreur, $validLogVigilance);
                    $this->checkNoeudRecapitulatifVentes($onglet, $couleur, $validLogErreur, $validLogVigilance);

                    foreach ($couleur->getConfig()->filter('cepage_') as $key => $cepage_config) {

                        if ($cepage_config->hasMinQuantite() && $lieu->getTotalVolumeForMinQuantite() > 0) {
                            if(!$couleur->exist($key)) {
                                array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage_config->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage_config->getLibelle() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_pas_rebeches')));
                            }
                        }

                        if(!$couleur->exist($key)) {
                            continue;
                        }

                        $cepage = $couleur->get($key);
                        $totalVolRevendique = $cepage->getTotalVolume(true);

                        if($totalVolRevendique == 0 && $cepage->getConfig()->hasMinQuantite() && $lieu->getTotalVolumeForMinQuantite() == 0) {
                            $couleur->remove($key);
                            continue;
                        }

                        //Vérifie le min rebeche autorisé
                        if ($cepage->getConfig()->hasMinQuantite()) {
                            $totalVolRatioMin = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->min_quantite, 2);
                            if ($totalVolRatioMin > $totalVolRevendique) {
                                array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite')));
                            }
                        }

                        //Vérifie le max rebeche autorisé
                        if ($cepage->getConfig()->hasMaxQuantite()) {
                            $totalVolRatioMax = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->max_quantite, 2);
                            if ($totalVolRatioMax < $totalVolRevendique) {
                                array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_max_quantite')));
                            }
                        }

                        //Vérifie si aucune des colonnes d'un cépage est saisi
                        if ($cepage->isNonSaisie()) {
                            array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_cepage_non_saisie')));

                            continue;
                        }

                        // vérifie le trop plein de DPLC
                        if ($appellation->getConfig()->appellation == 'ALSACEBLANC' && $cepage->getConfig()->hasRendementCepage() && round($cepage->getDplc(), 2) > 0) {
                            array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_dplc')));
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
                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero_detail')));
                                }

                                if($cepage_config->hasLieuEditable() && !$detail->lieu) {
                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_lieudit_non_saisie_detail')));
                                }

                                if ($detail->lies > $detail->volume) {
                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_usages_industriels_superieur_volume')));

                                    continue;
                                }

                                if ($detail->isNonSaisie()) {
                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_detail_non_saisie')));

                                    continue;
                                }
                                
                                if ($detail->hasMotifNonRecolteLibelle() && $detail->getMotifNonRecolteLibelle() == "Assemblage Edelzwicker") {
                                    if (!$couleur->exist('cepage_ED') || !$couleur->cepage_ED->getTotalVolume()) {
                                        array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_ED_non_saisie')));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if($this->recolte->getTotalVolumeVendus() > 0 && !$this->recolte->canCalculVolumeRevendiqueSurPlace()) {
            array_push($validLogVigilance, array('log' => acCouchdbManager::getClient('Messages')->getMessage('err_log_pas_calculer_revendique_sur_place')));
        }

        return array('erreur' => $validLogErreur, 'vigilance' => $validLogVigilance);
    }

    protected function checkNoeudRecapitulatifVentes($onglet, $noeud, &$validLogErreur, &$validLogVigilance) {
        if(!$noeud->hasRecapitulatifVente()) {
            return;
        }

        $appellation = $noeud->getAppellation();
        $lieu = $noeud->getLieu();

        $has_complete_recap_vente_dplc = $noeud->hasCompleteRecapitulatifVenteDplc();

        //Verifie que tous les dont_dplc recapitulatif des ventes est rempli
        if (!$noeud->hasCompleteRecapitulatifVenteDplc()) {
            array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif', 'log' => $noeud->getLibelleWithAppellation() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie_dplc')));
        }

        if (!$noeud->isValidRecapitulatifVente()) {
            array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif',  'log' => $noeud->getLibelleWithAppellation() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_invalide')));
            return;
        }

        $recap_is_ok = true;
        //Vérifie que chacun des dont dplc saisie dans le récaptitulatif des ventes est inférieur au volume déclaré
        foreach($noeud->acheteurs as $type => $acheteurs) {
            foreach($acheteurs as $cvi => $acheteur) {
                $volume = $noeud->getVolumeAcheteur($cvi, $type);
                $libelle = $noeud->getLibelleWithAppellation() . ", " . $acheteur->nom . ' (' . $acheteur->type_acheteur. ')';
                
                if($acheteur->dontdplc > $volume) {
                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif',  'log' => $libelle . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_dontdplc_superieur_volume')));
                        $recap_is_ok = false;
                        continue;
                }
            }
        }

        if(!$recap_is_ok) {
            return;
        }

        if($has_complete_recap_vente_dplc && $noeud->getVolumeRevendiqueCaveParticuliere() < 0) {
            array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif', 'log' => $noeud->getLibelleWithAppellation() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_revendique_sur_place_negatif')));
        }
    }

    protected function checkNoeudRecapitulatif($onglet, $noeud, &$validLogErreur, &$validLogVigilance) {
        if(!$noeud->hasRecapitulatif()) {
            return;
        }

        $appellation = $noeud->getAppellation();
        $lieu = $noeud->getLieu();

        // Verifie que le volume revendique n'est pas négatif, cad usage industriel saisi > vol revendique
        if($noeud->getLies() > $noeud->getTotalVolume()){
            array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif', 'log' => $noeud->getLibelleWithAppellation() . ' => ' . acCouchdbManager::getClient('Messages')->getMessage('err_log_usages_industriels_superieur_volume')));
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

        return $this->utilisateurs_document->getPremiereModification();
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
        return $this->getRecoltantObject();
    }

    public function getEtablissement() {
        return acCouchdbManager::getClient('_Tiers')->retrieveByCvi($this->cvi);
    }
    
    public function storeDeclarant() {
        $this->declarant_document->storeDeclarant();
    }
    
    public function hasDateDepotMairie() {
        return $this->exist('date_depot_mairie') && !is_null($this->date_depot_mairie);
    }
    
    public function getDateDepotMairieFr() {
       return Date::francizeDate($this->date_depot_mairie);
    }
    
    function setDepotmairie($date_iso) {
        if($this->modifiee == $this->validee){
            $this->modifiee = $date_iso;
        }
        $this->validee = $date_iso;
        $this->add('date_depot_mairie',$date_iso);
    }

}
