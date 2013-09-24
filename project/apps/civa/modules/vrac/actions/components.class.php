<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$tiers = $this->getUser()->getDeclarant();
        $this->vracs = VracTousView::getInstance()->findBy($tiers->_id, ConfigurationClient::getInstance()->buildCampagne(date('Y-m-d')));
        $this->etapes = VracEtapes::getInstance();
    }
    
}
