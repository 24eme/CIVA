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
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'supprimer') {
                $this->getUser()->removeDeclaration();
                $this->redirect('@mon_espace_civa');
            } elseif ($dr_data['type_declaration'] == 'visualisation') {
                $this->redirect('@visualisation?annee=' . $this->getUser()->getCampagne());
            } elseif ($dr_data['type_declaration'] == 'vierge') {
                $doc = new DR();
                $doc->set('_id', 'DR-' . $tiers->cvi . '-' . $this->getUser()->getCampagne());
                $doc->cvi = $tiers->cvi;
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->declaration_insee = $tiers->declaration_insee;
                $doc->declaration_commune = $tiers->declaration_commune;
                $doc->save();
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'visualisation_avant_import') {
                $this->redirect('@visualisation_avant_import');
            } elseif ($dr_data['type_declaration'] == 'import') {
                $acheteurs = array();
                $dr = acCouchdbManager::getClient('DR')->createFromCSVRecoltant($this->getUser()->getCampagne(), $tiers, $acheteurs);
                $dr->declaration_insee = $tiers->declaration_insee;
                $dr->declaration_commune = $tiers->declaration_commune;
                $dr->save();
                $this->getUser()->setFlash('flash_message', $this->getPartial('declaration/importMessage', array('acheteurs' => $acheteurs, 'post_message' => true)));
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'precedente') {
                $old_doc = $tiers->getDeclaration($dr_data['liste_precedentes_declarations']);
                if (!$old_doc) {
                    throw new Exception("Bug: " . $dr_data['liste_precedentes_declarations'] . " not found :()");
                }
                $doc = clone $old_doc;
                $doc->_id = 'DR-' . $tiers->cvi . '-' . $this->getUser()->getCampagne();
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->declaration_insee = $tiers->declaration_insee;
                $doc->declaration_commune = $tiers->declaration_commune;
                $doc->removeVolumes();
                $doc->remove('validee');
                $doc->remove('modifiee');
                $doc->remove('etape');
                $doc->remove('utilisateurs');
                $doc->remove('import_db2');
                $doc->update();
                $doc->save();
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            }
        }
        $this->redirect('@mon_espace_civa');
    }

    public function executeDownloadNotice() {
        return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "helpPdf/aide_recolte.pdf", "aide recolte.pdf");
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

        $check = $this->dr->check();
        $this->annee = $annee;

        $this->validLogErreur = $this->updateUrlLog($check['erreur']);
        $this->validLogVigilance = $this->updateUrlLog($check['vigilance']);

        $this->error = count($check['erreur']);
        $this->logVigilance = count($check['vigilance']);

        $this->getUser()->setAttribute('log_erreur', $this->validLogErreur);
        $this->getUser()->setAttribute('log_vigilance', $this->validLogVigilance);

        if ($this->askRedirectToPreviousEtapes()) {
            
            return $this->redirectByBoutonsEtapes();
        }

        if ($this->askRedirectToNextEtapes() && !$this->error && $request->isMethod(sfWebRequest::POST)) {
	        $this->dr->validate($tiers, $this->getUser()->getCompte(), $this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id'));
	        $this->dr->save();
	        $this->getUser()->initCredentialsDeclaration();
	      
	        $mess = 'Bonjour ' . $tiers->nom . ',

Vous venez de valider votre déclaration de récolte pour l\'année ' . date("Y") . '. Pour la visualiser rendez-vous sur votre espace civa : ' . sfConfig::get('app_base_url') . '/mon_espace_civa

Cordialement,

Le CIVA';

                //send email

            $message = $this->getMailer()->compose(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"),
                                                       $this->getUser()->getCompte()->email,
                                                       'CIVA - Validation de votre déclaration de récolte', $mess);
                
            if (!$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR)) {
                try {
                    $this->getMailer()->send($message);
                } catch (Exception $e) {
                    $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé, veuillez contacter CIVA');
                }
            }

            return $this->redirectByBoutonsEtapes();
        }
        
    }

    private function updateUrlLog($array) {
      $ret = array();
      foreach ($array as $log) {
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

        $this->getUser()->setFlash('flash_message', $flash_messages[$id]['log']);
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
        $key = 'DR-' . $tiers->cvi . '-' . $annee;
        $this->dr = acCouchdbManager::getClient()->find($key);
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
        $this->annee = $request->getParameter('annee', $this->getUser()->getCampagne());
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    public function executeSendPdf(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers('Recoltant');
        $dr = $this->getUser()->getDeclaration();

        $document = new ExportDRPdf($dr, $tiers, array($this, 'getPartial'));
        $document->generatePDF();

        $pdfContent = $document->output();
        $annee = $this->getRequestParameter('annee', null);

        // si l'on vient de la page de visualisation
        if($this->getRequestParameter('message', null) == "custom" && !is_null(($annee)))
        {
            $mess = 'Bonjour ' . $tiers->nom . ',

Vous trouverez ci-joint votre déclaration de récolte pour l\'année ' . $annee . '.

Cordialement,

Le CIVA';

        }else{

        $mess = 'Bonjour ' . $tiers->nom . ',

Vous trouverez ci-joint votre déclaration de récolte que vous venez de valider.

Cordialement,

Le CIVA';

        }

        //send email


        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo($this->getUser()->getCompte()->email)
                ->setSubject('CIVA - Votre déclaration de récolte')
                ->setBody($mess);


        $attachment = new Swift_Attachment($pdfContent, $document->getFileName(), 'application/pdf');
        $message->attach($attachment);
        
        try {
            $this->getMailer()->send($message);
        } catch (Exception $e) {

            $this->emailSend = false;
        }
        
        $this->emailSend = true;
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
        $this->redirect('@mon_espace_civa');
    }

    public function executeVisualisationAvantImport(sfWebRequest $request) {
        $this->annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());
        $this->acheteurs = array();
        $this->dr = acCouchdbManager::getClient('DR')->createFromCSVRecoltant($this->annee, $this->getUser()->getTiers('Recoltant'), $this->acheteurs);
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
            $this->redirect('@mon_espace_civa');
        } elseif ($boutons && in_array('next', array_keys($boutons))) {
            $this->redirectToNextEtapes();
        }
    }

    public function executeConfirmationMailDR(sfWebRequest $request) {

    }
}
