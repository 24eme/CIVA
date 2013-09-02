<?php

class vracComponents extends sfComponents {
    
	public function executeMonEspace(sfWebRequest $request) {
		/***
		 * @TODO Récupération des contrats vrac
		 */
        $this->vracs = array('item1','item2','item3','item4',);
    }
    
}
