<?php
class Recoltant extends BaseRecoltant {
	protected $_tiers_extend = null;

    public function __toString() {
        return $this->getNom() . " - Récoltant";
    }

    public function isDeclarantDR() {

        return true;
    }

    public function isDeclarantStock() {

        return true;
    }

    public function isDeclarantContratForSignature() {

        return true;
    }

    public function getIdentifiant() {

        return $this->cvi;
    }

    public function getMetteurEnMarche() {

        return $this->getCompteObject()->getTiersType('MetteurEnMarche');
    }

    public function getFax() {
    	$v = $this->_get('fax');
        if (intval($v))
            return $v;

        if($this->getTiersExtend() && $this->getTiersExtend()->getFax()) {

            return $this->getTiersExtend()->getFax();
        }

        return null;
    }

    public function getTelephone() {
    	$v = $this->_get('telephone');
        if (intval($v))
            return $v;

        if($this->getTiersExtend() && $this->getTiersExtend()->getTelephone()) {

            return $this->getTiersExtend()->getTelephone();
        }

        return null;
    }

    public function getTiersExtend() {
    	if(is_null($this->_tiers_extend)) {

    		$metteur_en_marche = $this->getMetteurEnMarche();
    		if($metteur_en_marche) {
    			$this->_tiers_extend = $metteur_en_marche;
    		} else {
    			$this->_tiers_extend = false;
    		}
    	}

    	return $this->_tiers_extend;
    }
}