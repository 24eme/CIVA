<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$this->tiers = $this->getUser()->getDeclarantsVrac();
        $this->vracs = VracTousView::getInstance()->findSortedByDeclarants($this->getUser()->getDeclarantsVrac());
        $this->etapes = VracEtapes::getInstance();
    }
    
}
