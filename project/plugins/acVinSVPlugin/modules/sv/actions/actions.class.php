<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();

        $this->sv = SVClient::getInstance()->find('SV-'.$this->etablissement->identifiant.'-2011');

        if(!$this->sv) {
            $this->sv = SVClient::getInstance()->createFromDR($this->etablissement->identifiant, "2021");
            $this->sv->save();
        }

        return $this->redirect('sv_apporteurs', $this->sv);
    }

    public function executeExploitation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
    }

    public function executeApporteurs(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
    }

    public function executeSaisie(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
        $this->form = new SVSaisieForm($this->sv);
    }

}
