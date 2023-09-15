<?php

class EtablissementRoute extends sfObjectRoute implements InterfaceEtablissementRoute {

    protected $etablissement = null;

    protected function getObjectForParameters($parameters = null) {
        $this->etablissement = EtablissementClient::getInstance()->find($parameters['identifiant']);
        $myUser = sfContext::getInstance()->getUser();
        if(sfContext::getInstance()->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
			CompteRoute::autoSignin($this->getEtablissement()->getSociete()->getMasterCompte());
		}
        if ($myUser->hasTeledeclaration() && $myUser->getCompte()->getSociete()->_id != $this->getEtablissement()->getSociete()->_id) {
            throw new sfError404Exception("Vous n'avez pas le droit d'accéder à cette page");
        }
        $module = sfContext::getInstance()->getRequest()->getParameterHolder()->get('module');
        //sfContext::getInstance()->getResponse()->setTitle(strtoupper($module).' - '.$this->etablissement->nom);

        return $this->etablissement;
    }

    protected function doConvertObjectToArray($object = null) {

        return array("identifiant" => $object->getIdentifiant());
    }

    public function getEtablissement() {

	    if (!$this->etablissement) {
           $this->etablissement = $this->getObject();
      	}

        if(sfContext::getInstance()->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
			CompteRoute::autoSignin($this->etablissement->getSociete()->getMasterCompte());
		}

	    return $this->etablissement;
    }
}
