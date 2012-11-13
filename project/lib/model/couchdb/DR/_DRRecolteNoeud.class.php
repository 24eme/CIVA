<?php

abstract class _DRRecolteNoeud extends sfCouchdbDocumentTree {


    public function getConfig() {

        return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }

    abstract public function getNoeuds();

    public function getNoeudSuivant(){
        if( $this->getConfig()->hasManyNoeuds())
            throw new sfException("getNoeud ne peut être appelé d'un noeud qui contient plusieurs noeuds...");

        return $this->getNoeuds()->getFirst();
    }

    public function getNoeudsSuivant() {

        return $this->getNoeudSuivant()->getNoeuds();
    }
/*
    public function getTotalUsagesIndustriels($force_calcul = false){

        return $this->getDataByFieldAndMethod("total_usages_industriels", array($this,"getSumNoeudFields"), $force_calcul, array("usages_industriels_calcule"));
    }
*/
    public function getTotalSuperficie($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_superficie", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalVolume($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_volume", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalCaveParticuliere() {

        return $this->getDataByFieldAndMethod('total_cave_particuliere', array($this, 'getSumNoeudWithMethod'), true, array('getTotalCaveParticuliere') );
    }

    public function getDataByFieldAndMethod($field, $method, $force_calcul = false, $parameters = array()) {
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }

        if(!empty($parameters))
            return $this->store($field, $method, $parameters);

        return $this->store($field, $method, array($field));
    }

    protected function getSumNoeudFields($field) {
        $sum = 0;
        foreach ($this->getNoeuds() as $key => $noeud) {
            if($noeud->getConfig()->excludeTotal()) {

                continue;
            }
            
            $sum += $noeud->get($field);
        }
        return $sum;
    }

    protected function getSumNoeudWithMethod($method) {
        $sum = 0;
        foreach ($this->getNoeuds() as $noeud) {
            if($noeud->getConfig()->excludeTotal()) {

                continue;
            }

            $sum += $noeud->$method();
        }
        return $sum;
    }


    public function getLibelle() {

        return $this->store('libelle', array($this, 'getInternalLibelle'));
    }

    public function getInternalLibelle() {

        return $this->getConfig()->getLibelle();
    }

    protected function getSumFields($field) {
        $sum = 0;
        foreach ($this->getNoeuds() as $k => $noeud) {
            $sum += $noeud->get($field);
        }
        return $sum;
    }

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
    }

}