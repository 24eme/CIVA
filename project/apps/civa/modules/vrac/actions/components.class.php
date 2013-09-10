<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) 
	{
		$tiers = $this->getUser()->getDeclarant();
        $this->vracs = VracTousView::getInstance()->findByIdentifiant($tiers->_id);//array('item1','item2','item3','item4',);
    }
    
}
