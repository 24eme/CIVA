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
            } elseif ($dr_data['type_declaration'] == 'vierge') {
                $doc = new DR();
                $doc->set('_id', 'DR-' . $tiers->cvi . '-' . $this->getUser()->getCampagne());
                $doc->cvi = $tiers->cvi;
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->declaration_insee = $tiers->declaration_insee;
                $doc->declaration_commune = $tiers->declaration_commune;
                $doc->save();
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'import') {
                $import_from = array();
                $dr = sfCouchdbManager::getClient('DR')->createFromCSVRecoltant($tiers, $import_from);
                $dr->save();
                $msg = '<p>' . sfCouchdbManager::getClient('Messages')->getMessage('msg_declaration_ecran_warning_pre_import') . '</p>';
                $msg .= '<ul>';
                foreach ($import_from as $i) {
                    $msg .= '<li>' . $i->nom . '</li>';
                }
                $msg .= '</ul>';
                $msg .= '<p>' . sfCouchdbManager::getClient('Messages')->getMessage('msg_declaration_ecran_warning_post_import') . '</p>';
                $this->getUser()->setFlash('flash_message', $msg);
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'precedente') {
                $old_doc = $tiers->getDeclaration($dr_data['liste_precedentes_declarations']);
                if (!$old_doc) {
                    throw new Exception("Bug: " . $dr_data['liste_precedentes_declarations'] . " not found :()");
                }
                $doc = clone $old_doc;
                $doc->_id = 'DR-' . $tiers->cvi . '-' . $this->getUser()->getCampagne();
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->removeVolumes();
                $doc->remove('validee');
                $doc->remove('modifiee');
                $doc->remove('etape');
                $doc->update();
                $doc->save();
                $this->getUser()->setFlash('flash_message', sfCouchdbManager::getClient('Messages')->getMessage('msg_declaration_ecran_warning_precedente'));
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            }
        }
        $this->redirect('@mon_espace_civa');
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

    public function executeDownloadNotice() {
        return $this->renderPdf(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "images/aide.pdf", "aide.pdf");
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
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);

        if ($request->isMethod(sfWebRequest::POST)) {

            if ($this->askRedirectToNextEtapes()) {
                $dr->validate($tiers, $this->getUser()->getCompte());
                $dr->utilisateurs->validation->add($this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id'), date('d/m/Y'));
                $dr->save();
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
            }

            $this->redirectByBoutonsEtapes();
        }
        $this->annee = $annee;

        $this->validLogErreur = array();
        $this->validLogVigilance = array();
        $this->error = false;
        $this->logVigilance = false;

        foreach ($dr->recolte->filter('appellation_') as $appellation) {
            $onglet = new RecolteOnglets($dr);
            foreach ($appellation->filter('lieu') as $lieu) {
                //check le total superficie
                if ($lieu->getTotalSuperficie() == 0) {
                    array_push($this->validLogVigilance, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero')));
                    $this->logVigilance = true;
                }
                //check le lieu
                if ($lieu->isNonSaisie()) {
                    array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_lieu_non_saisie')));
                    $this->error = true;
                } else {
                    //verifie les rebeches pour les crémants
                    if ($appellation->getConfig()->appellation == 'CREMANT' && round($lieu->getTotalVolumeForMinQuantite(), 2) > 0) {
                        $rebeches = false;
                        foreach ($lieu->filter('couleur') as $key => $couleur)
                            foreach ($couleur->filter('cepage_') as $key => $cepage)
                                if ($key == 'cepage_RB')
                                    $rebeches = true;
                        if (!$rebeches) {
                            array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $lieu->getCouleur('cepage_RB')->getKey(), 'cepage_RB')), $lieu->getKey(), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_pas_rebeches')));
                            $this->error = true;
                        }
                    }

                    //Verifie que le recapitulatif des ventes est rempli
                    if (!$lieu->hasCompleteRecapitulatifVente()) {
                        array_push($this->validLogVigilance, array('url_log' => $this->generateUrl('recolte_recapitulatif', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie')));
                        $this->logVigilance = true;
                    }

                    //Verifie que le recapitulatif des ventes à du dplc si le total dplc du lieu est > 0
                    if ($lieu->getConfig()->hasRendement() && $lieu->hasAcheteurs() && $lieu->hasCompleteRecapitulatifVente() && $lieu->getDplc() > 0 && !$lieu->getTotalDontDplcRecapitulatifVente()) {
                        array_push($this->validLogVigilance, array('url_log' => $this->generateUrl('recolte_recapitulatif', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_non_saisie_dplc')));
                        $this->logVigilance = true;
                    }

                    //Verifie que le recapitulatif des ventes n'est pas supérieur aux totaux
                    if (!$lieu->isValidRecapitulatifVente()) {
                        array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte_recapitulatif', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_recap_vente_invalide')));
                        $this->error = true;
                    }

                    //check les cepages
                    foreach ($lieu->filter('couleur') as $couleur) {
                        foreach ($couleur->getConfig()->filter('cepage_') as $key => $cepage_config) {

                            if ($couleur->exist($key)) {
                                $cepage = $couleur->get($key);
                                if ($cepage->getConfig()->hasMinQuantite()) {
                                    $totalVolRatio = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->min_quantite, 2);
                                    $totalVolRevendique = $cepage->getTotalVolume();
                                    if ($totalVolRatio > $totalVolRevendique) {
                                        array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite')));
                                        $this->error = true;
                                    }
                                }
                                if ($cepage->getConfig()->hasMaxQuantite()) {
                                    $totalVolRatio = round($lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->max_quantite, 2);
                                    $totalVolRevendique = $cepage->getTotalVolume();
                                    if ($totalVolRatio < $totalVolRevendique) {
                                        array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_max_quantite')));
                                        $this->error = true;
                                    }
                                }
                                if ($cepage->isNonSaisie()) {
                                    array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_cepage_non_saisie')));
                                    $this->error = true;
                                } else {
                                    // vérifie le trop plein de DPLC
                                    if ($appellation->getConfig()->appellation == 'ALSACEBLANC' && $cepage->getConfig()->hasRendement() && round($cepage->getDplc(), 2) > 0) {
                                        array_push($this->validLogVigilance, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_dplc')));
                                        $this->logVigilance = true;
                                    }
                                    foreach ($cepage->filter('detail') as $details) {
                                        foreach ($details as $detail) {
                                            $detail_nom = '';
                                            if ($detail->denomination != '' || $detail->vtsgn != '') {
                                                $detail_nom .= ' - ';
                                            }
                                            if ($detail->denomination != '')
                                                $detail_nom .= $detail->denomination . ' ';
                                            if ($detail->vtsgn != '')
                                                $detail_nom .= $detail->vtsgn . ' ';
                                            if ($detail->isNonSaisie()) {
                                                array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_detail_non_saisie')));
                                                $this->error = true;
                                            } elseif ($detail->hasMotifNonRecolteLibelle() && $detail->getMotifNonRecolteLibelle() == "Assemblage Edelzwicker") {
                                                if (!$couleur->exist('cepage_ED') || !$couleur->cepage_ED->getTotalVolume()) {
                                                    array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $couleur->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation() . ' - ' . $cepage->getLibelle() . $detail_nom . ' => ' . sfCouchdbManager::getClient('Messages')->getMessage('err_log_ED_non_saisie')));
                                                    $this->error = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->getUser()->setAttribute('log_erreur', $this->validLogErreur);
        $this->getUser()->setAttribute('log_vigilance', $this->validLogVigilance);
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
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
        $this->forward404Unless($dr);

        try {
            if (!$dr->updated)
                throw new Exception();
        } catch (Exception $e) {
            $dr->update();
            $dr->save();
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

        $mess = 'Bonjour ' . $tiers->nom . ',

Vous trouverez ci-joint votre déclaration de récolte que vous venez de valider.

Cordialement,

Le CIVA';

        //send email


        $message = Swift_Message::newInstance()
                ->setFrom(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo($this->getUser()->getCompte()->email)
                ->setSubject('CIVA - Votre déclaration de récolte')
                ->setBody($mess);


        $file_name = $dr->_id . '.pdf';

        $attachment = new Swift_Attachment($pdfContent, $file_name, 'application/pdf');
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
            if (!$dr->exist('etape')) {
                $dr->add('etape', 'validation');
            } else {
                $dr->set('etape', 'validation');
            }
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

}
