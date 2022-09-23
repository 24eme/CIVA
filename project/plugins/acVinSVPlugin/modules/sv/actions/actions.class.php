<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();

        $this->sv = SVClient::getInstance()->find('SV-'.$this->etablissement->identifiant.'-2021');

        if(!$this->sv) {
            $this->sv = SVClient::getInstance()->createFromDR($this->etablissement->identifiant, "2021");
            $this->sv->save();
        }
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
        $this->cvi = $request->getParameter('cvi', null);
        $this->type = $request->getParameter('type', "SV12");

        $this->form = new SVSaisieForm($this->sv, $this->cvi, $this->type);

        if (!$request->isMethod(sfWebRequest::POST)) {
        	return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
	    }

        $this->form->save();

        if($this->cvi) {
            $finded = false;
            foreach($this->sv->apporteurs as $cvi => $apporteur) {
                if($finded) {

                    return $this->redirect('sv_saisie', array('sf_subject' => $this->sv, 'cvi' => $cvi));
                }
                $finded = ($cvi == $this->cvi);
            }
        }

        return $this->redirect('sv_validation', $this->sv);
    }

    public function executeValidation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
    }
}
