<?php

class TiersRoute extends sfObjectRoute implements InterfaceTiersRoute {

    protected $tiers = null;
    
    protected function getObjectForParameters($parameters = null) {
    $this->tiers = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($parameters['cvi']);
      return $this->tiers;
    }

    protected function doConvertObjectToArray($object = null) {
  
        return array("cvi" => $object->getIdentifiant());
    }

    public function getTiers() {
      if (!$this->tiers) {
           $this->tiers = $this->getObject();
      }
      return $this->tiers;
    }
}
