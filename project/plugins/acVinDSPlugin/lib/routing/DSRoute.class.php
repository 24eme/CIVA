<?php
class DSRoute extends sfObjectRoute implements InterfaceTiersRoute {

	protected $ds = null;

	protected function getObjectForParameters($parameters) {
        $matches = array();
        if (preg_match('/^DS-(C?[0-9]{10})-([0-9]{4}[0-9]{2})-([0-9]{3})$/',$parameters['id'],$matches)) {
            $identifiant = $matches[1];
            $periode = $matches[2];
            $lieu_stockage = $matches[3];
        } else {
            throw new InvalidArgumentException(sprintf('The DS "%s" is not valid.', $this->pattern, $parameters['id']));
        }

        $this->ds = DSClient::getInstance()->findByIdentifiantPeriodeAndLieuStockage($identifiant, $periode, $lieu_stockage);
        if (!$this->ds) {
            throw new sfError404Exception(sprintf('No DS found with the id "%s" and the periode "%s".',  $identifiant, $periode));
        }
        return $this->ds;
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array("id" => $object->_id);
        return $parameters;
    }

    public function getDS() {
        if (!$this->ds) {
            $this->getObject();
        }
        return $this->ds;
    }

	public function getEtablissement() {
        $etablissement = $this->getDS()->getEtablissement();

        if(sfContext::getInstance()->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
			myUser::autoSignin($etablissement->getSociete()->getMasterCompte());
		}

        return $etablissement;
    }

    public function getTiers() {

        return $this->getEtablissement();
    }
}
