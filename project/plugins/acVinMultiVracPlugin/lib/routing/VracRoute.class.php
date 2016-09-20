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
            return unserialize($this->getUser()->getAttribute('vrac_object'));
        }

        if ($parameters['numero_contrat'] == self::NOUVEAU) {
            return $this->getNouveauVrac($parameters);
        }

        $vrac = VracClient::getInstance()->findByNumeroContrat($parameters['numero_contrat']);

        if ($vrac) {

            return $vrac;
        }

        throw new sfError404Exception(sprintf("Contrat %s introuvable", $parameters['numero_contrat']));
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array();
        $parameters["numero_contrat"] = ($object && !$object->isNew()) ? $object->numero_contrat : self::NOUVEAU;
        return $parameters;
    }

	public function getVrac()
	{

    	return $this->getObject();
    }

    protected function getNouveauVrac($parameters)
    {
        $acteur = $parameters['acteur'];
        $identifiant = $parameters['identifiant'];
        $etablissement = EtablissementClient::getInstance()->find($identifiant);

        $this->vrac = VracClient::getInstance()->createVrac($etablissement->_id);
        if ($etablissement->getFamille() == EtablissementFamilles::FAMILLE_COURTIER) {
            $this->vrac->mandataire_identifiant = $etablissement->_id;
            $this->vrac->storeMandataireInformations($etablissement);
        }elseif($acteur == 'acheteur') {
            $this->vrac->acheteur_identifiant = $etablissement->_id;
            $this->vrac->storeAcheteurInformations($etablissement);            
        }elseif($acteur == 'vendeur') {
            $this->vrac->vendeur_identifiant = $etablissement->_id;
            $this->vrac->storeVendeurInformations($etablissement);
        } else {
            throw new sfException('Ce tiers ne peut pas créer de contrat vrac.');
        }
        return $this->vrac;
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }
}
