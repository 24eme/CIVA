<?php
class TiersFictif extends _Tiers {
    
    protected $_type = null;
    
    public function __construct($type) {
        $this->_type = $type;
        parent::__construct();
    }
    
    public function save() {
        return true;
    }
    
    public function getDocumentDefinitionModel() {
        return $this->_type;
    }
}