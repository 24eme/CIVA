<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = SVClient::getInstance()->createFromDR($this->etablissement->identifiant, "2021");

        print_r($this->sv);
        exit;
    }

}
