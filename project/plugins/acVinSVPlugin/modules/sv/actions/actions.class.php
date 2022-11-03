<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();

        if ($sv = SVClient::getInstance()->findByIdentifiantAndCampagne($this->etablissement->identifiant, '2021')) {
            $this->redirect('sv_exploitation', ['id' => $sv->_id]);
        }

        $this->formCreation = new SVCreationForm($this->etablissement->identifiant, "2021");

        if (! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        $this->formCreation->bind($request->getParameter($this->formCreation->getName()));

        if (! $this->formCreation->isValid()) {
            return sfView::SUCCESS;
        }

        $sv = $this->formCreation->save();

        $this->redirect('sv_exploitation', ['id' => $sv->_id]);
    }

    public function executeExploitation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
    }

    public function executeExtraction(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();

        if (SVEtapes::getInstance()->isEtapeDisabled(SVEtapes::ETAPE_EXTRACTION, $this->sv)) {
            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getNext(SVEtapes::ETAPE_EXTRACTION)],
                $this->sv
            );
        }

        $this->form = new SVExtractionForm($this->sv);

        if (! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (! $this->form->isValid()) {
            return sfView::SUCCESS;
        }

        $this->form->save();
        $this->redirect('sv_apporteurs', ['id' => $this->sv->_id]);
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

    public function executeAutres(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
        $this->form = new SVAutreForm($this->sv);

        if (! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (! $this->form->isValid()) {
            return sfView::SUCCESS;
        }

        $this->form->save();
        return $this->redirect('sv_validation', $this->sv);
    }

    public function executeValidation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
    }
}
