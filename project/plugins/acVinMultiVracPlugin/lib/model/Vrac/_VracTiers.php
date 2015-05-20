<?php

abstract class _VracTiers extends acCouchdbDocumentTree {
    
    protected $_tiers = null;

	public function getCompteObject()
	{
		$tiers = acCouchdbManager::getClient("_Tiers")->find($this->identifiant);
		return $tiers->getCompteObject();
	}

    public function getTiersObject() {

        if(is_null($this->_tiers)) {
            $this->_tiers = acCouchdbManager::getClient("_Tiers")->find($this->identifiant);
        }

        return $this->_tiers;
    }

    public function isActif() {

        return $this->getTiersObject()->isActif();
    }
    
}