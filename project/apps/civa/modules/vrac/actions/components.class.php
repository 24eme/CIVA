<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
    	$this->getUser()->setAttribute('vrac_type_tiers', null);
		$this->tiers = $this->getUser()->getDeclarantsVrac();
        //$this->hasDoubt = $this->getUser()->getDeclarantVrac()->type != 'Courtier';
		$this->hasDoubt = false;
        $this->vracs = VracTousView::getInstance()->findSortedByDeclarants($this->getUser()->getDeclarantsVrac());
        $this->etapes = VracEtapes::getInstance();
    }
    
}
