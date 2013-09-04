<?php
class vracActions extends sfActions {

	public function executeIndex(sfWebRequest $request) 
	{
		
    }
    
	public function executeNouveau(sfWebRequest $request) 
	{
		$tiers = $this->getUser()->getDeclarant();
		$vrac = VracClient::getInstance()->createVrac();
		$vrac->etape = 'conditions';
		$vrac->save();
		return $this->redirect('vrac_etape_conditions', array('sf_subject' => $vrac));
    }
    
    public function executeEtapeConditions(sfWebRequest $request) 
    {
    	$this->vrac = $this->getRoute()->getVrac();
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
    }
}
