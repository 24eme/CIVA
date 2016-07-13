<?php

abstract class _VracTiers extends acCouchdbDocumentTree {

    protected $_tiers = null;

	public function getCompteObject()
	{
		$etablissement = EtablissementClient::getInstance()->find($this->identifiant);

        return $etablissement->getContact();
	}

    public function getTiersObject() {

        if(is_null($this->_tiers)) {
            $this->_tiers = EtablissementClient::getInstance()->find($this->identifiant);
        }

        return $this->_tiers;
    }

    public function isActif() {
        return $this->getTiersObject()->isActif();
    }

}
