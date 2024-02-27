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

    public function getRaisonSociale() {
        $rs = $this->_get('raison_sociale');
        if (strpos($rs, $this->intitule) !== false) {
            $rs = str_replace($this->intitule.' ', '', $rs);
            $this->raison_sociale = $rs;
        }
        return $rs;
    }

}
