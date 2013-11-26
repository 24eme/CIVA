<?php
class VracProduitAjoutValidator extends sfValidatorBase 
{
    
    public function configure($options = array(), $messages = array()) 
    {
        $this->addMessage('exist', "Produit déjà présent dans la liste.");
        $this->addMessage('lieu_dit_required', "Le lieu dit est obligatoire");
    }

    protected function doClean($values) 
    {
    	if (isset($values['vrac']) && !empty($values['vrac'])) {
    		if (isset($values['hash']) && !empty($values['hash'])) {
    			if ($vrac = VracClient::getInstance()->find($values['vrac'])) {
    				if ($vrac->exist($values['hash'])) {
    					$node = $vrac->get($values['hash']);
    					if ($node instanceof VracCepage) {
    						foreach ($node->getProduitsDetails() as $detail) {
	    						$vtsgn = (isset($values['vtsgn']) && !empty($values['vtsgn']))? $values['vtsgn'] : null;
	    						$lieu_dit = (isset($values['lieu_dit']) && !empty($values['lieu_dit']))? $values['lieu_dit'] : null;
	    						if (KeyInflector::slugify($detail->vtsgn) == KeyInflector::slugify($vtsgn) && KeyInflector::slugify($detail->lieu_dit) == KeyInflector::slugify($lieu_dit)) {
	    							throw new sfValidatorErrorSchema($this, array('hash' => new sfValidatorError($this, 'exist')));
	    						}
    						}
    					}
    				}
					$pattern = '/appellation_[a-zA-Z0-9]+/';
					if (preg_match($pattern, $values['hash'], $matches)) {
						$config = acCouchdbManager::getClient('Configuration')->retrieveConfiguration();
    					$appellationsLieuDit = array_keys($config->getAppellationsLieuDit()); 
    					$lieu_dit = (isset($values['lieu_dit']) && !empty($values['lieu_dit']))? $values['lieu_dit'] : null;   							
    					if(in_array($matches[0], $appellationsLieuDit) && !$lieu_dit) {
    						throw new sfValidatorErrorSchema($this, array('hash' => new sfValidatorError($this, 'lieu_dit_required')));
    					}
					}
    			}
    			
    		}
    	}
    	return $values;
    }
}