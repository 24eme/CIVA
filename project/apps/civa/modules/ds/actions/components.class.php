<?php

class dsComponents extends sfComponents {
    
    
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspace(sfWebRequest $request) {
      $this->ds = $this->getUser()->getDs();
      $this->ds_editable = $this->getUser()->isDsEditable();
    }
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceEnCours(sfWebRequest $request) {
        $this->ds = $this->getUser()->getDs();
        $this->campagnes = $this->getUser()->getTiers('Recoltant')->getDsArchivesSince(($this->getUser()->getCampagne()-1));
        krsort($this->campagnes);
    }
    
        /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceValidee(sfWebRequest $request) {
        $this->ds = $this->getUser()->getDs();
        $this->campagnes = $this->getUser()->getTiers('Recoltant')->getDsArchivesSince(($this->getUser()->getCampagne()-1));
        krsort($this->campagnes);
    }
    
    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeMonEspaceColonne(sfWebRequest $request) {
        $this->dsBycampagnes = $this->getUser()->getTiers('Recoltant')->getDsArchivesSince(($this->getUser()->getCampagne()-1));
        krsort($this->dsBycampagnes);
    }
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceNoLieuxStockage(sfWebRequest $request) {
    }
    
    
}
