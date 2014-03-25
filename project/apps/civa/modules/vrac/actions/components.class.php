<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$this->user = $this->getUser()->getDeclarantVrac();
        $this->vracs = VracTousView::getInstance()->findSortedByDeclarants($this->getUser()->getDeclarantsVrac());
        $this->etapes = VracEtapes::getInstance();
        $this->campagne = ConfigurationClient::getInstance()->buildCampagne(date('Y-m-d'));
    }
    
}
