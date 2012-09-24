<?php

class DRRecolte extends BaseDRRecolte {

    public function getNoeuds() {

        return $this->getAppellations();
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
          foreach( $app->getConfig()->getMentions() as  $key => $mention  )
          {
            $men = $app->add($key);
            if (!$men->getConfig()->hasManyLieu()) {
                $lieu = $men->add('lieu');
                foreach ($lieu->getConfig()->filter('^couleur') as $k => $v) {
                    $lieu->add($k);
                }
            }
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
