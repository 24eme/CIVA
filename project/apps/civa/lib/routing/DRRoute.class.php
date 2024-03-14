<?php
class DRRoute extends sfObjectRoute implements InterfaceTiersRoute {

	protected $dr = null;

	protected function getObjectForParameters($parameters) {
        $matches = array();
        if (!preg_match('/^DR-([0-9]{10})-([0-9]{4})$/', $parameters['id'], $matches)) {
            throw new InvalidArgumentException(sprintf("L'identifiant de la DR n'est pas valide : \"\"", $parameters['id']));
        }

        $this->dr = DRClient::getInstance()->find($parameters['id']);
        if (!$this->dr) {
            throw new sfError404Exception(sprintf("La DR %s n'a pas été trouvée",  $parameters['id']));
        }
        return $this->dr;
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array("id" => $object->getDocument()->_id);

        return $parameters;
    }

    public function getDR() {
        if (!$this->dr) {
            $this->getObject();
        }

        return $this->dr;
    }

    public function getEtablissement() {
        $etablissement = $this->getDR()->getEtablissement();

        if(sfContext::getInstance()->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
			myUser::autoSignin($etablissement->getSociete()->getMasterCompte());
		}

        return $etablissement;
    }

    public function getTiers() {

        return $this->getEtablissement();
    }
}
