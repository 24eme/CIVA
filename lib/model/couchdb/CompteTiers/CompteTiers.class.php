<?php

class CompteTiers extends BaseCompteTiers {

    protected $_tiers = null;

    public function getNom() {
        $nom = null;
        foreach ($this->tiers as $tiers) {
            if ($tiers->type == "Recoltant") {
                return $tiers->nom;
            } elseif (is_null($nom)) {
                $nom = $tiers->nom;
            }
        }
        return $nom;
    }

    /**
     *
     * @return array 
     */
    public function getTiersObject() {
        if (is_null($this->_tiers)) {
            $this->_tiers = array();
            foreach ($this->tiers as $tiers) {
                $this->_tiers[] = sfCouchdbManager::getClient()->retrieveDocumentById($tiers->id);
            }
        }

        return $this->_tiers;
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
            } elseif ($tiers->type == 'MetteurEnMarche' && is_null($value)) {
                $value = $tiers->get($hash);
            }
            
        }
        return $value;
    }

    public function getGecos() {
        return $this->getTiersField('cvi', true) . ',' . $this->getTiersField('no_accises', true) . ',' . $this->getTiersField('intitule') . ' ' . $this->getTiersField('nom') . ',' . $this->getTiersField('exploitant/nom');
    }

    public function getAdresse() {
        return $this->getTiersField('adresse');
    }

    public function getCodePostal() {
        return $this->getTiersField('code_postal');
    }

    public function getCommune() {
        return $this->getTiersField('commune');
    }

}