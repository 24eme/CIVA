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
        $parameters["numero_contrat"] = (!$object->isNew()) ? $object->numero_contrat : self::NOUVEAU;
        return $parameters;
    }

	public function getVrac()
	{

    	return $this->getObject();
    }

    protected function getNouveauVrac()
    {
        $tiers = VracClient::getInstance()->getFirstEtablissement($this->getUser()->getCompte()->getSociete());
        $vrac = VracClient::getInstance()->createVrac($tiers->_id);
        if ($tiers->getFamille() == EtablissementFamilles::FAMILLE_COURTIER) {
            $vrac->mandataire_identifiant = $tiers->_id;
            $vrac->storeMandataireInformations($tiers);
        } /*elseif ($tiers->type == 'Acheteur') {
            $vrac->acheteur_identifiant = $tiers->_id;
            $vrac->storeAcheteurInformations($tiers);
            $vrac->setAcheteurQualite($tiers->qualite_categorie);
        } elseif($tiers->type == 'MetteurEnMarche' && !$tiers->hasAcheteur()) {
            $vrac->acheteur_identifiant = $tiers->_id;
            $vrac->storeAcheteurInformations($tiers);
            $vrac->setAcheteurQualite($tiers->qualite_categorie);
        } else {
            throw new sfException('Ce tiers ne peut pas créer de contrat vrac.');
        }*/
        return $vrac;
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }
}
