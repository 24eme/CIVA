<?php

class AcheteurClient extends sfCouchdbClient {
    protected $_acheteurs = null;

    public function getAll() {
        return $this->startkey('ACHAT-0000000000')->endkey('ACHAT-9999999999')->execute();
    }

    public function loadAcheteurs() {
       $docs = $this->getAll();
       $acheteurs_negociant = array();
       $acheteurs_cave = array();
       $acheteurs_mout = array();

        foreach($docs as $id => $anyone) {
            $doc = $docs->get($id);
            if ($doc->getQualite() == "Negociant") {
                $acheteurs_negociant[$doc->getCvi()]['cvi'] = $doc->getCvi();
                $acheteurs_negociant[$doc->getCvi()]['commune'] = $doc->getCommune();
                $acheteurs_negociant[$doc->getCvi()]['nom'] = $doc->getNom();
            } elseif($doc->getQualite() == "Cooperative") {
                $acheteurs_cave[$doc->getCvi()]['cvi'] = $doc->getCvi();
                $acheteurs_cave[$doc->getCvi()]['commune'] = $doc->getCommune();
                $acheteurs_cave[$doc->getCvi()]['nom'] = $doc->getNom();
            }
            $acheteurs_mout[$doc->getCvi()]['cvi'] = $doc->getCvi();
            $acheteurs_mout[$doc->getCvi()]['commune'] = $doc->getCommune();
            $acheteurs_mout[$doc->getCvi()]['nom'] = $doc->getNom();
        }

        uasort($acheteurs_negociant, 'AcheteurClient::sortByNom');
        uasort($acheteurs_cave, 'AcheteurClient::sortByNom');
        uasort($acheteurs_mout, 'AcheteurClient::sortByNom');

        $acheteurs = array();
        $acheteurs['negoces'] = $acheteurs_negociant;
        $acheteurs['cooperatives'] = $acheteurs_cave;
        $acheteurs['mouts'] = $acheteurs_mout;
        return $acheteurs;
    }
    
    public function retrieveByCvi($cvi, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('ACHAT-'.$cvi, $hydrate);
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
