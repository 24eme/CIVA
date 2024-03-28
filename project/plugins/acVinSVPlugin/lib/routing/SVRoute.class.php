<?php
class SVRoute extends EtablissementRoute {

    protected $sv = null;

    protected function getObjectForParameters($parameters = null) {
        $this->sv = SVClient::getInstance()->find($parameters['id']);
        if (!$this->sv) {

            throw new sfError404Exception(sprintf('No SV found with the id "%s".', $parameters['id']));
        }
        parent::getObjectForParameters(array('identifiant' => $this->sv->identifiant));
        return $this->sv;
    }

    protected function doConvertObjectToArray($object = null) {
        $parameters = array("id" => $object->_id);
        return $parameters;
    }

    public function getEtablissement() {
        $etablissement = $this->getSV()->getEtablissement();

        if(sfContext::getInstance()->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
            sfContext::getInstance()->getUser()->autoSignin($etablissement->getSociete()->getMasterCompte());
        }

        return $etablissement;
    }

    public function getSV() {
        if (!$this->sv) {
            $this->getObject();
        }
        return $this->sv;
    }

}
