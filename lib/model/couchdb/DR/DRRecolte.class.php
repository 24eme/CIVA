<?php

class DRRecolte extends BaseDRRecolte {
    
    /**
     *
     * @return Configuration
     */
    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    /**
     *
     * @return DRRecolte
     */
    public function getAppellations() {
        return $this->filter('^appellation_');
    }

    /**
     *
     * @return sfCouchdbJson
     */
    public function getConfigAppellations() {
        return $this->getConfig()->filter('^appellation_');
    }

    /**
     *
     * @return boolean
     */
    public function hasOneOrMoreAppellation() {
        return $this->getAppellations()->count() > 0;
    }

    /**
     *
     * @return boolean
     */
    public function hasAllAppellation() {
        return (!($this->getAppellations()->count() < $this->getConfigAppellations()->count()));
    }

    /**
     * 
     */
    public function removeVolumes() {
      foreach ($this->getAppellations() as $appellation) {
	$appellation->removeVolumes();
      }
    }

    /**
     *
     * @param array $params 
     */
    protected function update($params = array()) {
      parent::update($params);

      if (in_array('from_acheteurs',$params)) {
        $acheteurs = $this->getCouchdbDocument()->getAcheteurs();
        foreach($acheteurs as $key => $appellation) {
	  $app = $this->add($key);
	  if (!$app->getConfig()->hasManyLieu()) {
	    $app->add('lieu');
	  }
        }
        foreach($this->getAppellations() as $key => $appellation) {
            if (!$acheteurs->exist($key)) {
                $this->remove($key);
            }
        }
      }
    }
}
