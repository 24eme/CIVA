<?php
class vracActions extends sfActions {

	public function executeIndex(sfWebRequest $request) 
	{
		
    }
    
    public function executeAcces(sfWebRequest $request)
    {
    	$vrac = $this->getRoute()->getVrac();
    	$route = null;
    	switch ($vrac->etape) {
    		case VracEtapes::ETAPE_SOUSSIGNES:
    			$route = 'vrac_etape_soussignes';
    			break;
    		case VracEtapes::ETAPE_CONDITIONS:
    			$route = 'vrac_etape_conditions';
    			break;
    		case VracEtapes::ETAPE_VALIDATION:
    			$route = 'vrac_etape_validation';
    			break;
    		default:
    			$route = null;
    			break;
    	}
    	if (!$route) {
    		throw new sfError404Exception('Etape "'.$vrac->etape.'" non connu');
    	}
    	return $this->redirect($route, array('sf_subject' => $vrac));
    }
    
	public function executeNouveau(sfWebRequest $request) 
	{
		$tiers = $this->getUser()->getDeclarant();
		$vrac = VracClient::getInstance()->createVrac($tiers->_id);
		$vrac->etape = VracEtapes::getInstance()->getFirst();
		$vrac->save();
		return $this->redirect('vrac_etape_soussignes', array('sf_subject' => $vrac));
    }
    
	public function executeSupprimer(sfWebRequest $request) 
	{
		$this->vrac = $this->getRoute()->getVrac();
		$this->vrac->delete();
		return $this->redirect('mon_espace_civa');
    }
    
    public function executeEtapeSoussignes(sfWebRequest $request) 
    {
    	$this->vrac = $this->getRoute()->getVrac();
    	$this->etapes = VracEtapes::getInstance();
    	$annuaire = $this->getAnnuaire();
    	$this->form = new VracSoussignesForm($this->vrac, $annuaire);
    	if ($nextEtape = $this->getEtapeSuivante($this->vrac, $this->etapes)) {
    		$this->vrac->etape = $nextEtape;
    	}
    	if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
        		return $this->redirect('vrac_etape_conditions', array('sf_subject' => $this->vrac));
        	}
        }
    }
    
    public function executeEtapeConditions(sfWebRequest $request) 
    {
    	$this->vrac = $this->getRoute()->getVrac();
    	$this->etapes = VracEtapes::getInstance();
    	if ($nextEtape = $this->getEtapeSuivante($this->vrac, $this->etapes)) {
    		$this->vrac->etape = $nextEtape;
    	}
    	$this->form = new VracForm($this->vrac);
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			return $this->redirect('vrac_etape_validation', array('sf_subject' => $this->vrac));
        	}
        }
    }
    
    public function executeEtapeValidation(sfWebRequest $request) 
    {
    	$this->vrac = $this->getRoute()->getVrac();
    	$this->etapes = VracEtapes::getInstance();
    	if ($nextEtape = $this->getEtapeSuivante($this->vrac, $this->etapes)) {
    		$this->vrac->etape = $nextEtape;
    	}
    	$this->form = new VracValidationForm($this->vrac->valide);
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			return $this->redirect('mon_espace_civa');
        	}
        }
    }
    
    protected function getEtapeSuivante($vrac, $etapes)
    {
    	$next = null;
    	$nextEtape = $etapes->getNext($vrac->etape);
    	if ($nextEtape && $etapes->isLt($vrac->etape, $nextEtape)) {
    		$next = $nextEtape;
    	}
    	return $next;
    }
    
    protected function getAnnuaire()
    {
    	$compte = $this->getUser()->getCompte();
		return AnnuaireClient::getInstance()->findOrCreateAnnuaire($compte->login);
    }
}
