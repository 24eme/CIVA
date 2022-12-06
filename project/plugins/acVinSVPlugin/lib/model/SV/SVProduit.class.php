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

    public function getLibelleHtml() {
        $libelle = $this->getConfig()->getLibelleFormat();
		if($this->denomination_complementaire) {
			$libelle .= ' <span class="text-muted">'.$this->denomination_complementaire.'</span>';
		}

		return $libelle;
	}

    public function getTauxExtraction() {
        if($this->getQuantiteRecolte() > 0 && $this->getVolumeRevendique() > 0) {

            return round($this->getQuantiteRecolte() / $this->getVolumeRevendique(), 2);
        }

        return $this->getTauxExtractionDefault();
    }

    public function getTauxExtractionDefault()
    {
        $noeud = str_replace('/declaration/', '', $this->getProduitHash().'/'.$this->getKey());

        if ($this->getDocument()->extraction->exist($noeud) && $this->getDocument()->extraction->get($noeud)->taux_extraction) {
            $default_taux = $this->getDocument()->extraction->get($noeud)->taux_extraction;
        } else {
            $default_taux = null;
        }

        return $default_taux;
    }

	public function getLibelleComplet()
	{

		return $this->getLibelle();
	}

    public function getProduitHash() {

        return preg_replace('|/apporteurs/[^/]*/|', '/declaration/', $this->getCepage()->getHash());
    }

    public function getCepage() {

        return $this->getParent();
    }

    public function isComplete() {
        if($this->getDocument()->type == SVClient::TYPE_SV11) {

            return !is_null($this->superficie_recolte) && !is_null($this->volume_recolte) && !is_null($this->volume_revendique) && !is_null($this->volume_detruit) && !is_null($this->vci);
        }

        if($this->getDocument()->type == SVClient::TYPE_SV12) {

            return !is_null($this->superficie_recolte) && !is_null($this->quantite_recolte) && !is_null($this->volume_revendique);
        }
    }
}