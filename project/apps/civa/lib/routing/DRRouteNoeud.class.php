<?php
class DRRouteNoeud extends DRRoute {

    protected $noeud = null;

	protected function getObjectForParameters($parameters) {
        parent::getObjectForParameters($parameters);

        if(!$parameters['hash']) {
            throw new sfException("La hash du produit est requise");
        }

        $this->noeud = $this->dr->get($parameters['hash']);
    }

    protected function getNoeud() {
        if (!$this->noeud) {
            $this->getObject();
        }

        return $this->noeud;
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array("id" => $object->getDocument()->_id, 'hash' => $object->getHash());

        return $parameters;
    }

}
