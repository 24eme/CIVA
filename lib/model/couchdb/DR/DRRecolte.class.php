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
    public function updateFromAcheteurs() {
        $acheteurs = $this->getCouchdbDocument()->getAcheteurs();
        $declaration = $this;
        $configuration = sfCouchdbManager::getClient('Configuration')->getConfiguration();
        foreach($acheteurs as $key => $appellation) {
	  $cappellation = $configuration->get('recolte')->get($key);
	  $app = $declaration->addAppellation($cappellation->appellation);
	  if (!$app->hasManyLieu()) {
	    $app->add('lieu');
	  }
        }
        foreach($declaration as $key => $appellation) {
            if (!$acheteurs->exist($key)) {
                $declaration->remove($key);
            }
        }
    }

    public function hasAllAppellation() {
        $nb_appellation = $this->filter('^appellation')->count();
        $nb_appellation_config = $this->getConfig()->filter('^appellation')->count();
        return (!($nb_appellation < $nb_appellation_config));
    }

    public function sort() {
        $appellations = array();
        foreach($this->filter('^appellation') as $key => $item) {
            $appellations[$key] = $item->getData();
            $this->remove($key);
        }
        foreach($this->getConfig()->filter('^appellation') as $key => $item) {
           $this->add($key, $appellations[$key]);
        }
    }
}
