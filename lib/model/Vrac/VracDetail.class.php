<?php
/**
 * Model for VracDetail
 *
 */

class VracDetail extends BaseVracDetail {
	
	public function getCepage() {

        return $this->getParent()->getParent();
    }
	
	public function getLibelle() {

        return $this->getCepage()->getLibelleComplet();
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
    	foreach ($this->retiraisons as $retiraison) {
    		$total += $retiraison->volume;
    	}
    	$this->volume_enleve = $total;
    }

}