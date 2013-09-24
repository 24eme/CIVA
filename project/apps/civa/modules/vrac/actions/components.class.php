<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$this->user = $this->getUser()->getDeclarant();
        $this->vracs = VracTousView::getInstance()->findBy($this->user->_id, ConfigurationClient::getInstance()->buildCampagne(date('Y-m-d')));
        $this->etapes = VracEtapes::getInstance();
    }
    
}
