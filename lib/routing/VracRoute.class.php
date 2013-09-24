<?php

class VracRoute extends sfObjectRoute
{
	const NOUVEAU = 'nouveau';
	
	protected function getObjectForParameters($parameters) 
	{
        if(isset($parameters['numero_contrat'])) {
        	if ($parameters['numero_contrat'] == self::NOUVEAU) {
        		return null;
        	}
            $this->vrac = VracClient::getInstance()->findByNumeroContrat($parameters['numero_contrat']);
            return $this->vrac;
        }
        throw new sfError404Exception('Contrat vrac inexistant.');
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array();
        $parameters["numero_contrat"] = ($object->numero_contrat)? $object->numero_contrat : self::NOUVEAU;
        return $parameters;
    }
    
	public function getVrac() 
	{
		try {
        	return $this->getObject();
		} catch (Exception $e) {
			return null;
		}
    }
}