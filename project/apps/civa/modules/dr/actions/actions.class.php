<?php
class drActions extends _DRActions {

    public function executeInit(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);

        // throw new sfException("En maintenance");
        $this->forward404Unless($request->isMethod(sfWebRequest::POST));
        $this->getUser()->initCredentialsDeclaration();
        $this->setCurrentEtape('mon_espace_civa');
        $campagne = $request->getParameter('campagne');
        $etablissement = $this->getRoute()->getEtablissement();
        $dr_data = $this->getRequestParameter('dr', null);

        if (!$dr_data) {

            return $this->forward404();
        }

        $dr = DRClient::getInstance()->find("DR-".$etablissement->getIdentifiant()."-".$campagne);

        if ($dr_data['type_declaration'] == 'brouillon') {
            try {
                if($dr->etape) {
                    return $this->redirectToEtape($dr->etape, $dr);
                }
            } catch (Exception $e) { }

            return $this->redirectByBoutonsEtapes(array('valider' => 'next'), $dr);
        } elseif ($dr_data['type_declaration'] == 'supprimer') {
            $dr->delete();

            return $this->redirect('mon_espace_civa_dr', $etablissement);
        } elseif ($dr_data['type_declaration'] == 'visualisation') {

            return $this->redirect('dr_visualisation', array('id' => $dr->_id));
        } elseif ($dr_data['type_declaration'] == 'vierge') {
            $dr = DRClient::getInstance()->createDeclaration($etablissement, $campagne, $this->getUser()->isSimpleOperateur());
            $dr->save();

            return $this->redirectByBoutonsEtapes(array('valider' => 'next'), $dr);
        } elseif ($dr_data['type_declaration'] == 'visualisation_avant_import') {

            return $this->redirect('dr_visualisation_avant_import', array('identifiant' => $etablissement->identifiant, 'campagne' => $campagne));
        } elseif ($dr_data['type_declaration'] == 'import') {
            $acheteurs = array();
            $dr = DRClient::getInstance()->createFromCSVRecoltant($campagne, $etablissement, $acheteurs, $this->getUser()->isSimpleOperateur());
            $dr->save();
            $this->getUser()->setFlash('flash_message', $this->getPartial('dr/importMessage', array('acheteurs' => $acheteurs, 'post_message' => true)));

            return $this->redirectByBoutonsEtapes(array('valider' => 'next'), $dr);
        } elseif ($dr_data['type_declaration'] == 'precedente') {
            $drPrevious = DRClient::getInstance()->find("DR-".$etablissement->getIdentifiant()."-".$dr_data['liste_precedentes_declarations']);
            if (!$drPrevious) {
                throw new Exception("Bug: " . $dr_data['liste_precedentes_declarations'] . " not found :(");
            }

            $dr = DRClient::getInstance()->createDeclarationClone($drPrevious, $etablissement, $campagne, $this->getUser()->isSimpleOperateur());
            $dr->save();

            return $this->redirectByBoutonsEtapes(array('valider' => 'next'), $dr);
        }

        return $this->forward404();
    }

    public function executeFlashPage(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->dr = $this->getRoute()->getDR();
        $boutons = $this->getRequestParameter('boutons', null);
        $this->setCurrentEtape('exploitation_message');
        if (!$this->getUser()->hasFlash('flash_message') && !$boutons) {

            return $this->redirectToNextEtapes($this->getRoute()->getDR());
        }
        if ($boutons && in_array('previous', array_keys($boutons))) {
            $this->getUser()->removeDeclaration();
            return $this->redirect('mon_espace_civa_dr', $this->getRoute()->getEtablissement());
        } elseif ($boutons && in_array('next', array_keys($boutons))) {

            return $this->redirectToNextEtapes($this->getRoute()->getDR());
        }
    }

    public function executeNoticeEvolutions(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->setCurrentEtape('notice_evolutions');

        $this->dr = $this->getRoute()->getDR();

        if($this->getUser()->isSimpleOperateur()) {

            return $this->redirectToNextEtapes($this->dr);
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $boutons = $this->getRequestParameter('boutons', null);
            if ($boutons && in_array('previous', array_keys($boutons))) {

                return $this->redirect('mon_espace_civa_dr', $this->getRoute()->getEtablissement());
            }

            return $this->redirectByBoutonsEtapes(null, $this->dr);
        }
    }

    public function executeExploitation(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->setCurrentEtape('exploitation');
        $this->help_popup_action = "help_popup_exploitation_administratif";

        $this->forwardUnless($this->etablissement = $this->getRoute()->getEtablissement(), 'declaration', 'monEspaceciva');

        $this->tiers = $this->etablissement;
        $this->dr = $this->getRoute()->getDR();
        $this->etablissement = $this->getRoute()->getEtablissement();

        $this->form_gest = new TiersExploitantForm($this->etablissement->getExploitant());
        $this->form_gest_err = 0;
        $this->form_expl = new TiersExploitationForm($this->etablissement);
        $this->form_expl_err = 0;

        if ($request->isMethod(sfWebRequest::POST)) {
            if ($request->getParameter('gestionnaire')) {
                $this->form_gest->bind($request->getParameter($this->form_gest->getName()));
                if ($this->form_gest->isValid()) {
                    $this->form_gest->save();
                } else {
                    $this->form_gest_err = 1;
                }
            }
            if ($request->getParameter('exploitation')) {
                $this->form_expl->bind($request->getParameter($this->form_expl->getName()));
                if ($this->form_expl->isValid()) {
                    $tiers = $this->form_expl->save();
                } else {
                    $this->form_expl_err = 1;
                }
            }
            if (!$this->form_gest_err && !$this->form_expl_err) {
                $this->dr->storeDeclarant();
                $this->dr->save();
                $this->redirectByBoutonsEtapes(null, $this->dr);
            }
        }
    }

    public function executeRepartition(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->setCurrentEtape('repartition');
        $this->dr = $this->getRoute()->getDR();

        $this->help_popup_action = "help_popup_exploitation_acheteur";

        $this->form = new ExploitationAcheteursForm($this->dr->getAcheteurs());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
        }

        if (!$request->isMethod(sfWebRequest::POST) || !$this->form->isValid()) {
            $this->appellations = ExploitationAcheteursForm::getListeAppellations($this->dr);
            $this->acheteurs_negociant_using = $this->dr->acheteurs->getArrayNegoces();
            $this->acheteurs_cave_using = $this->dr->acheteurs->getArrayCooperatives();
            $this->acheteurs_mout_using = $this->dr->acheteurs->getArrayMouts();

            $this->acheteurs_negociant = ListAcheteursConfig::getNegoces();
            $this->acheteurs_cave = ListAcheteursConfig::getCooperatives();
            $this->acheteurs_mout = ListAcheteursConfig::getMouts();
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            if ($this->form->isValid()) {
                $this->form->save();
                $this->dr->save();

                $this->redirectByBoutonsEtapes(null, $this->dr);
            }
        }
    }

    public function executeRepartitionTableRowItemAjax(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->dr = $this->getRoute()->getDR();
        if ($request->isXmlHttpRequest() && $request->isMethod(sfWebRequest::POST)) {
            $name = $request->getParameter('qualite_name');
            $donnees = $request->getParameter('donnees');
            $nom = $donnees[0];
            $cvi = $donnees[1];
            $commune = $donnees[2];
            $mout = ($request->getParameter('acheteur_mouts', null) == '1');

            $appellations_form = ExploitationAcheteursForm::getListeAppellations($this->dr);
            if ($mout) {
                $appellations_form = ExploitationAcheteursForm::getListeAppellationsMout($this->dr);
            }
            $values = array();
            $i = 3;
            foreach ($appellations_form as $key => $item) {
                $values[$key] = (isset($donnees[$i]) && $donnees[$i] == '1');
                $i++;
            }

            $form = ExploitationAcheteursForm::getNewItemAjax($name, $cvi, $values, $appellations_form);

            return $this->renderPartial('exploitationAcheteursTableRowItem', array('nom' => $nom,
                'cvi' => $cvi,
                'commune' => $commune,
                'appellations' => ExploitationAcheteursForm::getListeAppellations($this->dr),
                'form_item' => $form[$name . ExploitationAcheteursForm::FORM_SUFFIX_NEW][$cvi],
                'mout' => $mout));
        } else {
            $this->forward404();
        }
    }

        public function executeRepartitionLieu(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->setCurrentEtape('exploitation_lieu');
        $this->help_popup_action = "help_popup_exploitation_lieu";
        $this->appellations = array();
        $this->forms = array();
        $this->dr = $this->getRoute()->getDR();
        $this->form = new LieuDitForm($this->dr);

        if (!$request->isMethod(sfWebRequest::POST)) {
            $hasLieu = false;
            foreach($this->dr->recolte->getAppellations() as $appellation) {
                if(!$appellation->getConfig()->hasManyLieu()) {
                    continue;
                }

                $hasLieu = true;
                break;
            }
            if (!$hasLieu) {
                if ($this->hasRequestParameter('from_recolte')) {
                    return $this->redirectToPreviousEtapes($this->dr);
                } else {
                    return $this->redirectToNextEtapes($this->dr);
                }
            }

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $this->form->save();

        $boutons = $request->getPostParameter('boutons');
        if(!$this->form->hasOneLieuForEach() && isset($boutons['next'])) {
            $this->getUser()->setFlash('erreur_global', "Vous devez saisir un lieu-dit pour chacune des appellations");

            return $this->redirect('dr_repartition_lieu', $this->dr);
        }

        return $this->redirectByBoutonsEtapes(null, $this->dr);
    }

    public function executeRepartitionLieuDelete(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $declaration = $this->getRoute()->getDR();
        $hash = $request->getParameter('hash');
        $lieu = $request->getParameter('lieu');


        if(!$declaration->exist($hash)) {

            return $this->redirect('dr_repartition_lieu', $declaration);
        }

        if(!$declaration->get($hash) instanceof DRRecolteAppellation) {

            return $this->redirect('dr_repartition_lieu', $declaration);
        }

        if($declaration->get($hash)->hasDetailsInLieu($lieu)) {

            return $this->redirect('dr_repartition_lieu', $declaration);
        }

        foreach($declaration->get($hash)->getMentions() as $mention) {
            $mention->getLieux()->remove($lieu);
        }

        $declaration->save();

        return $this->redirect('dr_repartition_lieu', $declaration);
    }

    public function executeNoRecolte(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->setCurrentEtape('repartition');
        $this->redirectToNextEtapes($this->getRoute()->getDR());
    }

    public function executeDownloadNotice() {
        return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/aide_recolte.pdf", "aide recolte.pdf");
    }

    public function executeAutres(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->setCurrentEtape('exploitation_autres');
        $this->help_popup_action = "help_popup_autres";

        $this->dr = $this->getRoute()->getDR();

        $this->form = new ExploitationAutresForm($this->dr);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirectByBoutonsEtapes(null, $this->dr);
            }
        }
    }

    public function executeValidation(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $this->help_popup_action = "help_popup_validation";
        $this->setCurrentEtape('validation');

        $this->dr = $this->getRoute()->getDR();

        $this->getUser()->getAttributeHolder()->remove('log_erreur');

        $etablissement = $this->dr->getEtablissement();
        $this->annee =$this->dr->getCampagne();


        $this->dr->update();

        $check = $this->dr->check();

        $this->validLogErreur = $this->updateUrlLog($check['erreur']);
        $this->validLogVigilance = $this->updateUrlLog($check['vigilance']);

        $this->error = count($check['erreur']);
        $this->logVigilance = count($check['vigilance']);

        $this->getUser()->setAttribute('log_erreur', $this->validLogErreur);
        $this->getUser()->setAttribute('log_vigilance', $this->validLogVigilance);

        $this->isAdmin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
        $this->validation_date = date('Y-m-d');
        $this->validation_compte_id = (!$this->dr->isValideeTiers()) ? $this->getUser()->getCompte()->get('_id') : $this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id');

        if($this->getUser()->isInDelegateMode() && !$this->isAdmin) {
            $this->validation_compte_id = $this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id');
        }

        if($this->dr->hasDateDepotMairie()) {
            $this->validation_compte_id = $this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id');
        }

        if($this->isAdmin){
            $this->formDatesModification = new DREditionDatesModificationForm($this->dr,
                $this->getUser());
        }

        if ($this->askRedirectToPreviousEtapes($this->dr)) {

            return $this->redirectByBoutonsEtapes(null, $this->dr);
        }

        if ($this->askRedirectToNextEtapes() && !$this->error && $request->isMethod(sfWebRequest::POST)) {

            if(isset($this->formDatesModification)) {
                $this->formDatesModification->bind($request->getParameter($this->formDatesModification->getName()));

                if(!$this->formDatesModification->isValid()) {

                    return sfView::SUCCESS;
                }

                $this->validation_date = $this->formDatesModification->getDate();

                if($this->formDatesModification->getCompteId()) {
                    $this->validation_compte_id = $this->formDatesModification->getCompteId();
                }
            }

            $this->dr->remove("autorisations");
            $autorisations = array();
            foreach($request->getParameter('autorisations', array()) as $autorisation) {
                $autorisations[$autorisation] = 1;
            }
            if(count($autorisations) > 0) {
                $this->dr->add('autorisations', $autorisations);
            }

            $this->dr->validate($this->validation_date, $this->validation_compte_id);
            if(!$this->dr->hasDateDepotMairie()) {
                $this->dr->add('en_attente_envoi', true);
            }

            $this->dr->save();
            $this->getUser()->initCredentialsDeclaration();

            return $this->redirectByBoutonsEtapes(null, $this->dr);
        }

    }

    private function updateUrlLog($array) {
      $ret = array();
      foreach ($array as $log) {
        $log['url_log'] = isset($log['url']) ? $log['url'] : false;
        array_push($ret, $log);
      }
      return $ret;
    }

    public function executeSetFlashLog(sfWebRequest $request) {
        $this->secureDR(DRSecurity::EDITION);
        $id = $this->getRequestParameter('flash_message', null);
        $array = $this->getRequestParameter('array', null);

        $flash_messages = $this->getUser()->getAttribute($array);

        $this->getUser()->getAttributeHolder()->remove('log_erreur');
        $this->getUser()->getAttributeHolder()->remove('log_vigilance');

        $this->getUser()->setFlash('flash_message', $flash_messages[$id]['info']);
        $this->redirect($flash_messages[$id]['url_log']);
    }

    public function executeVisualisation(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $this->help_popup_action = "help_popup_visualisation";

        $this->dr = $this->getRoute()->getDR();

        $this->annee = $this->dr->getCampagne();

        $key = $this->dr->_id;

        $this->has_import = DRClient::getInstance()->hasImport($this->dr->cvi, $this->dr->campagne);
        $this->forward404Unless($this->dr);

        try {
            if (!$this->dr->updated)
                throw new Exception();
        } catch (Exception $e) {
            $this->dr->update();
            $this->dr->save();
        }

    }

    public function executeConfirmation(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $this->setCurrentEtape('confirmation');

        $this->dr = $this->getRoute()->getObject();
        if($this->getUser()->isSimpleOperateur()) {

            return $this->redirect('mon_espace_civa_dr', $this->dr->getEtablissement());
        }
        $this->has_import = DRClient::getInstance()->hasImport($this->dr->cvi, $this->dr->campagne);
        $this->annee = $request->getParameter('annee', $this->getUser()->getCampagne());
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    public function executeSendPdfAcheteurs(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $dr = $this->getRoute()->getDR();

        $annee = $this->getRequestParameter('annee', null);

        $this->mailerManager = new RecolteMailingManager($this->getMailer(),array($this, 'getPartial'),$dr, $this->getRoute()->getEtablissement(),$annee);

        $this->sendMailAcheteursReport = $this->mailerManager->sendAcheteursMails();
    }

    public function executeSendPdf(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $tiers = $this->getUser()->getTiers('Recoltant');
        $dr = $this->getRoute()->getDR();
        $annee = $this->getRequestParameter('annee', null);

        $this->mailerManager = new RecolteMailingManager($this->getMailer(),array($this, 'getPartial'),$dr,$tiers,$annee);

        $this->emailSend = $this->mailerManager->sendMail(true);
    }

    public function executeInvaliderCiva(sfWebRequest $request) {
        $this->secureDR(DRSecurity::ADMIN);

        $this->setCurrentEtape('mon_espace_civa');
        $dr = $this->getRoute()->getDR();
        if ($dr) {
            $dr->remove('modifiee');
            $dr->add('etape');
            $dr->etape = 'validation';
            $dr->save();
        }

        $this->redirectToNextEtapes($dr);
    }

    public function executeInvaliderRecoltant(sfWebRequest $request) {
        $this->secureDR(DRSecurity::ADMIN);

        $dr = $this->getRoute()->getDR();
        if ($dr) {
            $dr->remove('modifiee');
            $dr->remove('validee');
            if (!$dr->exist('etape')) {
                $dr->add('etape', 'validation');
            } else {
                $dr->set('etape', 'validation');
            }
            $dr->save();
        }

        $this->getUser()->initCredentialsDeclaration();
        $this->redirect('mon_espace_civa_dr', $dr->getEtablissement());
    }

    public function executeVisualisationAvantImport(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->campagne = $this->getRequestParameter('campagne');
        $this->acheteurs = array();
        $this->dr = DRClient::getInstance()->createFromCSVRecoltant($this->campagne, $this->etablissement, $this->acheteurs, $this->getUser()->isSimpleOperateur());
        $this->visualisation_avant_import = true;
    }

    public function executeConfirmationMailDR(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
    }

    public function executeFeedBack(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $this->dr = $this->getRoute()->getDR();
        $this->form = new FeedBackForm();

        if(!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $message = $this->form->getValue('message');

        /*$mess = 'Bonjour ' . $this->tiers->nom . ',

Vous trouverez ci-joint votre Déclaration de Stocks pour l\'année ' . $this->ds->getAnnee() . ' que vous venez de valider.

Cordialement,

Le CIVA';*/

        $message .= "\n\n-------\n\n".$this->getUser()->getCompte()->nom."\ncvi: ".$this->getRoute()->getDR()->cvi;

        $to = sfConfig::get('app_email_feed_back');
        $this->emailSend = true;
        $mail = Swift_Message::newInstance()
                ->setFrom($this->getUser()->getCompte()->email)
                ->setTo($to)
                ->setSubject("CIVA - Retour d'expérience sur la Déclaration de Récolte")
                ->setBody($message);

        try {
            if(is_array($to) && count($to) < 1) {
                throw new sfException("emails not configure in app.yml email->feed_back");
            }

            $this->getMailer()->send($mail);
        } catch (Exception $e) {

            $this->emailSend = false;
        }

        return $this->redirect('dr_feed_back_confirmation', $this->dr);
    }

    public function executeFeedBackConfirmation(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        $this->dr = $this->getRoute()->getDR();
    }

    public function executeAutorisation(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);

        $this->url = $request->getParameter('url');
        $this->id = $request->getParameter('id');

        if(!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $dr = acCouchdbManager::getClient()->find($this->id);
        $dr->add('autorisations')->add(DRClient::AUTORISATION_AVA, 1);
        $dr->save();

        return $this->redirect('declaration_transmission', array("url" => $this->url, "id" => $this->id));
    }

    public function executeTransmissionAva(sfWebRequest $request) {
        $this->id = $request->getParameter('id');
        $this->url = $request->getParameter('url');

        if(!DRClient::getInstance()->find($this->id)) {

            return $this->redirect($this->url);
        }

        return $this->redirect('dr_transmission', array('id' => $this->id, 'url' => $this->url));
    }

    public function executeTransmission(sfWebRequest $request) {
        $this->secureDR(DRSecurity::CONSULTATION);
        set_time_limit(180);
        $this->url = $request->getParameter('url');
        $this->id = $request->getParameter('id');
        $dr = $this->getRoute()->getDR();
        if(!$dr || !$dr->isValideeTiers()) {

            return $this->redirect($this->url);
        }

        if(!$dr->hasAutorisation(DRClient::AUTORISATION_AVA)) {

            return $this->redirect('dr_autorisation', array('id' => $this->id, 'url' => $this->url));
        }

        $this->setLayout(false);

        $this->document = new ExportDRPdf($dr, array($this, 'getPartial'), 'pdf');
        $this->document->generatePDF();
        $this->pdf = base64_encode($this->document->output());

        $csvContruct = new ExportDRCsv($dr->campagne, $dr->cvi);
        $csvContruct->export();
        $this->csv = base64_encode($csvContruct->output());
    }

    protected function renderPdf($path, $filename) {
        $this->getResponse()->setHttpHeader('Content-Type', 'application/pdf');
        $this->getResponse()->setHttpHeader('Content-disposition', 'attachment; filename="' . $filename . '"');
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Content-Length', filesize($path));
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        return $this->renderText(file_get_contents($path));
    }
}
