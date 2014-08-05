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
    
    public function getTiersType($type = null) {
        $tiers = $this->getTiersIndexedByType();

        if(!$type) {
            if (array_key_exists('Recoltant', $tiers)) {
                $type = 'Recoltant';
            } elseif (array_key_exists('Acheteur', $tiers)) {
                $type = 'Acheteur';
            } elseif (array_key_exists('Courtier', $tiers)) {
                $type = 'Courtier';
            } else {
                $type = 'MetteurEnMarche';
            }
        }

        if(!isset($tiers[$type])) {

            return null;
        }

    	return $tiers[$type];
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
                $value = $tiers->get($hash);
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
        
        return !$this->exist("id_compte_societe") || !$this->id_compte_societe || $this->id_compte_societe == $this->_id;
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

    public function hasDroit($droit) {

        $droits = $this->getDroits();

        return in_array($droit, $droits->toArray(true, false));
    }

    public function getDroits() {
        if($this->isCompteSociete() && count($this->_get('droits')->toArray(true, false)) == 0) {
            $this->droits = array_keys($this->getDroitsTiers());
        }

        return $this->_get('droits');
    }

    public function getDroitsTiers() {
        $droits = array();
        $tiers = $this->getTiersObject();
        foreach ($tiers as $t) {
            if($t->isDeclarantDR()) {
                $droits[_CompteClient::DROIT_DR_RECOLTANT] = null;
            }
            if($t->isDeclarantStock()) {
                $droits[_CompteClient::DROIT_DS_DECLARANT] = null;
            }

            if($t->isDeclarantContratForSignature()) {
                $droits[_CompteClient::DROIT_VRAC_SIGNATURE] = null;
            }

            if($t->isDeclarantContratForSignature()) {
                $droits[_CompteClient::DROIT_VRAC_RESPONSABLE] = null;
            }

            if($t->isDeclarantGamma()) {
                $droits[_CompteClient::DROIT_GAMMA] = null;
            }

            if($t->isDeclarantDRAcheteur()) {
                $droits[_CompteClient::DROIT_DR_ACHETEUR] = null;
            }
        }

        foreach($droits as $key => $libelle) {
            $droits[$key] = _CompteClient::getInstance()->getDroitLibelle($key);
        }

        ksort($droits);

        return $droits;
    }

    public function updateTiers() {
        $this->_tiers = null;
        $droits = $this->getDroits()->toArray(true, false);

        foreach($this->getTiersObject() as $tiers) {
            foreach($droits as $droit) {
                $tiers->emails->add($droit)->add($this->_id, array('nom' => $this->nom, 'email' => $this->email));
            }

            $tiers->save();
        }
    }

    public function getTiersIndexedByType() {
        $tiers = array();
        foreach($this->getTiersObject() as $t) {
            $tiers[$t->type] = $t;
        }
        
        return $tiers; 
    }

    public function getDeclarantDS() {
        $tiers_propriete = null;
        foreach($this->getTiersObject() as $t) {
            if($t->isDeclarantStockNegoce()) {
                
                return $t->getDeclarantDS();
            }

            if($t->isDeclarantStockPropriete()) {
                $tiers_propriete = $t->getDeclarantDS();
            }
        }

        return $tiers_propriete;
    }

    public function save() {
        parent::save();
        $this->updateTiers();
    }
}