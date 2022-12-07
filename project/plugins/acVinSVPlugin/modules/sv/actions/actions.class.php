<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $campagne = CurrentClient::getCurrent()->campagne;

        if ($sv = SVClient::getInstance()->findByIdentifiantAndCampagne($this->etablissement->identifiant, $campagne)) {
            $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getFirst()],
                ['id' => $sv->_id]
            );
        }

        $this->formCreation = new SVCreationForm($this->etablissement->identifiant, $campagne);

        if (! $request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }
        $this->formCreation->bind($request->getParameter($this->formCreation->getName()), $request->getFiles($this->formCreation->getName()));

        if (! $this->formCreation->isValid()) {
            return sfView::SUCCESS;
        }

        $typeCreation = $this->formCreation->process();

        if ($typeCreation === 'DR') {
            $sv = SVClient::getInstance()->createFromDR($this->etablissement->identifiant, $campagne);
            $sv->save();

            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getFirst()],
                ['id' => $sv->_id]
            );
        }

        if ($typeCreation === 'VIERGE') {
            $sv = SVClient::getInstance()->createSV($this->etablissement->identifiant, $campagne);
            $sv->save();

            return $this->redirect('sv_validation', ['id' => $sv->_id]);
        }

        return $this->redirect(
            'sv_csv_verify',
            ['identifiant' => $this->etablissement->identifiant, 'campagne' => $campagne, 'hash' => $typeCreation]
        );
    }

    public function executeVerify(sfWebRequest $request)
    {
        $this->etablissement = $this->getRoute()->getEtablissement();

        $filepath = sfConfig::get('sf_data_dir').'/upload/'.$request->getParameter('hash');
        $this->csv = new CsvFileAcheteur($filepath);

        $this->verify = SVClient::getInstance()->checkCSV(
            $this->csv,
            $this->etablissement->cvi,
            $request->getParameter('campagne')
        );

        if (empty($this->verify)) {
            $sv = SVClient::getInstance()->createFromCSV(
                $this->etablissement->identifiant,
                $request->getParameter('campagne'),
                $this->csv
            );

            $sv->save();

            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance()->getFirst()],
                ['id' => $sv->_id]
            );
        }
    }

    public function executeApporteurs(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide()) { return $this->redirect('sv_validation', ['id' => $this->sv->_id]); }
    }

    public function executeSaisie(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
        $this->cvi = $request->getParameter('cvi', null);

        if ($this->sv->isValide()) { return $this->redirect('sv_validation', ['id' => $this->sv->_id]); }

        if ($this->cvi && $cvi_index = array_search($this->cvi, array_keys($this->sv->apporteurs->toArray()))) {
            $this->cvi_precedent = $this->sv->apporteurs->get($this->cvi)->getPreviousSister()->getKey();
        }

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

        return $this->redirect(
            SVEtapes::$links[SVEtapes::ETAPE_APPORTEURS],
            ['id' => $this->sv->_id]
        );
    }

    public function executeAutres(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide()) { return $this->redirect('sv_validation', ['id' => $this->sv->_id]); }

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

        if ($this->sv->isValide()) { return $this->redirect('sv_validation', ['id' => $this->sv->_id]); }

        $this->recapProduits = $this->sv->getRecapProduits();
        $this->form = new SVStockageForm($this->sv);

        if (!$request->isMethod(sfWebRequest::POST)) {
        	return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
	    }

        $this->form->save();

        return $this->redirect('sv_validation', $this->sv);
    }

    public function executeValidation(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->sv = $this->getRoute()->getSV();
        $this->svvalidation = new SVValidation($this->sv);

        if (! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        if ($this->svvalidation->isValide() === false) {
            return sfView::SUCCESS;
        }

        $this->sv->validate();
        $this->sv->save();

        return $this->redirect('sv_validation', $this->sv);
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
