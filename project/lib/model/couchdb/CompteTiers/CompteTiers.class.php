<?php

class CompteTiers extends BaseCompteTiers {

    protected $_tiers = null;
    protected $_duplicated = null;
    
    public function getNom() {
        if($this->isCompteSociete()) {
            
            return $this->getTiersField('nom');
        }

        return $this->_get('nom');
    }

    public function getIntitule() {

        return $this->getTiersField('intitule');
    }

    /**
     *
     * @return array 
     */
    public function getTiersObject() {
        if (is_null($this->_tiers)) {
	  $this->_duplicated = null;
	  $this->_tiers = array();
	  foreach ($this->tiers as $tiers) {
	    $this->_tiers[] = acCouchdbManager::getClient()->find($tiers->id);
	  }
        }
        return $this->_tiers;
    }
    
    public function getTiersType($type) {
    	foreach ($this->tiers as $tiers) {
    		if ($tiers->type == $type) {
    			return acCouchdbManager::getClient()->find($tiers->id);
    		}
    	}
    	return null;
    }

    /**
     *
     * @param string $hash
     * @return string 
     */
    public function getTiersField($hash, $exist = false) {
        $value = null;
        foreach ($this->getTiersObject() as $tiers) {
            if ($exist && !$tiers->exist($hash)) {
                continue;
            }
            if ($tiers->type == 'Recoltant') {
                return $tiers->get($hash);
            } elseif (is_null($value)) {
                $value = $tiers->get($hash);
            }
            
        }
        return $value;
    }

    public function getGecos() {
        $login = $this->getLogin();
        $gamma = $this->getTiersField('gamma', true);
        if($gamma && $gamma->num_cotisant) {
            $login = $gamma->num_cotisant;
        }
        return $login . ',' . $this->getTiersField('no_accises', true) . ',' . $this->getTiersField('intitule') . ' ' . $this->getTiersField('nom') . ',' . $this->getTiersField('exploitant/nom', true);
    }

    public function getAdresse() {
        $value = $this->getTiersField('adresse');
        return $value ? $value : parent::getAdresse();
    }

    public function getCodePostal() {
        $value = $this->getTiersField('code_postal');
        return $value ? $value : parent::getCodePostal();
    }

    public function getCommune() {
        $value = $this->getTiersField('commune');
        return $value ? $value : parent::getCommune();
    }

    public function getDuplicatedTiers() {
      if ($this->_duplicated)
	return $this->_duplicated;

      $type = array();
      $this->_duplicated = array();
      foreach ($this->tiers as $id => $t) {
	if (isset($type[$t->type])) {
	  $this->_duplicated[$t->id] = $t;
	  $this->_duplicated[$type[$t->type]->id] = $type[$t->type];
	}
	$type[$t->type] = $t;
      }
      return $this->_duplicated;
    }

    public function hasDelegation(){

        return $this->exist('delegation') && count($this->delegation) > 0;
    }

    public function isCompteSociete() {
        $login = str_replace('C', '', $this->login);
        
        return strlen($login) == 10;
    }

    public function getIdCompteSociete() {
        if($this->isCompteSociete()) {

            return $this->_id;
        }

        return substr($this->_id, 0, strlen($this->_id) - 2);
    }

    public function getCompteSociete() {
        if($this->isCompteSociete()) {

            return $this;
        }

        return _CompteClient::getInstance()->find($this->getIdCompteSociete());
    }

    public function getTiers() {
        if($this->isCompteSociete()) {

            return $this->_get('tiers');
        }

        return $this->getCompteSociete()->tiers;
    }

    public function getDroits() {
        if($this->isCompteSociete()) {
            $this->droits = array_keys($this->getDroitsTiers());
        }

        return $this->_get('droits');
    }

    public function getDroitsTiers() {
        $droits = array();
        $tiers = $this->getTiersObject();
        foreach ($tiers as $t) {
            if ($t->type == "Recoltant") {
                $droits[_CompteClient::DROIT_DR_RECOLTANT] = null;
                $droits[_CompteClient::DROIT_DS_DECLARANT] = null;
                $droits[_CompteClient::DROIT_VRAC_SIGNATURE] = null;
            } elseif ($t->type == "MetteurEnMarche") {
                if ($t->no_accises) {
                    $droits[_CompteClient::DROIT_GAMMA] = null;
                }
                if (!$t->cvi_acheteur) {
                    $droits[_CompteClient::DROIT_VRAC_SIGNATURE] = null;
                }
            } elseif ($t->type == "Acheteur") {
                $droits[_CompteClient::DROIT_DR_ACHETEUR] = null;
                $droits[_CompteClient::DROIT_DS_DECLARANT] = null;
                $droits[_CompteClient::DROIT_VRAC_SIGNATURE] = null;
                $droits[_CompteClient::DROIT_VRAC_RESPONSABLE] = null;
            } elseif ($t->type == "Courtier") {
                $droits[_CompteClient::DROIT_VRAC_SIGNATURE] = null;
                $droits[_CompteClient::DROIT_VRAC_RESPONSABLE] = null;
            }
        }

        foreach($droits as $key => $libelle) {
            $droits[$key] = _CompteClient::getInstance()->getDroitLibelle($key);
        }

        return $droits;
    }

    public function updateTiers() {
        $this->_tiers = null;
        $droits = $this->getDroits();

        foreach($this->getTiersObject() as $tiers) {
            $droits_type = _CompteClient::getDroitsType($tiers->type);
            foreach($droits_type as $droit) {
                if(in_array($droit, $droits_type)) {
                    $tiers->emails->add($droit)->add($this->_id, array('nom' => $this->nom, 'email' => $this->email));
                }
            }

            $tiers->save();
        }
    }

    public function save() {
        parent::save();
        $this->updateTiers();
    }
}