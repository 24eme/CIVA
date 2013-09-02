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
    }
}
