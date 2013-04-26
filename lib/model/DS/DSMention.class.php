<?php
/**
 * Model for DSMention
 *
 */

class DSMention extends BaseDSMention {
    
    public function getAppellation(){

        return $this->getParent();
    }

    public function getChildrenNode() {

        return $this->getLieux();
    }

    public function getLieux(){

        return $this->filter('^lieu');
    }
    
}