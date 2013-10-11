<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$this->user = $this->getUser()->getDeclarant();
        $this->vracs = VracTousView::getInstance()->findSortedBy($this->user->_id);
        $this->etapes = VracEtapes::getInstance();
        $this->campagne = ConfigurationClient::getInstance()->buildCampagne(date('Y-m-d'));
    }
    
}
