<?php

class VracRoute extends sfObjectRoute
{
	const NOUVEAU = 'nouveau';
	
	protected function getObjectForParameters($parameters) 
	{
        if(!isset($parameters['numero_contrat'])) {
            throw new sfError404Exception('Numéro de contrat inexistant');
        }

    	if ($parameters['numero_contrat'] == self::NOUVEAU && $this->getUser()->getAttribute('vrac_object')) {
            return unserialize($vrac);
    	}

        if ($parameters['numero_contrat'] == self::NOUVEAU) {
            return $this->getNouveauVrac(sfContext::getInstance()->getUser());
        }

        $vrac = VracClient::getInstance()->findByNumeroContrat($parameters['numero_contrat']);

        if ($vrac) {
            
            return $vrac;
        }

        throw new sfError404Exception(sprintf("Contrat %s introuvable", $parameters['numero_contrat']));
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array();
        $parameters["numero_contrat"] = ($object->_id)? $object->numero_contrat : self::NOUVEAU;
        return $parameters;
    }
    
	public function getVrac() 
	{

    	return $this->getObject();
    }

    protected function getNouveauVrac($tiers)
    {
        $tiers = $this->getUser()->getDeclarant();
        $vrac = VracClient::getInstance()->createVrac($tiers->_id);
        $vrac->mandataire_identifiant = $tiers->_id;
        $vrac->storeMandataireInformations($tiers);
        return $vrac;
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }
}