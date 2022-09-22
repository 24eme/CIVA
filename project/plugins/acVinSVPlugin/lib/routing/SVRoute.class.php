<?php

class SVRoute extends sfObjectRoute implements InterfaceEtablissementRoute {

    protected $sv = null;

    protected function getObjectForParameters($parameters) {

        $this->sv = SVClient::getInstance()->find('SV-'.$parameters['identifiant'].'-'.$parameters['periode_version']);

        if (!$this->sv) {

            throw new sfError404Exception(sprintf("La SV n'a pas été trouvée"));
        }

        $control = isset($this->options['control']) ? $this->options['control'] : array();

        if (in_array('valid', $control) && !$this->sv->isValidee()) {

            throw new sfException('La SV doit être validée');
        }

        if (in_array('edition', $control) && $this->sv->isValidee()) {

            throw new sfException('La SV ne peut pas être éditée car elle est validée');
        }

        return $this->sv;
    }

    protected function doConvertObjectToArray($object) {
        $parameters = array("identifiant" => $object->getIdentifiant(), "periode_version" => $object->getPeriodeAndVersion());
        return $parameters;
    }

    public function getSV() {
        if (!$this->sv) {
            $this->getObject();
        }

        return $this->sv;
    }

    public function getEtablissement() {

        return $this->getSV()->getEtablissementObject();
    }

}