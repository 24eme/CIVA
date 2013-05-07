<?php

class TiersSessionRoute extends sfObjectRoute {


    protected function getObjectForParameters($parameters = null) {
	   return $this->getTiers();
    }

    protected function doConvertObjectToArray($object = null) {
        $parameters = array("identifiant" => $this->getTiers()->getIdentifiant());
        return $parameters;
    }

    public function getEtablissement() {
	   return sfContext::getInstance()->getUser()->getTiers();
    }
}
