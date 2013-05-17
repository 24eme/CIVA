<?php
class dsActions extends sfActions {
    
    public function executeIndex(sfWebRequest $request) {
       if ($request->isMethod(sfWebRequest::POST)) {
           $this->tiers = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($request['cvi']);           
           $dss = DSCivaClient::getInstance()->findOrCreateDssByTiers($this->tiers,date('Y-m-d'));
           foreach ($dss as $ds) {
               $ds->save();
           }
           $this->redirect('ds_lieux_stockage', $this->tiers);
        }
    } 

    public function executeLieuxStockage(sfWebRequest $request)
    {
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSLieuxDeStockageForm($this->tiers);   
        $this->dss = DSCivaClient::getInstance()->findDssByCvi($this->tiers, date('Y-m-d')); 
        $this->ds = DSCivaClient::getInstance()->getDSPrincipale($this->tiers, date('Y-m-d'));
        
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {
               $this->dss = $this->form->doUpdateDss($this->dss);
                foreach ($this->dss as $current_ds) {
                    $current_ds->save();
                }
                $this->redirect('ds_edition_operateur', array('id' => $this->ds->_id,'appellation_lieu' => $this->ds->getFirstAppellationLieu()));
            }
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

 
    public function executeStock(sfWebRequest $request) {        
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->appellation_lieu = $request['appellation_lieu'];
        $this->form = new DSEditionFormCiva($this->ds,$this->appellation_lieu);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->doUpdateObject();
                $this->ds->save();
                $next = $this->ds->getNextAppellationLieu($this->appellation_lieu);
                if($next){
                    $this->redirect('ds_edition_operateur', array('id' => $this->ds->_id,'appellation_lieu' => $next));
                }
                else
                {
                    $this->redirect('ds_recapitulatif_lieu_stockage', array('id' => $this->ds->_id));   
                }
            }
        }
    }
    
    public function executeStockRetour(sfWebRequest $request) {        
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->appellation = $request['appellation_lieu'];
        $this->previous_appellationLieu = $this->ds->getPreviousAppellationLieu($this->appellation);
        if($this->previous_appellationLieu){
            $this->redirect('ds_edition_operateur', array('id' => $this->ds->_id,'appellation_lieu' => $this->previous_appellationLieu));
        }
        else{
           $this->redirect('ds_lieux_stockage', $this->tiers); 
        }
    } 
    
    public function executeRecapitulatifLieuStockage(sfWebRequest $request) {
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $suivant = isset($request['suivant']) && $request['suivant'];
        if($suivant){
            $nextDs = DSCivaClient::getInstance()->getNextDS($this->ds);
            if($nextDs){
                $this->redirect('ds_edition_operateur', array('id' => $nextDs->_id,'appellation_lieu' => $nextDs->getFirstAppellationLieu()));
            }
            else{
                $this->redirect('ds_autre', $this->tiers); 
            }
        }
    }
    

    
    public function executeAutre(sfWebRequest $request)
    {
        $this->tiers = $this->getRoute()->getTiers();
        $this->ds = DSCivaClient::getInstance()->getDSPrincipale($this->tiers, date('Y-m-d'));
        $this->form = new DSEditionCivaAutreForm($this->ds);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirect('ds_validation', $this->tiers); 
            }
        }
    }
    
    public function executeValidation(sfWebRequest $request)
    {
        $this->tiers = $this->getRoute()->getTiers();
        $this->ds_client = DSCivaClient::getInstance();
        $this->ds_principale = $this->ds_client->getDSPrincipale($this->tiers, date('Y-m-d'));
        
    }
    
    public function executeRecapLieuStockage(sfWebRequest $request)
    {
    }
    
    public function executeRecap(sfWebRequest $request)
    {
        
    }
    
}
