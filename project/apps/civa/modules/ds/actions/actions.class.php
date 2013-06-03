<?php
class dsActions extends sfActions {
    
    public function executeInit(sfWebRequest $request) {
  //      var_dump($request->getParameterHolder()); exit;
       if ($request->isMethod(sfWebRequest::POST)) {
           $this->tiers = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($request['cvi']);     
           $date = date('Y-m-d');
           $dss = DSCivaClient::getInstance()->findOrCreateDssByTiers($this->tiers,$date);
           foreach ($dss as $ds) {
               $ds->save();
           }
           $this->ds = DSCivaClient::getInstance()->getDSPrincipale($this->tiers,$date);
           $this->redirect('ds_etape_redirect', $this->ds);
        }
    } 
    
    public function executeRedirectEtape(sfWebRequest $request) {
         $this->ds = $this->getRoute()->getDS();         
         $this->tiers = $this->getRoute()->getTiers();
         $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds); 
         if((!$this->ds) || (!$this->ds->exist('num_etape')))
             throw new sfException("La DS n'existe pas ou ne possède pas de numéro d'étape");         
         switch ($this->ds->num_etape) {
             case 1:
                 $this->redirect('ds_exploitation', $this->ds);
             break;
             case 2:
                 $this->redirect("ds_lieux_stockage", $this->ds);
             break;
             default :
                 $this->redirectEtapeAfterStock($this->ds,$this->dss,$this->tiers);
             break;
         }
    }

    private function redirectEtapeAfterStock($ds,$dss,$tiers){
        $etape = $ds->num_etape;
        if((3 <= $etape) && ($etape < (3+count($dss)))){
            $pos = $etape - 3;
            $dss_id = array_keys($dss);
            $ds_id = $dss_id[$pos];
            $this->redirect('ds_edition_operateur', array('id' => $ds_id));
        }
        if(3 + count($dss) - 1 < $etape){
            $etape_without_dss = $etape - count($dss) + 1;
            if($etape_without_dss == 4){
                $this->redirect('ds_autre', $ds); 
            }
            if($etape_without_dss == 5){
                $this->redirect('ds_validation', $ds); 
            }
        }
    }


    public function executeExploitation(sfWebRequest $request)
    {
        $this->ds = $this->getRoute()->getDS(); 
        $this->tiers = $this->getRoute()->getTiers();        
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds); 
        $this->form_gest = new TiersExploitantForm($this->getUser()->getTiers()->getExploitant());
        $this->form_gest_err = 0;
        $this->form_expl = new TiersExploitationForm($this->getUser()->getTiers());
        $this->form_expl_err = 0;
        if ($request->isMethod(sfWebRequest::POST)) {
            if ($request->getParameter('gestionnaire')) {
                $this->form_gest->bind($request->getParameter($this->form_gest->getName()));
                if   ($this->form_gest->isValid()) {
                    $this->form_gest->save();
                } else {
                    $this->form_gest_err = 1;
                }
            }
            if ($request->getParameter('exploitation')) {
                $this->form_expl->bind($request->getParameter($this->form_expl->getName()));
                if ($this->form_expl->isValid()) {

                    $tiers = $this->form_expl->save();
                    // $ldap = new ldap();

                    if ($tiers) {
                        /* $values['nom'] = $tiers->nom;
                          $values['adresse'] = $tiers->siege->adresse;
                          $values['code_postal'] = $tiers->siege->code_postal;
                          $values['ville'] = $tiers->siege->commune; */
                        //$ldap->ldapModify($this->getUser()->getTiers());
                    }
                } else {
                    $this->form_expl_err = 1;
                }
            }
            if (!$this->form_gest_err && !$this->form_expl_err) {
                $this->ds->declarant->nom = $this->tiers->exploitant->nom;
                $this->ds->declarant->telephone = $this->tiers->exploitant->telephone;
                $this->ds->declarant->email = $this->tiers->email;
                $this->ds->save();
                $this->redirect('ds_exploitation', $this->ds);
            }
        }
        
        $suivant = isset($request['suivant']) && $request['suivant'];
        if($suivant){
            $this->ds->updateEtape(2);
            $this->ds->save();
            $this->redirect("ds_lieux_stockage", $this->ds);
        }
    }
    
    public function executeLieuxStockage(sfWebRequest $request)
    {
        $this->ds = $this->getRoute()->getDS();
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds); 
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSLieuxDeStockageForm($this->ds);   
        
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {
               $this->dss = $this->form->doUpdateDss($this->dss);
                foreach ($this->dss as $current_ds) {
                    $current_ds->save();
                }   
                if($request->isXmlHttpRequest())
                {            
                    return $this->renderText(json_encode(array("success" => true)));                  
                }
                $this->ds->updateEtape(3);
                $this->redirect('ds_edition_operateur', array('id' => $this->ds->_id));
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
        if(!$this->getRoute()->getNoeud()) {

            return $this->redirect('ds_edition_operateur', $this->ds->getFirstAppellation());
        }

        if($this->getRoute()->getNoeud() instanceof DSAppellation) {
            if(count($this->getRoute()->getNoeud()->getLieux()) < 1 && $this->getRoute()->getNoeud()->getConfig()->hasManyLieu()) {
                
                return $this->redirect('ds_ajout_lieu', $this->getRoute()->getNoeud());
            }

            return $this->redirect('ds_edition_operateur', $this->getRoute()->getNoeud()->getLieux()->getFirst());
        }

        $this->lieu = $this->getRoute()->getNoeud();

        if(count($this->lieu->getProduitsDetails()) < 1) {

            return $this->redirect('ds_ajout_produit', $this->lieu);
        }

        $this->form = new DSEditionFormCiva($this->ds, $this->lieu);

        $this->appellations = $this->ds->declaration->getAppellationsSorted();
        $this->appellation = $this->lieu->getAppellation();
        $this->current_lieu = null;
        $this->isFirstAppellation = ($this->ds->getFirstAppellation()->getHash() == $this->appellation->getHash());
        
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->doUpdateObject();
                $this->ds->save();
                if($request->isXmlHttpRequest())
                {            
                    return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->ds->get('_id'),"revision" => $this->ds->get('_rev')))));                  
                }
                            
                $next = $this->ds->getNextLieu($this->lieu);
                if($next){
                    $this->redirect('ds_edition_operateur', $next);
                }
                else
                {
                    $this->redirect('ds_recapitulatif_lieu_stockage', array('id' => $this->ds->_id));   
                }
            }
        }
    }

    public function executeAjoutLieu(sfWebRequest $request) {
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->appellation = $this->getRoute()->getNoeud();

        $this->config_appellation = $this->appellation->getConfig();
        $this->form = new DSEditionAddLieuFormCiva($this->ds, $this->config_appellation);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));
        
        if(!$this->form->isValid()) {
            
            return sfView::SUCCESS;
        }

        $lieu = $this->ds->addLieu($this->form->getValue('hashref'));
        $this->ds->save();

        return $this->redirect('ds_edition_operateur', $lieu);
    }

    public function executeAjoutProduit(sfWebRequest $request) {
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->lieu = $this->getRoute()->getNoeud();
        $this->config_lieu = $this->lieu->getConfig();
        $this->appellation = $this->lieu->getAppellation();
        $this->form = new DSEditionAddProduitFormCiva($this->ds, $this->config_lieu);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));
        
        if(!$this->form->isValid()) {
            
            return sfView::SUCCESS;
        }

        $this->ds->addDetail($this->form->getValue('hashref'), $this->form->getValue('lieudit'));
        $this->ds->save();

        return $this->redirect('ds_edition_operateur', $this->lieu);
    }
    
    public function executeRecapitulatifLieuStockage(sfWebRequest $request) {
        $this->ds = $this->getRoute()->getDS();
        $this->ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($this->ds);
        $this->tiers = $this->getRoute()->getTiers();
        $suivant = isset($request['suivant']) && $request['suivant'];
        if($suivant){
            $nextDs = DSCivaClient::getInstance()->getNextDS($this->ds);
            if($nextDs){
                $nextDs->updateEtape(3);
                $this->redirect('ds_edition_operateur', array('id' => $nextDs->_id,'appellation_lieu' => $nextDs->getFirstAppellation()));
            }
            else{
                $this->ds->updateEtape(4);
                $this->redirect('ds_autre', $this->ds_principale); 
            }
        }
    }
    
    public function executeAutre(sfWebRequest $request)
    {
        $this->ds = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->form = new DSEditionCivaAutreForm($this->ds);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->ds->updateEtape(5);
                $this->form->save();
                if($request->isXmlHttpRequest())
                {            
                    return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->ds->get('_id'),"revision" => $this->ds->get('_rev')))));                  
                } 
                $this->redirect('ds_validation', $this->ds); 
            }
        }
    }
    
    public function executeValidation(sfWebRequest $request)
    {
        $this->ds_principale = $this->getRoute()->getDS();
        $this->tiers = $this->getRoute()->getTiers();
        $this->ds_client = DSCivaClient::getInstance();
    }
    
}
