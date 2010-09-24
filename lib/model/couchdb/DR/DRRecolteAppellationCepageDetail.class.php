<?php

class DRRecolteAppellationCepageDetail extends BaseDRRecolteAppellationCepageDetail {

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function getCodeDouane() {
      return $this->getParent()->getParent()->getCodeDouane($this->vtsgn);
    }

    public function getAcheteursValuesWithCvi($field) {
        $values = array();
        if ($this->exist($field)) {
            $acheteurs = $this->get($field);
            foreach ($acheteurs as $acheteur) {
                $values[$acheteur->cvi] = $acheteur->quantite_vendue;
            }
        }
        return $values;
    }

    protected function update() {
        parent::update();
	if ($this->getCouchdbDocument()->isValidated())
	  return ;
        $v = $this->cave_particuliere;
        $v += $this->getSumAcheteur('negoces');
        $v += $this->getSumAcheteur('cooperatives');
        $v += $this->getSumAcheteur('mouts');

        $this->volume = $v;
        $this->volume_revendique = 0;
        $this->volume_dplc = 0;

        if ($this->hasRendementCepage()) {
            $volume_max = $this->getVolumeMax();
            if ($this->volume > $volume_max) {
                $this->volume_revendique = $volume_max;
                $this->volume_dplc = $this->volume - $volume_max;
            } else {
                $this->volume_revendique = $this->volume;
            }
        } else {
            $this->volume_revendique = $this->volume;
        }

        if ($this->volume && $this->volume > 0) {
           $this->remove('motif_non_recolte');
        } else {
           $this->add('motif_non_recolte');
        }
    }

    public function getVolumeMax() {
        return ($this->superficie/100) * $this->getRendementCepage();
    }

    private function getSumAcheteur($field) {
      $sum = 0;
      if ($this->exist($field)) {
          foreach ($this->get($field) as $acheteur) {
            $sum += $acheteur->quantite_vendue;
          }
      }
      return $sum;
    }

    public function hasRendementCepage() {
        return $this->getParent()->getParent()->hasRendement();
    }
    
    public function getRendementCepage() {
        return $this->getParent()->getParent()->getRendement();
    }
    
    public function save() {
      return $this->getCouchdbDocument()->save();
    }

    public function removeVolumes() {
      $this->setVolume(null);
      $this->cave_particuliere = null;
      $this->remove('cooperatives');
      $this->remove('mouts');
      $this->remove('negoces');
    }

    public function hasMotifNonRecolteLibelle() {
        return $this->exist('motif_non_recolte');
    }
    public function isNonSaisie() {
        return ($this->getMotifNonRecolteLibelle() == 'Déclaration en cours');
    }
    public function getMotifNonRecolteLibelle() {
      if ($this->volume)
	return '';

      if ($this->exist('motif_non_recolte') && ConfigurationClient::getConfiguration()->motif_non_recolte->exist($this->motif_non_recolte)) {
          return ConfigurationClient::getConfiguration()->motif_non_recolte->get($this->motif_non_recolte);
      } else {
         return 'Déclaration en cours';
      }
      
    }
}
