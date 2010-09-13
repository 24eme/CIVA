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
        $this->setCurrentEtape('mon_espace_civa');
        $this->campagnes = $this->getUser()->getTiers()->getDeclarationArchivesCampagne(($this->getUser()->getCampagne()-1));
        krsort($this->campagnes);
        $this->declaration = $this->getUser()->getDeclaration();
        if ($request->isMethod(sfWebRequest::POST)) {
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
        $this->setCurrentEtape('validation');

        $tiers = $this->getUser()->getTiers();
        $annee = $this->getRequestParameter('annee', null);
        $key = 'DR-'.$tiers->cvi.'-'.$annee;
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);

        $this->annee = $annee;

        $this->validLogErreur = array();
        $this->validLogVigilance = array();
        $i = 0 ;
        $this->error = false;
        $this->logVigilance = false;
        foreach ($dr->recolte->filter('appellation_') as $appellation) {
            $onglet = new RecolteOnglets($dr);
            foreach ($appellation->filter('lieu') as $lieu) {
                //check le total superficie
                if($lieu->getTotalSuperficie()==0){
                    $this->validLogVigilance[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()));
                    $this->validLogVigilance[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_superficie_zero');
                    $i++;
                    $this->logVigilance = true;
                }
                //check le lieu
                if ($lieu->isNonSaisie()) {
                    $this->validLogErreur[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey()));
                    $this->validLogErreur[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_lieu_non_saisie');
                    $i++;
                    $this->error = true;
                }else {
                    //verifie les rebeches pour les crÃ©mants
                    if($appellation->appellation=='CREMANT' && $lieu->getTotalVolumeForMinQuantite()>0) {
                        $rebeches=false;
                        foreach ($lieu->filter('cepage_') as $key => $cepage) {
                            if($key == 'cepage_RB') $rebeches = true;
                        }
                        if(!$rebeches) {
                            $this->validLogErreur[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), 'cepage_RB'));
                            $this->validLogErreur[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_pas_rebeches');
                            $i++;
                            $this->error = true;
                        }

                    }
                    //check les cepages
                    foreach ($lieu->filter('cepage_') as $key => $cepage) {
		      if($cepage->getConfig()->hasMinQuantite()) {
			$totalVolRatio = $lieu->getTotalVolumeForMinQuantite() * $cepage->getConfig()->min_quantite;
			$totalVolRevendique = $cepage->getTotalVolume();
                            if( $totalVolRatio > $totalVolRevendique ) {
                                $this->validLogErreur[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey()));
                                $this->validLogErreur[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cremant_min_quantite');
                                $i++;
                                $this->error = true;
                            }
                        }
                        if($cepage->isNonSaisie()) {
                            $this->validLogErreur[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey()));
                            $this->validLogErreur[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_cepage_non_saisie');
                            $i++;
                            $this->error = true;
                        }else {
                            if($cepage->getTotalDPLC()>$appellation->getTotalDPLC()){
                                $this->validLogVigilance[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey()));
                                $this->validLogVigilance[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_dplc');
                                $i++;
                                $this->logVigilance = true;
                            }
                            foreach($cepage->filter('detail') as $details) {
                                foreach ($details as $detail) {
                                    if($detail->isNonSaisie()) {
                                        $detail_nom = '';
                                        if($detail->denomination!= '' || $detail->vtsgn!= '') {
                                            $detail_nom .= ' - ';
                                        }
                                        if($detail->denomination!= '') $detail_nom .= $detail->denomination.' ';
                                        if($detail->vtsgn!= '')        $detail_nom .= $detail->vtsgn.' ';
                                        $this->validLogErreur[$appellation->getKey()][$i]['url'] = $this->generateUrl('recolte', $onglet->getUrlParams($appellation->getKey(), $lieu->getKey(), $cepage->getKey()));
                                        $this->validLogErreur[$appellation->getKey()][$i]['log'] = $lieu->getLibelleWithAppellation().' - '.$cepage->getLibelle().$detail_nom.' => '.sfCouchdbManager::getClient('Messages')->getMessage('err_log_detail_non_saisie');
                                        $i++;
                                        $this->error = true;
                                    }
                                }
                            }
                        }
                    }
                }


                //check les details
            }
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeVisualisation(sfWebRequest $request) {
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

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

}
