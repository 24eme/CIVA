<?php

class LS extends BaseLienSymbolique {

    public function getTypeDocumentReferant(){

        return sfCouchdbManager::getClient()->getDoc($this->getPointeur())->getDefinition();
    }

    public function getDocumentPointe(){
        return sfCouchdbManager::getClient()->getDoc($this->getPointeur());
    }

}