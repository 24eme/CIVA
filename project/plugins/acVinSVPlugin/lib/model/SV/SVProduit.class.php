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

        $default_taux = null;

        if ($this->getDocument()->extraction->exist($noeud) && $this->getDocument()->extraction->get($noeud)->taux_extraction) {
            $default_taux = $this->getDocument()->extraction->get($noeud)->taux_extraction;
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

    public function getSuperficieMouts()
    {
        if ($this->exist('superficie_mouts') === false) {
            return null;
        }

        return $this->_get('superficie_mouts');
    }

    public function isComplete($squeezeRevendique = false) {
        if($this->isRebeche()) {

            return !is_null($this->volume_recolte) && !is_null($this->volume_revendique);
        }

        if ($this->isMout()) {
            if ($squeezeRevendique) {
                return !is_null($this->volume_mouts) && !is_null($this->superficie_mouts);
            }
            return !is_null($this->volume_mouts) && !is_null($this->volume_mouts_revendique) && !is_null($this->superficie_mouts);
        }

        if($this->getDocument()->type == SVClient::TYPE_SV11) {

            return !is_null($this->superficie_recolte) && !is_null($this->volume_recolte) && !is_null($this->volume_revendique) && !is_null($this->volume_detruit);
        }

        if($this->getDocument()->type == SVClient::TYPE_SV12) {
            if ($squeezeRevendique) {
                return !is_null($this->superficie_recolte) && !is_null($this->quantite_recolte);
            }

            // Si pas de volume / superficie à l'étape 1
            if ($this->superficie_recolte === 0 && $this->quantite_recolte === 0) {
                return true;
            }

            return !is_null($this->superficie_recolte) && !is_null($this->quantite_recolte) && !is_null($this->volume_revendique);
        }
    }

    public function isEmpty()
    {
        if ($this->getDocument()->type == SVClient::TYPE_SV11) {
            if (! $this->superficie_recolte && ! $this->volume_recolte && ! $this->volume_revendique && ! $this->volume_detruit) {
                return true;
            }
        }

        if ($this->getDocument()->type == SVClient::TYPE_SV12) {
            if (! $this->superficie_recolte && ! $this->quantite_recolte && ! $this->volume_revendique) {
                echo $this->libelle;
                return true;
            }
        }

        return false;
    }

    public function isRebeche()
    {
        return strpos($this->getProduitHash(), '/cepages/RB') !== false;
    }

    public function isMout()
    {
        return $this->exist('volume_mouts') || $this->exist('volume_mouts_revendique');
    }

    public function setVolumeRecolte($v) {
        if ($this->isRebeche() && !$this->volume_revendique) {
            $this->_set('volume_revendique', $v);
        }
        return $this->_set('volume_recolte', $v);
    }

    public function setVolumeRevendique($v) {
        if ($this->isRebeche() && !$this->volume_recolte) {
            $this->_set('volume_recolte', $v);
        }
        return $this->_set('volume_revendique', $v);
    }

    public function setVolumeMouts($v) {
        $this->_set('volume_mouts', $v);

        $this->volume_mouts_revendique = $this->volume_mouts;
    }
}
