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
    }
    
        /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceValidee(sfWebRequest $request) {
        $this->ds = $this->getUser()->getDs();
    }
    
    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeMonEspaceColonne(sfWebRequest $request) {
        $this->dsByperiodes = $this->getUser()->getDeclarantDS()->getDsArchivesSince(($this->getUser()->getPeriodeDS()-1));
        $this->tiers = $this->getUser()->getDeclarantDS();
        krsort($this->dsByperiodes);
    }
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceNoLieuxStockage(sfWebRequest $request) {
    }
    
    
}
