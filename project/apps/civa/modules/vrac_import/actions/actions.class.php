<?php
class vrac_importActions extends sfActions
{
    private function secureRoute($identifiant)
    {
        if (! $this->getUser()->isAdmin() && $this->getUser()->getCompte()->getIdentifiant() !== $identifiant) {
            return $this->forwardSecure();
        }
    }

    public function executeAccueil(sfWebRequest $request)
    {
        $this->compte = $this->getRoute()->getCompte();
        $this->secureRoute($this->compte->identifiant);

        return sfView::SUCCESS;
    }

    public function executeCSVVracListe(sfWebRequest $request)
    {
        $this->compte = $this->getRoute()->getCompte();
        $this->csvs = CSVVRACClient::getInstance()->findByIdentifiant($this->compte->getIdentifiant());

        return sfView::SUCCESS;
    }

    public function executeCSVVracFiche(sfWebRequest $request)
    {
        $this->csvVrac = CSVVRACClient::getInstance()->find($request->getParameter('csvvrac'));
        $this->vracimport = new VracCsvImport($this->csvVrac->getFile());
        $this->compte = CompteClient::getInstance()->find($this->csvVrac->identifiant);

        $this->secureRoute($this->compte->identifiant);

        $this->formAnnexe = new sfForm();
        $this->formAnnexe->setWidget('annexeInputFile', new sfWidgetFormInputFile([], ['multiple' => true, 'accept' => 'application/pdf, application/x-pdf']));

        if ($request->getMethod() === sfWebRequest::GET) {
            return sfView::SUCCESS;
        }

        $this->formAnnexe->setValidator('annexeInputFile', new sfValidatorFileMulti([
            'required' => false, 'max_size' => '2097152',
            'mime_categories' => ['pdf' => ['application/pdf', 'application/x-pdf']],
            'mime_types' => 'pdf'
        ]));

        $this->formAnnexe->bind(null, $request->getFiles());
        if ($this->formAnnexe->isValid()) {
            $annexes = $this->formAnnexe->getValue('annexeInputFile');
            if ($annexes) {
                foreach ($annexes as $annexe) {
                    $this->csvVrac->addAnnexe($annexe->getTempName(), $annexe->getOriginalName());
                }
            }
        } else {
            // Mauvais format de fichier / Fichier trop gros
            // Message session ? Erreur ? Redirection ?
        }

        return $this->redirect('vrac_csv_validation', ['csvvrac' => $this->csvVrac->_id]);
    }

    public function executeCSVVracNew(sfWebRequest $request)
    {
        $this->compte = $this->getRoute()->getCompte();

        $this->secureRoute($this->compte->identifiant);

        $csv = current($request->getFiles());
        $this->csvVrac = CSVVRACClient::getInstance()->createNouveau($csv['tmp_name'], $this->compte);
        $this->vracimport = new VracCsvImport($this->csvVrac->getFile());
        $this->vracimport->import();
        $this->vracimport->checkErreurs($this->csvVrac);

        $this->csvVrac->save();

        return $this->redirect('vrac_csv_fiche', ['csvvrac' => $this->csvVrac->_id]);
    }

    public function executeCSVVracUpload(sfWebRequest $request)
    {
        $this->csvVrac = CSVVRACClient::getInstance()->find($request->getParameter('csvvrac'));

        if (! $this->getUser()->isAdmin() && $this->getUser()->getCompte()->getIdentifiant() !== $this->csvVrac->identifiant) {
            return $this->forwardSecure();
        }

        $csv = current($request->getFiles());
        if ($csv['size'] === 0) {
            throw new sfException("Pas de fichier fourni");
        }
        $this->csvVrac->clearErreurs();
        $this->csvVrac->storeAttachment($csv['tmp_name'], 'text/csv', $this->csvVrac->getFileName());
        $this->csvVrac->save();
        $this->vracimport = new VracCsvImport($this->csvVrac->getFile());
        $this->vracimport->import();
        $this->vracimport->checkErreurs($this->csvVrac);

        $this->csvVrac->save();

        return $this->redirect('vrac_csv_fiche', ['csvvrac' => $this->csvVrac->_id]);
    }

    public function executeCSVVracValidation(sfWebRequest $request)
    {
        $this->csvVrac = CSVVRACClient::getInstance()->find($request->getParameter('csvvrac'));
        $this->compte = CompteClient::getInstance()->find($this->csvVrac->identifiant);
        $this->secureRoute($this->compte->identifiant);

        $this->vracimport = new VracCsvImport($this->csvVrac->getFile());
    }

    public function executeCSVVracImport(sfWebRequest $request)
    {
        $this->csvVrac = CSVVRACClient::getInstance()->find($request->getParameter('csvvrac'));
        $this->secureRoute($this->csvVrac->identifiant);

        if ($this->csvVrac->statut === CSVVRACClient::LEVEL_ERROR) {
            throw new sfException("Impossible d'importer un fichier en erreur");
        }

        if ($this->csvVrac->statut === CSVVRACClient::LEVEL_IMPORTE) {
            throw new sfException("Impossible de réimporter un fichier");
        }

        $this->vracimport = new VracCsvImport($this->csvVrac->getFile());
        $imported = $this->vracimport->import(true);

        if (count($this->csvVrac->getAnnexes())) {
            foreach ($this->csvVrac->getAnnexes() as $name => $annexe) {
                $this->vracimport->addAnnexe($annexe, $name);
            }
        } else {
            // Mauvais format de fichier / Fichier trop gros
            // Message session ? Erreur ? Redirection ?
        }

        $this->csvVrac->statut = CSVVRACClient::LEVEL_IMPORTE;
        $this->csvVrac->remove('documents');
        $this->csvVrac->add('documents', $imported);
        $this->csvVrac->save();

        return $this->redirect('vrac_csv_liste', ['identifiant' => $this->csvVrac->identifiant]);
    }

    public function executeCSVVracDownload(sfWebRequest $request)
    {
        $this->csvVrac = CSVVRACClient::getInstance()->find($request->getParameter('csvvrac'));
        $file = $this->csvVrac->_attachments->get($this->csvVrac->getFileName());

        $this->getResponse()->setHttpHeader('Content-Type', $file->content_type);
        $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="' . $file->getKey() . '"');
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Content-Length', $file->length);
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        return $this->renderText(file_get_contents($this->csvVrac->getAttachmentUri($file->getKey())));
    }

    public function executeCSVVracVisualisation(sfWebRequest $request)
    {
        $this->csvVrac = CSVVRACClient::getInstance()->find($request->getParameter('csvvrac'));
        $this->secureRoute($this->csvVrac->identifiant);

        if ($this->csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE) {
            $this->redirect('vrac_csv_validation', ['csvvrac' => $csvVrac->_id]);
        }

        $this->compte = CompteClient::getInstance()->find($this->csvVrac->identifiant);
        $this->vracimport = new VracCsvImport($this->csvVrac->getFile());

        $this->setTemplate('CSVVracValidation');
    }
}
