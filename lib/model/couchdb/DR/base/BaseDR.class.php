<?php

class BaseDR extends sfCouchdbDocument {
    public function setupDefinition() {
        $this->_definition = sfCouchdbManager::getDefinition('DR');
    }
}
