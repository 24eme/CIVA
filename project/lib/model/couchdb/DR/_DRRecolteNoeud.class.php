<?php

abstract class _DRRecolteNoeud extends sfCouchdbDocumentTree {

    public function getConfig() {

        return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }

    abstract public function getNoeuds();

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


}