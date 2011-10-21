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
            return ($this->recolte->hasOneOrMoreAppellation());
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
      $this->recolte->removeVolumes();
    }
    
    /**
     *
     * @return float 
     */
    public function getTotalSuperficie() {
      $v = 0;
      foreach($this->recolte->filter('^appellation_') as $appellation) {
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
      foreach($this->recolte->filter('^appellation_') as $appellation) {
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
      foreach($this->recolte->filter('^appellation_') as $appellation) {
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
      foreach($this->recolte->filter('^appellation_') as $appellation) {
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
      foreach($this->recolte->filter('^appellation_') as $appellation) {
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
      return !$this->exist('modifiee');
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
    public function validate($tiers){
        $this->remove('etape');
        $this->add('modifiee', date('Y-m-d'));
        if (!$this->exist('validee') || !$this->validee) {
            $this->add('validee', date('Y-m-d'));
        }
        $this->declarant->nom =  $tiers->get('nom');
        $this->declarant->email =  $tiers->get('email');
        $this->declarant->telephone =  $tiers->get('telephone');
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
        foreach($this->recolte->getAppellations() as $appellation)  {
            if (count($appellation->getLieux()) < 1) {
                $this->recolte->remove($appellation->getKey());
                $this->acheteurs->remove($appellation->getKey());
                $clean = true;
            }
            foreach($appellation->getLieux() as $lieu) {
                if (count($lieu->getCepages()) < 1) {
                    $this->recolte->remove($appellation->getKey());
                    $this->acheteurs->remove($appellation->getKey());
                    $clean = true;
                }
                foreach($lieu->getCepages() as $cepage) {
                    if (count($cepage->detail) < 1) {
                        $this->recolte->remove($appellation->getKey());
                        $this->acheteurs->remove($appellation->getKey());
                        $clean = true;
                    }
                }
            }
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
	    $this->recolte->remove($k);
	    continue;
	}
	foreach ($a->filter('^lieu') as $k => $l) {
	  if (!$nextCampagne->get($l->getParent()->getHash())->exist($k)) {
	    $this->recolte->remove($k);
	    continue;
	  }
	  foreach ($l->filter('^couleur') as $k => $co) {
	    if (!$nextCampagne->get($co->getParent()->getHash())->exist($k)) {
	      $this->recolte->remove($k);
	      continue;
	    }
	    foreach ($co->filter('^cepage') as $k => $c) {
	      if (!$nextCampagne->get($c->getParent()->getHash())->exist($k)) {
		$this->recolte->remove($k);
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
}