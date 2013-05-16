<?php

class TiersRoute extends sfObjectRoute implements InterfaceTiersRoute {

    protected $tiers = null;
    
    protected function getObjectForParameters($parameters) {
    $this->tiers = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($parameters['cvi']);
      return $this->tiers;
    }

    protected function doConvertObjectToArray($object) {  
        return array("cvi" => $object->getCvi());
    }

    public function getTiers() {
      if (!$this->tiers) {
           $this->tiers = $this->getObject();
      }
      return $this->tiers;
    }
}
