<?php
/**
 * Model for SVProduit
 *
 */

class SVProduit extends BaseSVProduit {
    public function getConfig()
	{
		return $this->getCouchdbDocument()->getConfiguration()->get($this->getProduitHash());
	}

	public function getLibelle() {
		if(!$this->_get('libelle')) {
			$this->libelle = $this->getConfig()->getLibelleFormat();
			if($this->denomination_complementaire) {
				$this->libelle .= ' '.$this->denomination_complementaire;
			}
		}

		return $this->_get('libelle');
	}

    public function getTauxExtraction() {
        if($this->getQuantiteRecolte() > 0 && $this->getVolumeRevendique() > 0) {

            return round($this->getQuantiteRecolte() / $this->getVolumeRevendique(), 2);
        }

        return 130;
    }

	public function getLibelleComplet()
	{

		return $this->getLibelle();
	}

    public function getProduitHash() {

        return preg_replace('|/apporteurs/[^/]*/|', '/declaration/', $this->getParent()->getHash());
    }
}