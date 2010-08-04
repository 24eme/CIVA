<?php

class DRRecolte extends BaseDRRecolte {
    public function addAppellation($appellation) {
        $appellation_key = 'appellation_'.$appellation;
        if (!$this->exist($appellation_key)) {
            $appellation_obj = $this->add($appellation_key);
            $appellation_obj->appellation = $appellation;
            return $appellation_obj;
        } else {
            $this->get($appellation_key);
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
            $declaration->addAppellation($configuration->get('recolte')->get($key)->appellation);
        }
        foreach($declaration as $key => $appellation) {
            if (!$acheteurs->exist($key)) {
                $declaration->remove($key);
            }
        }
    }
}
