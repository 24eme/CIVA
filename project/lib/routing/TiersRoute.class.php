<?php

class TiersRoute extends sfObjectRoute implements InterfaceTiersRoute {

    protected $tiers = null;

    protected function getObjectForParameters($parameters) {
    $this->tiers = acCouchdbManager::getClient('_Tiers')->findByIdentifiant($parameters['cvi']);
      return $this->tiers;
    }

    protected function doConvertObjectToArray($object) {
        return array("cvi" => $object->getCvi());
    }

    public function getEtablissement() {
      if (!$this->tiers) {
           $this->tiers = $this->getObject();
      }
      return $this->tiers;
    }

    public function getTiers() {

      return $this->getEtablissement();
    }

}
