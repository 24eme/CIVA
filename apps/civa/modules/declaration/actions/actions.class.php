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

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceCiva(sfWebRequest $request) {
        if(ConfigurationClient::getConfiguration()->exist('dr_non_editable'))
            $this->dr_non_editable = ConfigurationClient::getConfiguration()->dr_non_editable;
        else
             $this->dr_non_editable = 0;
        
        $this->help_popup_action = "help_popup_mon_espace_civa";
        $this->setCurrentEtape('mon_espace_civa');
        $this->getUser()->initDeclarationCredentials();
        $this->campagnes = $this->getUser()->getTiers()->getDeclarationArchivesCampagne(($this->getUser()->getCampagne()-1));
        krsort($this->campagnes);
        $this->has_no_assices = $this->getUser()->getTiers()->hasNoAssices();
        $this->declaration = $this->getUser()->getDeclaration();
        if ($this->getUser()->hasCredential(myUser::CREDENTIAL_DECLARATION_BROUILLON) && $request->isMethod(sfWebRequest::POST)) {
            $this->processChooseDeclaration($request);
        }
    }

    protected function processChooseDeclaration(sfWebRequest $request) {
        $tiers = $this->getUser()->getTiers();
        $dr_data = $this->getRequestParameter('dr', null);
        if ($dr_data) {
            if ($dr_data['type_declaration'] == 'brouillon') {
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'supprimer') {
                $this->getUser()->getDeclaration()->delete();
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
            } elseif ($dr_data['type_declaration'] == 'precedente') {
                $old_doc = $tiers->getDeclaration($dr_data['liste_precedentes_declarations']);
                if (!$old_doc) {
                    throw new Exception("Bug: ".$dr_data['liste_precedentes_declarations']." not found :()");
                }
                $doc = clone $old_doc;
                $doc->_id = 'DR-'.$tiers->cvi.'-'.$this->getUser()->getCampagne();
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->removeVolumes();
                $doc->remove('validee');
                $doc->remove('modifiee');
                $doc->update();
                $doc->save();
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            }
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

        $tiers = $this->getUser()->getTiers();
        $annee = $this->getRequestParameter('annee', null);
        $key = 'DR-'.$tiers->cvi.'-'.$annee;
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);

        if ($request->isMethod(sfWebRequest::POST)) {

            if ($this->askRedirectToNextEtapes()) {
                $dr->validate($tiers);
                $dr->save();
                $this->getUser()->initDeclarationCredentials();

            $mess = 'Bonjour '.$tiers->nom.',

Vous venez de valider votre déclaration de récolte pour l\'année '.date("Y").'. Pour la visualiser rendez-vous sur votre espace civa : '.sfConfig::get('app_base_url').'mon_espace_civa

Cordialement,

Le CIVA';

            //send email
	    try {
	      $message = $this->getMailer()->compose('ne_pas_repondre@civa.fr',
						     $tiers->email,
						     'CIVA - Validation de votre déclaration de récolte',
						     $mess
						     );
	      $this->getMailer()->send($message);
	    }catch(Exception $e) {
	      $this->getUser()->setFlash('error', 'Erreur de configuration : Mail de confirmation non envoyé :(');
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
                if($lieu->getTotalSuperficie()==0){
                    array_push($this->validLogVigilance, array('url_log'=>$this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log' => $lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero')));
                    $this->logVigilance = true;

                }
                //check le lieu
                if ($lieu->isNonSaisie()) {
                    array_push($this->validLogErreur, array('url_log'=>$this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey())), 'log'=>$lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_lieu_non_saisie')));
                    $this->error = true;
                }else {
                    //verifie les rebeches pour les crémants
                    if($appellation->appellation=='CREMANT' && $lieu->getTotalVolumeForMinQuantite()>0) {
                        $rebeches=false;
                        foreach ($lieu->filter('cepage_') as $key => $cepage) {
                            if($key == 'cepage_RB') $rebeches = true;
                        }
                        if(!$rebeches) {
                            array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), 'cepage_RB')), $lieu->getKey(), 'log' => $lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_pas_rebeches')));
                            $this->error = true;
                        }

                    }

                    //check les cepages
                    foreach ($lieu->getConfig()->filter('cepage_') as $key => $cepage_config) {
                         if ($lieu->exist($key)) {
                             $cepage = $lieu->get($key);
                             if($cepage->getConfig()->hasMinQuantite()) {
                                $totalVolRatio = $lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->min_quantite;
                                $totalVolRevendique = $cepage->getTotalVolume();
                                    if( $totalVolRatio > $totalVolRevendique ) {
                                        array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey())), 'log'=>$lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite')));
                                        $this->error = true;
                                    }
                            }
                            if($cepage->getConfig()->hasMaxQuantite()) {
                                $totalVolRatio = $lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->max_quantite;
                                $totalVolRevendique = $cepage->getTotalVolume();
                                    if( $totalVolRatio < $totalVolRevendique ) {
                                        array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey())), 'log'=>$lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_max_quantite')) );
                                        $this->error = true;
                                    }
                            }
                            if($cepage->isNonSaisie()) {
                                array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey())), 'log'=> $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cepage_non_saisie')));
                                $this->error = true;
                            }else {
                                // vérifie le trop plein de DPLC
                                if($appellation->appellation=='ALSACEBLANC' && $cepage->getConfig()->hasRendement() && $cepage->getDplc() > 0){
                                            array_push($this->validLogVigilance, array('url_log'=>$this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_dplc')));
                                            $this->logVigilance = true;
                                }
                                foreach($cepage->filter('detail') as $details) {
                                    foreach ($details as $detail) {
                                         $detail_nom = '';
                                            if($detail->denomination!= '' || $detail->vtsgn!= '') {
                                                $detail_nom .= ' - ';
                                            }
                                            if($detail->denomination!= '') $detail_nom .= $detail->denomination.' ';
                                            if($detail->vtsgn!= '')        $detail_nom .= $detail->vtsgn.' ';
                                        if($detail->isNonSaisie()) {
                                            array_push($this->validLogErreur, array('url_log' => $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey())),'log'=> $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().$detail_nom.' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite') ));
                                            $this->error = true;
                                        }elseif($detail->hasMotifNonRecolteLibelle() && $detail->getMotifNonRecolteLibelle()=="Assemblage Edelswicker"){
                                            if(!$lieu->exist('cepage_ED') || !$lieu->cepage_ED->getVolume()){
                                                array_push($this->validLogErreur, array('url_log' =>$this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey())), 'log' => $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().$detail_nom.' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_ED_non_saisie')));
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
        $tiers = $this->getUser()->getTiers();
        $annee = $this->getRequestParameter('annee', null);
        $key = 'DR-'.$tiers->cvi.'-'.$annee;
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
        $this->forward404Unless($dr);

        $this->annee = $annee;

    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeConfirmation(sfWebRequest $request) {
        $this->setCurrentEtape('confirmation');
        $this->annee = $request->getParameter('annee');
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

}
