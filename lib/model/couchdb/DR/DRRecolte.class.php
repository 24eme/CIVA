<?php

class DRRecolte extends BaseDRRecolte {
    
    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function addAppellation($appellation) {
        $appellation_key = 'appellation_'.$appellation;
        if (!$this->exist($appellation_key)) {
            $appellation_obj = $this->add($appellation_key);
            $appellation_obj->appellation = $appellation;
            return $appellation_obj;
        } else {
            return $this->get($appellation_key);
        }
    }

    public function getAppellation($appellation) {
        return $this->get('appellation_'.$appellation);
    }

    protected function update($params = array()) {
      parent::update($params);
      
      if (in_array('from_acheteurs',$params)) {
        $acheteurs = $this->getCouchdbDocument()->getAcheteurs();
        $configuration = sfCouchdbManager::getClient('Configuration')->getConfiguration();
        foreach($acheteurs as $key => $appellation) {
	  $cappellation = $configuration->get('recolte')->get($key);
	  $app = $this->addAppellation($cappellation->appellation);
	  if (!$app->hasManyLieu()) {
	    $app->add('lieu');
	  }
        }
        foreach($this->filter('appellation_') as $key => $appellation) {
            if (!$acheteurs->exist($key)) {
                $this->remove($key);
            }
        }
      }
    }

    public function hasOneOrMoreAppellation() {
        return $this->filter('^appellation_')->count() > 0;
    }

    public function hasAllAppellation() {
        $nb_appellation = $this->filter('^appellation')->count();
        $nb_appellation_config = $this->getConfig()->filter('^appellation')->count();
        return (!($nb_appellation < $nb_appellation_config));
    }

    public function removeVolumes() {
      foreach ($this->filter('^appellation_') as $appellation) {
	$appellation->removeVolumes();
      }
    }
}
