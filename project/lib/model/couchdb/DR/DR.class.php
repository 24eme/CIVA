<?php
class DR extends BaseDR {
    const ETAPE_EXPLOITATION = 'exploitation';
    const ETAPE_RECOLTE = 'recolte';
    const ETAPE_VALIDATION = 'validation';

    public static $_etapes = array(DR::ETAPE_EXPLOITATION, DR::ETAPE_RECOLTE, DR::ETAPE_VALIDATION);
    public static $_etapes_inclusion = array(self::ETAPE_EXPLOITATION => array(), self::ETAPE_RECOLTE => array(self::ETAPE_EXPLOITATION), self::ETAPE_VALIDATION => array(self::ETAPE_EXPLOITATION, self::ETAPE_RECOLTE));

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
        $this->clean();
        $this->remove('etape');
        $this->add('modifiee', date('Y-m-d'));
        if (!$this->exist('validee') || !$this->validee) {
            $this->add('validee', date('Y-m-d'));
        }
        $this->declarant->nom =  $tiers->get('nom');
        if ($compte) {
            $this->declarant->email =  $compte->email;
        } else {
            $this->declarant->email =  $tiers->get('email');
        }
        $this->declarant->telephone =  $tiers->get('telephone');
        if ($compteValidateurId) {
            $this->utilisateurs->validation->add($compteValidateurId, date('d/m/Y'));
        }
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
    public function clean() {
        $clean = false;
        foreach($this->recolte->certification->genre->getAppellations() as $appellation) {
            foreach($appellation->getLieux() as $lieu) {
                foreach($lieu->getCouleurs() as $couleur) {
                    foreach($couleur->getCepages() as $cepage) {
                        if (count($cepage->detail) < 1) {
                            $cepage->getParent()->remove($cepage->getKey());
                            $clean = true;
                        }
                    }
                    if (count($couleur->getCepages()) < 1) {
                        $couleur->getParent()->remove($couleur->getKey());
                        $clean = true;
                    }
                }
                if (count($lieu->getCouleurs()) < 1) {
                    $lieu->getParent()->remove($lieu->getKey());
                    $clean = true;
                }
            }
            if (count($appellation->getLieux()) < 1) {
                $this->recolte->certification->genre->remove($appellation->getKey());
                $this->acheteurs->remove($appellation->getKey());
                $clean = true;
            }
        }

        if (count($this->recolte->certification->genre->getAppellations()) == 0) {
            $this->recolte->remove('certification');
        }

        if (count($this->acheteurs->getNoeudAppellations()) == 0) {
            $this->acheteurs->remove('certification');
        }

        return $clean;
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
        return sfCouchdbManager::getClient('Configuration')->retrieveConfiguration($this->campagne);
    }

    public function setCampagne($campagne) {
        $nextCampagne = sfCouchdbManager::getClient('Configuration')->retrieveConfiguration($campagne);
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
        return sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->cvi);
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
                    array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero')));
                }
                //check le lieu
                if ($lieu->isNonSaisie()) {
                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_lieu_non_saisie')));
                } else {

                    //Verifie que le recapitulatif des ventes est rempli
                    if (!$lieu->hasCompleteRecapitulatifVente()) {
                        array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie')));
                    }

                    //Verifie que le recapitulatif des ventes à du dplc si le total dplc du lieu est > 0
                    if ($lieu->getConfig()->hasRendement() && $lieu->hasAcheteurs() && $lieu->hasCompleteRecapitulatifVente() && $lieu->getDplc() > 0 && !$lieu->getTotalDontDplcRecapitulatifVente()) {
                        array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif', 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie_dplc')));
                    }

                    //Verifie que le recapitulatif des ventes n'est pas supérieur aux totaux
                    if (!$lieu->isValidRecapitulatifVente()) {
                        array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()), 'url_log_page'=> 'recolte_recapitulatif',  'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_invalide')));
                    }

                    //check les cepages
                    foreach ($lieu->filter('couleur') as $couleur) {
                        foreach ($couleur->getConfig()->filter('cepage_') as $key => $cepage_config) {
                            if ($cepage_config->hasMinQuantite()) {
                                if(!$couleur->exist($key)) {
                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage_config->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage_config->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_pas_rebeches')));
                                }
                            }
                            if ($couleur->exist($key)) {
                                $cepage = $couleur->get($key);
                                if ($cepage->getConfig()->hasMinQuantite()) {
                                    $totalVolRatio = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->min_quantite, 2);
                                    $totalVolRevendique = $cepage->getTotalVolume();
                                    if ($totalVolRatio > $totalVolRevendique) {
                                        array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite')));
                                    }
                                }
                                if ($cepage->isNonSaisie()) {
                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cepage_non_saisie')));
                                } else {
                                    // vérifie le trop plein de DPLC
                                    if ($appellation->getConfig()->appellation == 'ALSACEBLANC' && $cepage->getConfig()->hasRendement() && round($cepage->getDplc(), 2) > 0) {
                                        array_push($validLogVigilance, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_dplc')));
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
                                            /*
                       if (!$detail->superficie || $detail->superficie <= 0) {
                         array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero_detail')));
                       }
                       */
                                            if ($detail->isNonSaisie()) {
                                                array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_detail_non_saisie')));
                                            } elseif ($detail->hasMotifNonRecolteLibelle() && $detail->getMotifNonRecolteLibelle() == "Assemblage Edelzwicker") {
                                                if (!$couleur->exist('cepage_ED') || !$couleur->cepage_ED->getTotalVolume()) {
                                                    array_push($validLogErreur, array('url_log_param' => $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey()), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_ED_non_saisie')));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return array('erreur' => $validLogErreur, 'vigilance' => $validLogVigilance);
    }

    public function getPremiereModificationDr() {

        $arr_date = array();
        if ($this->exist('utilisateurs') && $this->utilisateurs->exist('edition')) {
            foreach($this->utilisateurs->edition as $date) {
                $arr_date[]= $date;
            }
        }
        if(count($arr_date) > 0)
        {
            return min($arr_date);
        }else
            return null;
    }

}
