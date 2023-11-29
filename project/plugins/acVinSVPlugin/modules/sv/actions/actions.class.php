<?php

class svActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
    }

    public function executeEtablissement(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $campagne = CurrentClient::getCurrent()->campagne;

        if ($sv = SVClient::getInstance()->findByIdentifiantAndCampagne($this->etablissement->identifiant, $campagne)) {
            $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance($sv->type)->getFirst()],
                ['id' => $sv->_id]
            );
        }

        $this->formCreation = new SVCreationForm($this->etablissement->cvi);

        if (! $request->isMethod(sfWebRequest::POST)) {
            throw new sfException('Ne peut pas être appelé en GET');
        }
        $this->formCreation->bind($request->getParameter($this->formCreation->getName()), $request->getFiles($this->formCreation->getName()));

        if (! $this->formCreation->isValid()) {
            throw new sfException('Formulaire SVCreationForm invalide : '.implode(', ', $this->formCreation->getErrors()['_globals']));
        }

        $typeCreation = $this->formCreation->process();

        if ($typeCreation === 'DR') {
            $sv = SVClient::getInstance()->createFromDR($this->etablissement->identifiant, $campagne);
            $sv->save();

            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance($sv->type)->getFirst()],
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

        $this->sv = SVClient::getInstance()->createSV(
            $this->etablissement->identifiant,
            $request->getParameter('campagne'),
        );

        $this->verify = SVClient::getInstance()->checkCSV(
            $this->csv,
            $this->etablissement->cvi,
            $request->getParameter('campagne'),
            SVClient::getTypeByEtablissement($this->etablissement)
        );

        if (empty($this->verify)) {
            $this->sv = SVClient::getInstance()->createFromCSV(
                $this->etablissement->identifiant,
                $request->getParameter('campagne'),
                $this->csv
            );

            $this->sv->save();

            return $this->redirect(
                SVEtapes::$links[SVEtapes::getInstance($this->sv->type)->getFirst()],
                ['id' => $this->sv->_id]
            );
        }
    }

    public function executeApporteurs(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();
        $this->hasCVI = $request->getParameter('addCVI', null);
        $this->form = new SVAjoutApporteurForm($this->sv, ['cvi' => $this->hasCVI]);

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }
    }

    public function executeAjoutApporteur(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();
        $this->hasCVI = $request->getParameter('addCVI', null);
        $this->form = new SVAjoutApporteurForm($this->sv, ['cvi' => $this->hasCVI]);

        $this->form->bind($request->getParameter($this->form->getName()));

        if (! $this->form->isValid()) {
            $msg = [];
            foreach ($this->form->getErrorSchema() as $e) {
                $msg[] = $e->getMessage();
            }
            $this->getUser()->setFlash('error_msg', "Erreur lors de l'ajout de l'apporteur ".$this->form->getValue('cvi')." : ".implode(',', $msg));
            return $this->redirect('sv_apporteurs', ['id' => $this->sv->_id]);
        }

        if (! $this->hasCVI) {
            return $this->redirect('sv_apporteurs', ['id' => $this->sv->_id, 'addCVI' => $this->form->getValues()['cvi']]);
        }

        $this->form->save();

        $this->getUser()->setFlash('success_msg', "Apporteur ajouté : ".$this->form->getValue('cvi'));
        $this->redirect('sv_apporteurs', ['id' => $this->sv->_id]);
    }

    public function executeAjoutProduitApporteur(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();
        $this->cvi = $request->getParameter('cvi');
        $this->form = new SVAjoutProduitApporteurForm($this->sv, $this->cvi);

        $this->form->bind($request->getParameter($this->form->getName()));

        if ($this->form->isValid()) {
            $this->form->save();
        }

        return $this->redirect('sv_saisie', ['id' => $this->sv->_id, 'cvi' => $this->cvi]);
    }

    public function executeExtraction(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

        if($this->sv->isFromCSV()) { return $this->redirect('sv_revendication', ['id' => $this->sv->_id]); }

        if($this->sv->type != SVClient::TYPE_SV12) { return $this->redirect('sv_autres', ['id' => $this->sv->_id]); }

        $this->form = new SVExtractionForm($this->sv);

        if (! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (! $this->form->isValid()) {
            return sfView::SUCCESS;
        }

        $this->form->save();

        $this->redirect('sv_revendication', ['id' => $this->sv->_id]);
    }

    public function executeRevendication(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();

        if($this->sv->type != SVClient::TYPE_SV12) { return $this->redirect('sv_autres', ['id' => $this->sv->_id]); }

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

    }


    public function executeSaisie(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();
        $this->cvi = $request->getParameter('cvi', null);
        $this->modal = $request->getParameter('modal', null);

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

        if ($this->cvi && $cvi_index = array_search($this->cvi, array_keys($this->sv->apporteurs->toArray()))) {
            $this->cvi_precedent = $this->sv->apporteurs->get($this->cvi)->getPreviousSister()->getKey();
        }

        $this->form = new SVSaisieForm($this->sv, $this->cvi);
        $this->formAjoutProduit = new SVAjoutProduitApporteurForm($this->sv, $this->cvi);

        if (!$request->isMethod(sfWebRequest::POST)) {
        	return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
	    }

        $this->form->save();

        if ($request->getParameter('ajout-produit')) {
            return $this->redirect('sv_saisie', ['sf_subject' => $this->sv, 'cvi' => $this->cvi, 'modal' => 'ajout-produit']);
        }

        if($request->getParameter('precedent_cvi')) {

            return $this->redirect('sv_saisie', array('sf_subject' => $this->sv, 'cvi' => $request->getParameter('precedent_cvi')));
        }

        if($request->getParameter('retour_liste')) {
            return $this->redirect(
                SVEtapes::$links[SVEtapes::ETAPE_APPORTEURS],
                ['id' => $this->sv->_id]
            );
        }

        $finded = false;
        foreach($this->sv->apporteurs as $cvi => $apporteur) {
            if($finded) {

                return $this->redirect('sv_saisie', array('sf_subject' => $this->sv, 'cvi' => $cvi));
            }
            $finded = ($cvi == $this->cvi);
        }

        return $this->redirect(
            SVEtapes::$links[SVEtapes::ETAPE_APPORTEURS],
            ['id' => $this->sv->_id]
        );
    }


    public function executeSaisieRevendication(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();
        $this->cvi = $request->getParameter('cvi', null);

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

        if ($this->cvi && $cvi_index = array_search($this->cvi, array_keys($this->sv->apporteurs->toArray()))) {
            $this->cvi_precedent = $this->sv->apporteurs->get($this->cvi)->getPreviousSister()->getKey();
        }

        $this->form = new SVSaisieRevendicationForm($this->sv, $this->cvi);
        $this->formAjoutProduit = new SVAjoutProduitApporteurForm($this->sv, $this->cvi);

        if (!$request->isMethod(sfWebRequest::POST)) {
        	return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
	    }

        $this->form->save();

        if($request->getParameter('precedent_cvi')) {

            return $this->redirect('sv_saisie_revendication', array('sf_subject' => $this->sv, 'cvi' => $request->getParameter('precedent_cvi')));
        }

        if($request->getParameter('retour_liste')) {
            return $this->redirect(
                SVEtapes::$links[SVEtapes::ETAPE_REVENDICATION],
                ['id' => $this->sv->_id]
            );
        }

        $finded = false;
        foreach($this->sv->apporteurs as $cvi => $apporteur) {
            if($finded) {

                return $this->redirect('sv_saisie_revendication', array('sf_subject' => $this->sv, 'cvi' => $cvi));
            }
            $finded = ($cvi == $this->cvi);
        }

        return $this->redirect(
            SVEtapes::$links[SVEtapes::ETAPE_REVENDICATION],
            ['id' => $this->sv->_id]
        );
    }

    public function executeRecalculeVolumesRevendiques(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

        $this->sv->recalculeVolumesRevendiques();
        $this->sv->save();

        return $this->redirect('sv_revendication', $this->sv);
    }

    public function executeAutres(sfWebRequest $request) {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

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
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

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
        $this->sv = $this->getRoute()->getSV();
        $this->svvalidation = new SVValidation($this->sv);

        if ($this->sv->isValide()) { return $this->redirect('sv_visualisation', ['id' => $this->sv->_id]); }

        if (! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        if ($this->svvalidation->isValide() === false) {
            return sfView::SUCCESS;
        }

        $this->sv->validate();
        $this->sv->save();

        $message = "Bonjour,

Vous venez de valider votre déclaration de production pour l'année ".$this->sv->periode.".

Vous trouverez ci-joint votre déclaration au format PDF

Vous pouvez également toujours la visualiser sur votre espace civa : https://declaration.preprod.vinsalsace.pro/sv/validation/".$this->sv->_id."

--
L'application de télédéclaration de production du CIVA

";

        $pdf = new ExportSVPdf($this->sv, 'pdf');
        $pdf->generatePDF();

        $to = $this->sv->etablissement->getEmailTeledeclaration();
        $mail = Swift_Message::newInstance()
                ->setFrom(sfConfig::get('app_email_from'))
                ->setTo($to)
                ->setSubject("CIVA - Validation de votre Déclaration de Production")
                ->attach(new Swift_Attachment(
                    $pdf->output(), $pdf->getFileName(), 'application/pdf'
                ))
                ->setBody($message);

        try {
            $this->getMailer()->send($mail);
            $this->emailSent = true;
        } catch (Exception $e) {
            $this->emailSent = false;
        }

        return $this->redirect('sv_confirmation', $this->sv);
    }

    public function executeConfirmation(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide() === false) {
            return $this->redirect('sv_validation', $this->sv);
        }
    }

    public function executeFeedBack(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide() === false) {
            return $this->redirect('sv_validation', $this->sv);
        }

        $this->form = new FeedBackForm();

        if(! $request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (! $this->form->isValid()) {
            return sfView::SUCCESS;
        }

        $message = $this->form->getValue('message');
        $message .= "\n\n-------\n\n".$this->getUser()->getCompte()->nom."\ncvi: ".$this->getRoute()->getSV()->declarant->cvi;

        $to = sfConfig::get('app_email_feed_back');
        $mail = Swift_Message::newInstance()
                ->setFrom($this->getUser()->getCompte()->email)
                ->setTo($to)
                ->setSubject("CIVA - Retour d'expérience sur la Déclaration de Production")
                ->setBody($message);

        try {
            if(is_array($to) && count($to) < 1) {
                throw new sfException("emails not configure in app.yml email->feed_back");
            }

            $this->getMailer()->send($mail);
            $this->emailSent = true;
        } catch (Exception $e) {
            $this->emailSent = false;
        }
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

    public function executeVisualisation(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide() === false) {
            return $this->redirect('sv_validation', $this->sv);
        }

        $this->svvalidation = new SVValidation($this->sv);
        $this->motifModificationForm = new SVMotifModificationForm($this->sv);
    }

    public function executeJSON(sfWebRequest $request)
    {
        $sv = $this->getRoute()->getSV();
        $has_motif = $request->getParameter('has_motif', 1);

        if ($sv->isValide() === false || $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) === false) {
            return $this->redirect('sv_visualisation', $sv);
        }

        if ($has_motif) {
            $motifModificationForm = new SVMotifModificationForm($sv);

            $motifModificationForm->bind($request->getParameter($motifModificationForm->getName()));

            if ($motifModificationForm->isValid() === false) {
                return $this->redirect('sv_visualisation', $sv);
            }

            $motifModificationForm->save();
        }

        $class = "Export".$sv->getType()."Json";
        $json = [$class::ROOT_NODE => []];

        $export = new $class($sv);
        $export->build();
        $json[$class::ROOT_NODE][] = json_decode($export->export());

        $export->addHeaders($this->getResponse());
        return $this->renderText(json_encode($json).PHP_EOL);
    }

    public function executeCSV(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();
        $file = $this->sv->_attachments->getFirst();

        $this->getResponse()->setHttpHeader('Content-Type', $file->content_type);
        $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="' . $file->getKey() . '.csv"');
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Content-Length', $file->length);
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        return $this->renderText(file_get_contents($this->sv->getAttachmentUri($file->getKey())));
    }

    public function executeInvaliderCiva(sfWebRequest $request)
    {
        $this->sv = $this->getRoute()->getSV();

        if ($this->sv->isValide() === false || $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN) === false) {
            return $this->redirect('sv_validation', $this->sv);
        }

        $this->sv->devalidate();
        $this->sv->save();

        return $this->redirect('sv_validation', $this->sv);
    }

    public function executeTransmission(sfWebRequest $request) {
        $sv = $this->getRoute()->getSV();
        set_time_limit(180);
        $this->url = $request->getParameter('url');

        if ($sv->isValide() === false) {

            return $this->redirect($this->url);
        }

        $this->setLayout(false);

        $this->document = new ExportSVPdf($sv, 'pdf');
        $this->document->generatePDF();
        $this->pdf = base64_encode($this->document->output());

        $csvContruct = new ExportSVMouvementsCsv();
        $this->csv = base64_encode($csvContruct->exportOne($sv));
    }

}
