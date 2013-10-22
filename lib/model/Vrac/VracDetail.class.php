<?php
/**
 * Model for VracDetail
 *
 */

class VracDetail extends BaseVracDetail {
	
	public function getCepage() {

        return $this->getParent()->getParent();
    }
    
    public function getAppellation() {
    	return $this->getCepage()->getAppellation();
    }
	
	public function getLibelle() {

        return $this->getCepage()->getLibelleComplet();
    }
    
    public function getLibelleSansCepage() {
    	return $this->getCepage()->getCouleur()->getLibelleComplet();
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
    	if ($this->volume_propose && $this->prix_unitaire) {
    		$this->actif = 1;
    	} else {
    		$this->actif = 0;
    	}
    }
    
    public function updateVolumeEnleve()
    {
    	$total = 0;
    	$indices = array();
    	foreach ($this->retiraisons as $key => $retiraison) {
    		if (!$retiraison->volume && !$retiraison->date) {
    			$indices[] = $key;
    			continue;
    		}
    		$total += $retiraison->volume;
    	}
    	foreach ($indices as $indice) {
    		$this->retiraisons->remove($indice);
    	}
    	$this->volume_enleve = $total;
    }
    
    public function getTotalVolumeEnleve()
    {
    	return ($this->volume_enleve && $this->actif)? $this->volume_enleve : 0;
    }
    
    public function getTotalVolumePropose()
    {
    	return ($this->volume_propose && $this->actif)? $this->volume_propose : 0;
    }
    
    public function getTotalPrixPropose()
    {
    	return ($this->volume_propose && $this->prix_unitaire && $this->actif)? $this->volume_propose * $this->prix_unitaire : 0;
    }
    
    public function getTotalPrixEnleve()
    {
    	return ($this->volume_enleve && $this->prix_unitaire && $this->actif)? $this->volume_enleve * $this->prix_unitaire : 0;
    }
    
    public function allProduitsClotures()
    {
    	return (!$this->cloture && $this->actif)? false : true;
    }
    
    public function clotureProduits()
    {
    	if (!$this->cloture && $this->actif) {
    		$this->cloture = 1;
    	}
    	return null;
    }

}