<?php

class AcheteurClient extends acCouchdbClient {

    protected $_acheteurs = null;

    public static function getInstance() {

        return acCouchdbManager::getClient('Acheteur');
    }

    public function loadAcheteurs() {
        $cooperatives = $this->startkey(array('Cooperative'))
                ->endkey(array('Cooperative', array()))
                ->executeView('ACHAT', 'qualite');
        $negociants = $this->startkey(array('Negociant'))
                ->endkey(array('Negociant', array()))
                ->executeView('ACHAT', 'qualite');
        $negocaves = $this->startkey(array('NegoCave'))
                ->endkey(array('NegoCave', array()))
                ->executeView('ACHAT', 'qualite');
        $recoltants = $this->startkey(array('Recoltant'))
                ->endkey(array('Recoltant', array()))
                ->executeView('ACHAT', 'qualite');

        $acheteurs_negociant = array();
        $acheteurs_cave = array();
        $acheteurs_mout = array();
        foreach ($cooperatives as $key => $value) {
            $acheteurs_cave[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_cave[$value->cvi]['commune'] = $value->commune;
            $acheteurs_cave[$value->cvi]['nom'] = $value->nom;

            $acheteurs_mout[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_mout[$value->cvi]['commune'] = $value->commune;
            $acheteurs_mout[$value->cvi]['nom'] = $value->nom;
        }
        foreach ($negociants as $key => $value) {
            $acheteurs_negociant[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_negociant[$value->cvi]['commune'] = $value->commune;
            $acheteurs_negociant[$value->cvi]['nom'] = $value->nom;

            $acheteurs_mout[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_mout[$value->cvi]['commune'] = $value->commune;
            $acheteurs_mout[$value->cvi]['nom'] = $value->nom;
        }

        foreach ($negocaves as $key => $value) {
            $acheteurs_negociant[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_negociant[$value->cvi]['commune'] = $value->commune;
            $acheteurs_negociant[$value->cvi]['nom'] = $value->nom;

            $acheteurs_mout[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_mout[$value->cvi]['commune'] = $value->commune;
            $acheteurs_mout[$value->cvi]['nom'] = $value->nom;
        }

        foreach ($recoltants as $key => $value) {
            $acheteurs_negociant[$value->cvi]['cvi'] = $value->cvi;
            $acheteurs_negociant[$value->cvi]['commune'] = $value->commune;
            $acheteurs_negociant[$value->cvi]['nom'] = $value->nom;
        }

        uasort($acheteurs_negociant, 'AcheteurClient::sortByNom');
        uasort($acheteurs_mout, 'AcheteurClient::sortByNom');

        $acheteurs = array();
        $acheteurs['negoces'] = $acheteurs_negociant;
        $acheteurs['cooperatives'] = $acheteurs_cave;
        $acheteurs['mouts'] = $acheteurs_mout;
        return $acheteurs;
    }

    public static function sortByNom($a, $b) {
        return strcmp($a['nom'], $b['nom']);
    }

    protected function loadAcheteursList($type) {
        if (is_null($this->_acheteurs)) {
            $this->_acheteurs = CacheFunction::cache('model', array($this, 'loadAcheteurs'), array(), 31556926);
        }

        return $this->_acheteurs[$type];
    }

    public function getAcheteurs() {
        if (is_null($this->_acheteurs)) {
            $this->_acheteurs = CacheFunction::cache('model', array($this, 'loadAcheteurs'), array(), 31556926);
        }

        $acheteurs = array();
        foreach($this->_acheteurs as $type => $items) {
            foreach($items as $cvi => $acheteur) {
                $acheteurs[$cvi] = $acheteur;
            }
        }

        return $acheteurs;
    }

    public function getNegoces() {
        return $this->loadAcheteursList('negoces');
    }

    public function getCooperatives() {
        return $this->loadAcheteursList('cooperatives');
    }

    public function getMouts() {
        return $this->loadAcheteursList('mouts');
    }

}
