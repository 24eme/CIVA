<?php
/**
 * Model for VracDetail
 *
 */

class VracDetail extends BaseVracDetail {

	public function getCepage() {

        return $this->getParent()->getParent();
    }

	public function getConfig() {

		return $this->getCepage()->getConfig();
	}

    public function getAppellation() {
    	return $this->getCepage()->getAppellation();
    }

	public function getLibelle() {

        return $this->getCepage()->getLibelleComplet();
    }

    public function getLibelleSansCepage() {
    	return $this->getAppellation()->getLibelleComplet();
    }

    public function getLieuLibelle() {
    	return $this->getCepage()->getCouleur()->getLieu()->getLibelle();
    }

	public function getComplementPartielLibelle() {
		$complement = '';
		if ($this->lieu_dit) {
			$complement .= ' '.$this->lieu_dit;
		}
		if ($this->vtsgn) {
			$complement .= ' '.$this->vtsgn;
		}
        return $complement;
    }

	public function getComplementLibelle() {
		$complement = $this->getComplementPartielLibelle();
		if ($this->denomination) {
			$complement .= ' '.$this->denomination;
		}
		if ($this->millesime) {
			$complement .= ' '.$this->millesime;
		}
        return $complement;
    }

	public function getLibellePartiel() {
		return $this->getLibelle().$this->getComplementPartielLibelle();
    }

	public function getLibelleComplet() {
		return $this->getLibelle().$this->getComplementLibelle();
    }

    public function defineActive()
    {
    	$this->actif = 0;
    	if ($this->exist('nb_bouteille')) {
    		if ($this->nb_bouteille && $this->centilisation) {
	    		$this->actif = 1;
	    	}
    	} else {
            if ($this->getDocument()->hasDoubleValidation() && ($this->volume_propose||$this->surface_propose)) {
                $this->actif = 1;
            } elseif (($this->volume_propose||$this->surface_propose) && $this->prix_unitaire) {
	    		$this->actif = 1;
	    	}
    	}
    }

    public function updateVolumeEnleve()
    {
    	$total = null;
    	$indices = array();
    	foreach ($this->retiraisons as $key => $retiraison) {
    		if (!$retiraison->volume && !$retiraison->date) {
    			$indices[] = $key;
    			continue;
    		}
    		if ($retiraison->volume !== null) {
    			$total += $retiraison->volume;
    		}
    	}
    	foreach ($indices as $indice) {
    		$this->retiraisons->remove($indice);
    	}
    	$this->volume_enleve = $total;
    }

	public function autoFillRetiraisons($fillDate = true) {
		if(count($this->retiraisons) > 0) {
			return;
		}

		$retiraison = $this->retiraisons->add();
		if($fillDate) {
			$retiraison->date = $this->getDocument()->valide->date_validation;
		}
		$retiraison->volume = $this->volume_propose;
		$this->clotureProduits();
		$this->updateVolumeEnleve();
	}

    public function getTotalVolumeEnleve()
    {
    	return ($this->volume_enleve && $this->actif)? $this->volume_enleve : 0;
    }

    public function getTotalVolumePropose()
    {
    	return ($this->volume_propose && $this->actif)? $this->volume_propose : 0;
    }

    public function getTotalSurfacePropose()
    {
    	return ($this->surface_propose && $this->actif)? $this->surface_propose : 0;
    }

    public function getTotalPrixPropose()
    {
    	if ($this->exist('nb_bouteille')) {
    		return ($this->nb_bouteille && $this->prix_unitaire && $this->actif)? $this->nb_bouteille * $this->prix_unitaire : 0;
    	} else {
    		return ($this->volume_propose && $this->prix_unitaire && $this->actif)? $this->volume_propose * $this->prix_unitaire : 0;
    	}
    }

    public function getTotalPrixEnleve()
    {
    	if ($this->exist('nb_bouteille')) {
    		return ($this->nb_bouteille && $this->prix_unitaire && $this->actif)? $this->nb_bouteille * $this->prix_unitaire : 0;
    	} else {
    		return ($this->volume_enleve && $this->prix_unitaire && $this->actif)? $this->volume_enleve * $this->prix_unitaire : 0;
    	}
    }

    public function allProduitsClotures()
    {
    	return (!$this->cloture && $this->actif)? false : true;
    }

    public function hasRetiraisons()
    {
    	return (count($this->retiraisons) > 0)? true : false;
    }

    public function clotureProduits()
    {
    	if (!$this->cloture && $this->actif) {
    		$this->cloture = 1;
    	}
    	return null;
    }

    public function updateProduit()
    {
    	if ($this->exist('nb_bouteille') && $this->actif) {
    		$this->volume_propose = round(($this->nb_bouteille * $this->centilisation) / 10000, 2);
    		$this->volume_enleve = $this->volume_propose;
    		$this->clotureProduits();
    	}
    }

	public function getLabel() {
		if(!$this->exist('label')) {

			return null;
		}

		return $this->_get('label');
	}

    public function clear()
    {
    	$this->vtsgn = null;
    	$this->lieu_dit = null;
    	$this->millesime = null;
    	$this->prix_unitaire = null;
    	$this->denomination = null;
    	$this->cloture = null;
        $this->surface_propose = null;
    	$this->volume_propose = null;
    	$this->volume_enleve = null;
	$this->remove('label');
    	if ($this->exist('nb_bouteille')) {
	    	$this->nb_bouteille = null;
	    	$this->centilisation = null;
    	}
    }

}
