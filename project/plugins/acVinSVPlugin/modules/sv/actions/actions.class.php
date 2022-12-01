<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();

        if ($sv = SVClient::getInstance()->findByIdentifiantAndCampagne($this->etablissement->identifiant, '2021')) {
            $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getFirst()],
                ['id' => $sv->_id]
            );
        }

        $this->formCreation = new SVCreationForm($this->etablissement->identifiant, "2021");

        if (! $request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }
        $this->formCreation->bind($request->getParameter($this->formCreation->getName()), $request->getFiles($this->formCreation->getName()));

        if (! $this->formCreation->isValid()) {
            return sfView::SUCCESS;
        }

        $typeCreation = $this->formCreation->process();

        if ($typeCreation === 'DR') {
            $sv = SVClient::getInstance()->createFromDR($this->etablissement->identifiant, "2021");
            $sv->save();

            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getFirst()],
                ['id' => $sv->_id]
            );
        }

        return $this->redirect(
            'sv_csv_verify',
            ['identifiant' => $this->etablissement->identifiant, 'campagne' => "2021", 'hash' => $typeCreation]
        );
    }

    public function executeVerify(sfWebRequest $request)
    {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->verify = SVClient::getInstance()->checkCSV(
            sfConfig::get('sf_data_dir').'/upload/'.$request->getParameter('hash'),
            $this->etablissement->cvi,
            $request->getParameter('campagne')
        );

        if (empty($this->verify)) {
            $sv = SVClient::getInstance()->createFromCSV(
                $this->etablissement->identifiant,
                $request->getParameter('campagne'),
                sfConfig::get('sf_data_dir').'/upload/'.$request->getParameter('hash')
            );

            $sv->save();

            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getFirst()],
                ['id' => $sv->_id]
            );
        }
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

        $this->form = new SVSaisieForm($this->sv, $this->cvi);

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
        return $this->redirect('sv_stockage', $this->sv);
    }

    public function executeStockage(sfWebRequest $request)
    {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();

        $this->recapProduits = $this->sv->getRecapProduits();
        $this->form = new SVStockageForm($this->sv);

        if (!$request->isMethod(sfWebRequest::POST)) {
        	return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
	    }
    }

    public function executeValidation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
    }

    public function executePdf(sfWebRequest $request)
    {
        $sv = $this->getRoute()->getSV();
        $this->document = new ExportSVPdf($sv, $request->getParameter('output', 'pdf'));
        $this->document->setPartialFunction(array($this, 'getPartial'));

        if ($request->getParameter('force')) {
            $this->document->removeCache();
        }

        $this->document->generatePDF();
        $this->document->addHeaders($this->getResponse());
        return $this->renderText($this->document->output());
    }
}
