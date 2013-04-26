<?php
class dsActions extends sfActions {
    
    public function executeIndex(sfWebRequest $request) {
       if ($request->isMethod(sfWebRequest::POST)) {
            $this->tiers = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($request['cvi']);  
            $declarationDs = DSClient::getInstance()->findOrCreateDsByCvi($this->tiers->cvi,date('Y-m-d'));     
            $declarationDs->save();
            $this->redirect('ds_lieux_stockage', $this->tiers);
            }
        } 

    public function executeMonEspace(sfWebRequest $request) {    
         
        $this->tiers = $this->getRoute()->getTiers();        
        $this->dsHistorique = DSClient::getInstance()->getHistoryByOperateur($this->etablissement);
        $this->generationOperateurForm = new DSGenerationOperateurForm();
        
        if ($request->isMethod(sfWebRequest::POST)) {
	          $this->generationOperateurForm->bind($request->getParameter($this->generationOperateurForm->getName()));
	          if ($this->generationOperateurForm->isValid()) {
                $values = $this->generationOperateurForm->getValues();
                $date = $values["date_declaration"];
          	    try {
          	        $ds = DSClient::getInstance()->findOrCreateDsByEtbId($this->etablissement->identifiant, $date);     
          	        $ds->save();
          	    }catch(sfException $e) {
          	        $this->getUser()->setFlash('global_error', $e->getMessage());
          	        $this->redirect('ds');
          	    }
                
                return $this->redirect('ds_generation_operateur', $ds);
	          }
        }
    }

    public function executeLieuxStockage(sfWebRequest $request)
    {
        $this->tiers = $this->getRoute()->getTiers();
        $this->ds = DSClient::getInstance()->findOrCreateDsByCvi($this->tiers->cvi,date('Y-m-d'));     
    }
 
    public function executeStock(sfWebRequest $request) {        
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSEditionFormCiva($this->ds);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->doUpdateObject();
                $this->ds->save();
            }
        }
    }

    
    public function executeAutre(sfWebRequest $request)
    {
    }
    
    public function executeRecapLieuStockage(sfWebRequest $request)
    {
    }
    
    public function executeRecap(sfWebRequest $request)
    {
        
    }
    
}
