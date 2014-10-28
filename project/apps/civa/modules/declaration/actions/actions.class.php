<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationActions extends EtapesActions {

    public function executeInit(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfWebRequest::POST));
        $this->getUser()->initCredentialsDeclaration();
        $this->setCurrentEtape('mon_espace_civa');
        $tiers = $this->getUser()->getTiers('Recoltant');
        $dr_data = $this->getRequestParameter('dr', null);
        if ($dr_data) {
            if ($dr_data['type_declaration'] == 'brouillon') {
                $dr = $this->getUser()->getDeclaration();
                try {
                    if($dr->etape) {
                        return $this->redirectToEtape($dr->etape);
                    }
                } catch (Exception $e) {

                }
                return $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'supprimer') {
                $this->getUser()->removeDeclaration();
                
                return $this->redirect('@mon_espace_civa_dr');
            } elseif ($dr_data['type_declaration'] == 'visualisation') {
                $this->redirect('@visualisation?annee=' . $this->getUser()->getCampagne());
            } elseif ($dr_data['type_declaration'] == 'vierge') {
                $doc = DRClient::getInstance()->createDeclaration($tiers, $this->getUser()->getCampagne(), $this->getUser()->isSimpleOperateur());
                $doc->save();
        
                return $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'visualisation_avant_import') {
                $this->redirect('@visualisation_avant_import');
            } elseif ($dr_data['type_declaration'] == 'import') {
                $acheteurs = array();
                $dr = DRClient::getInstance()->createFromCSVRecoltant($this->getUser()->getCampagne(), $tiers, $acheteurs, $this->getUser()->isSimpleOperateur());
                $dr->save();
                $this->getUser()->setFlash('flash_message', $this->getPartial('declaration/importMessage', array('acheteurs' => $acheteurs, 'post_message' => true)));
                
                return $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'precedente') {
                $old_doc = $tiers->getDeclaration($dr_data['liste_precedentes_declarations']);
                if (!$old_doc) {
                    throw new Exception("Bug: " . $dr_data['liste_precedentes_declarations'] . " not found :()");
                }
                $doc = DRClient::getInstance()->createDeclarationClone($old_doc, $tiers, $this->getUser()->getCampagne(), $this->getUser()->isSimpleOperateur());
                $doc->save();
                
                return $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            }
        }
        $this->redirect('@mon_espace_civa_dr');
    }

    public function executeDownloadNotice() {
        return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/aide_recolte.pdf", "aide recolte.pdf");
    }

    public function executeNoticeEvolutions(sfWebRequest $request) {
        $this->setCurrentEtape('notice_evolutions');

        if($this->getUser()->isSimpleOperateur()) {

            return $this->redirectToNextEtapes();
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $boutons = $this->getRequestParameter('boutons', null);
            if ($boutons && in_array('previous', array_keys($boutons))) {
                
                return $this->redirect('@mon_espace_civa_dr'); 
            }   
            
            return $this->redirectByBoutonsEtapes();
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeExploitationAutres(sfWebRequest $request) {
        $this->setCurrentEtape('exploitation_autres');
        $this->help_popup_action = "help_popup_autres";

        $this->form = new ExploitationAutresForm($this->getUser()->getDeclaration());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirectByBoutonsEtapes();
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeValidation(sfWebRequest $request) {

        $this->help_popup_action = "help_popup_validation";
        $this->setCurrentEtape('validation');

        $this->getUser()->getAttributeHolder()->remove('log_erreur');

        $tiers = $this->getUser()->getTiers('Recoltant');
        $annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());
        $key = 'DR-' . $tiers->cvi . '-' . $annee;
        $this->dr = acCouchdbManager::getClient()->find($key);
        $this->dr->update();

        $check = $this->dr->check();
        $this->annee = $annee;

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

        if($this->isAdmin){
            $this->formDatesModification = new DREditionDatesModificationForm($this->dr,
                $this->getUser());
        }

        if ($this->askRedirectToPreviousEtapes()) {
            
            return $this->redirectByBoutonsEtapes();
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

            return $this->redirectByBoutonsEtapes();
        }
        
    }

    private function updateUrlLog($array) {
      $ret = array();
      foreach ($array as $log) {
        if(!isset($log['url_log_param'])) {
            $log['url_log'] = false;
            array_push($ret, $log);
            continue;
        }
        if (!isset($log['url_log_page']))
          $log['url_log_page'] = 'recolte';
        $log['url_log'] = $this->generateUrl($log['url_log_page'], $log['url_log_param']);
        array_push($ret, $log);
      }
      return $ret;
    }


    /**
     * Set flash message et redirige sur la page d'erreur de la DR
     */
    public function executeSetFlashLog(sfWebRequest $request) {
        $id = $this->getRequestParameter('flash_message', null);
        $array = $this->getRequestParameter('array', null);

        $flash_messages = $this->getUser()->getAttribute($array);

        $this->getUser()->getAttributeHolder()->remove('log_erreur');
        $this->getUser()->getAttributeHolder()->remove('log_vigilance');

        $this->getUser()->setFlash('flash_message', $flash_messages[$id]['info']);
        $this->redirect($flash_messages[$id]['url_log']);
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeVisualisation(sfWebRequest $request) {
        $this->help_popup_action = "help_popup_visualisation";
        $tiers = $this->getUser()->getTiers('Recoltant');
        $annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());

        if($annee + 4 < $this->getUser()->getCampagne()) {
            
            $this->forward404("Cette DR n'est plus accessible");
        }

        $key = 'DR-' . $tiers->cvi . '-' . $annee;
        $this->dr = acCouchdbManager::getClient()->find($key);
        $this->has_import = DRClient::getInstance()->hasImport($this->dr->cvi, $this->dr->campagne); 
        $this->forward404Unless($this->dr);

        try {
            if (!$this->dr->updated)
                throw new Exception();
        } catch (Exception $e) {
            $this->dr->update();
            $this->dr->save();
        }

        $this->annee = $annee;
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeConfirmation(sfWebRequest $request) {
        $this->setCurrentEtape('confirmation');
        if($this->getUser()->isSimpleOperateur()) {

            return $this->redirect('mon_espace_civa_dr');
        }
        $this->dr = $this->getUser()->getDeclaration();
        $this->has_import = DRClient::getInstance()->hasImport($this->dr->cvi, $this->dr->campagne);
        $this->annee = $request->getParameter('annee', $this->getUser()->getCampagne());
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }
    
    public function executeSendPdfAcheteurs(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers('Recoltant');
        $dr = $this->getUser()->getDeclaration();        
        
        $annee = $this->getRequestParameter('annee', null);
        
        $this->mailerManager = new RecolteMailingManager($this->getMailer(),array($this, 'getPartial'),$dr,$tiers,$annee);
        
        $this->sendMailAcheteursReport = $this->mailerManager->sendAcheteursMails();
    }

    

    public function executeSendPdf(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers('Recoltant');
        $dr = $this->getUser()->getDeclaration();
        $annee = $this->getRequestParameter('annee', null);

        $this->mailerManager = new RecolteMailingManager($this->getMailer(),array($this, 'getPartial'),$dr,$tiers,$annee);
        
        $this->emailSend = $this->mailerManager->sendMail(true);       
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeInvaliderCiva(sfWebRequest $request) {
        $this->setCurrentEtape('mon_espace_civa');
        $dr = $this->getUser()->getDeclaration();
        if ($dr) {
            $dr->remove('modifiee');
            $dr->add('etape');
            $dr->etape = 'validation';
            $dr->save();
        }
        $this->getUser()->initCredentialsDeclaration();
        $this->redirectToNextEtapes();
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeInvaliderRecoltant(sfWebRequest $request) {
        $dr = $this->getUser()->getDeclaration();
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
        $this->redirect('@mon_espace_civa_dr');
    }

    public function executeVisualisationAvantImport(sfWebRequest $request) {
        $this->annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());
        $this->acheteurs = array();
        $this->dr = acCouchdbManager::getClient('DR')->createFromCSVRecoltant($this->annee, $this->getUser()->getTiers('Recoltant'), $this->acheteurs, $this->getUser()->isSimpleOperateur());
        $this->visualisation_avant_import = true;
    }

    public function executeFlashPage(sfWebRequest $request) {
        $boutons = $this->getRequestParameter('boutons', null);
        $this->setCurrentEtape('exploitation_message');
        if (!$this->getUser()->hasFlash('flash_message') && !$boutons) {
            $this->redirectToNextEtapes();
        }
        if ($boutons && in_array('previous', array_keys($boutons))) {
            $this->getUser()->removeDeclaration();
            $this->redirect('@mon_espace_civa_dr');
        } elseif ($boutons && in_array('next', array_keys($boutons))) {
            $this->redirectToNextEtapes();
        }
    }

    public function executeConfirmationMailDR(sfWebRequest $request) {

    }
    
    public function executeFeedBack(sfWebRequest $request) {
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

        $message .= "\n\n-------\n\n".$this->getUser()->getCompte()->nom."\ncvi: ".$this->getUser()->getDeclarant()->cvi;

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

        return $this->redirect('recolte_feed_back_confirmation');
    }

    public function executeFeedBackConfirmation(sfWebRequest $request) {

    }

    public function executeAutorisation(sfWebRequest $request) {
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

    public function executeTransmission(sfWebRequest $request) {
        set_time_limit(180);
        $this->url = $request->getParameter('url');
        $this->id = $request->getParameter('id');
        $dr = acCouchdbManager::getClient()->find($this->id);
        if(!$dr || !$dr->isValideeTiers()) {
            
            return $this->redirect($this->url);
        }

        if(!$dr->hasAutorisation(DRClient::AUTORISATION_AVA)) {

            return $this->redirect('declaration_autorisation', array('id' => $this->id, 'url' => $this->url));
        }

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