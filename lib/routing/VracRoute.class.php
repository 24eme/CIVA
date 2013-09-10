<?php

class VracRoute extends sfObjectRoute
{
	protected function getObjectForParameters($parameters) 
	{
        if(isset($parameters['numero_contrat'])) {
            $this->vrac = VracClient::getInstance()->findByNumeroContrat($parameters['numero_contrat']);
            return $this->vrac;
        }
        throw new sfError404Exception('Contrat vrac inexistant.');
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array();
            $parameters["numero_contrat"] = $object->numero_contrat;

        return $parameters;
    }
    
	public function getVrac() 
	{
        return $this->getObject();
    }
}