<?php

abstract class _VracTiers extends acCouchdbDocumentTree {
    
	public function getCompteObject()
	{
		$tiers = acCouchdbManager::getClient("_Tiers")->find($this->identifiant);
		return $tiers->getCompteObject();
	}
    
}