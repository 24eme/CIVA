<?php

class AcheteurClient extends acCouchdbClient {

    protected $_acheteurs = null;

    public static function getInstance() {

        return acCouchdbManager::getClient('Acheteur');
    }

    public function loadAcheteurs() {
        $liste = $this->executeView('ACHAT', 'qualite');

        $acheteurs_negociant = array();
        $acheteurs_cave = array();
        $acheteurs_mout = array();

        foreach ($liste as $id => $etab) {
            if ($etab->getFamille() === EtablissementFamilles::FAMILLE_COOPERATIVE) {
                $acheteurs_cave[$etab->cvi]['cvi'] = $etab->cvi;
                $acheteurs_cave[$etab->cvi]['commune'] = $etab->commune;
                $acheteurs_cave[$etab->cvi]['nom'] = $etab->nom;
            }

            if (in_array($etab->getFamille(), [EtablissementFamilles::FAMILLE_NEGOCIANT, EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR])) {
                $acheteurs_negociant[$etab->cvi]['cvi'] = $etab->cvi;
                $acheteurs_negociant[$etab->cvi]['commune'] = $etab->commune;
                $acheteurs_negociant[$etab->cvi]['nom'] = $etab->nom;
            }

            if (in_array($etab->getFamille(), [EtablissementFamilles::FAMILLE_NEGOCIANT, EtablissementFamilles::FAMILLE_COOPERATIVE])) {
                $acheteurs_mout[$etab->cvi]['cvi'] = $etab->cvi;
                $acheteurs_mout[$etab->cvi]['commune'] = $etab->commune;
                $acheteurs_mout[$etab->cvi]['nom'] = $etab->nom;
            }
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
