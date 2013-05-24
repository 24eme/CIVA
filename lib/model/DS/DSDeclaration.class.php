<?php
/**
 * Model for DSDeclaration
 *
 */

class DSDeclaration extends BaseDSDeclaration {
    
    public function getChildrenNode() {

        return $this->getCertifications();
    }

    public function getCertifications() {

        return $this->filter('^certification');
    }
    
    public function getAppellations() {

        return $this->getChildrenNodeDeep(2)->getAppellations();
    }

    public function getAppellationsSorted() {

        return $this->getChildrenNodeDeep(2)->getAppellationsSorted();
    }
    
}