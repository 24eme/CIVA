<?php

/**
 * recolte actions.
 *
 * @package    civa
 * @subpackage recolte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class recolteActions extends EtapesActions {

    public function preExecute() {
        parent::preExecute();
        $this->setCurrentEtape('recolte');
        $this->declaration = $this->getUser()->getDeclaration();
        $this->help_popup_action = "help_popup_DR";
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();
        $this->initAcheteurs();
        $this->initPrecDR();

        if (!$this->details->count() > 0) {
            if ($this->getUser()->hasFlash('flash_message')) {
                $this->getUser()->setFlash('flash_message', $this->getUser()->getFlash('flash_message'));
            }
            $this->redirect($this->onglets->getUrl('recolte_add'));

        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();
        $this->initAcheteurs();
        $this->initPrecDR();

        $this->detail_action_mode = 'update';
        $this->is_detail_edit = true;

        $this->detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($this->detail_key));

        $this->form_detail = new RecolteForm($this->details->get($this->detail_key), $this->getFormDetailsOptions());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeAdd(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();
        $this->initAcheteurs();
        $this->initPrecDR();

        $this->detail_action_mode = 'add';
        $this->is_detail_edit = true;

        $detail = $this->details->add();
        $this->detail_key = $this->details->count() - 1;

        $this->form_detail = new RecolteForm($detail, $this->getFormDetailsOptions());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeDelete(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();

        $detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($detail_key));

        $this->details->remove($detail_key);
        if ($this->details->count() == 0) {
            $this->onglets->getCurrentLieu()->remove($this->onglets->getCurrentKeyCepage());
        }
        $this->declaration->save();

        $this->redirect($this->onglets->getUrl('recolte'));
    }

    public function executeMotifNonRecolte(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->initOnglets($request);
        $this->initDetails();

        $this->detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($this->detail_key));

        if($this->onglets->getCurrentKeyAppellation() == "appellation_ALSACEBLANC") $nonEdel = true;
        else $nonEdel = false;
        
        $this->form = new RecolteMotifNonRecolteForm($this->details->get($this->detail_key), array('nonEdel'=> $nonEdel));

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form ->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                return $this->renderText(json_encode(array('action' => 'redirect',
                        'data' => $this->generateUrl('recolte', array_merge($this->onglets->getUrlParams(), array('refresh' => uniqid()))))));
            }
            return $this->renderText(json_encode(array('action' => 'render',
                    'data' => $this->getPartial('recolte/motifNonRecolteForm', array('onglets' => $this->onglets ,'form' => $this->form, 'detail_key' => $this->detail_key)))));
        }

        return $this->renderText($this->getPartial('recolte/motifNonRecolteForm', array('onglets' => $this->onglets ,'form' => $this->form, 'detail_key' => $this->detail_key)));
    }

    public function executeAjoutAppellationAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());

        $this->initOnglets($request);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form_ajout_appellation ->bind($request->getParameter($this->form_ajout_appellation->getName()));
            if ($this->form_ajout_appellation->isValid()) {
                $this->form_ajout_appellation->save();
                if ($this->form_ajout_appellation->needLieu()) {
                    $request->setParameter('force_appellation', $this->form_ajout_appellation->getValue('appellation'));
                    $request->setMethod(sfWebRequest::GET);
                    $this->forward('recolte', 'ajoutLieuAjax');
                    //return $this->redirect(array_merge($this->onglets->getUrl('recolte_add_lieu'), array('force_appellation' => $this->form_ajout_appellation->getValue('appellation'))));
                } else {
                    return $this->renderText(json_encode(array('action' => 'redirect',
                            'data' => $this->generateUrl('recolte_add', $this->onglets->getUrlParams($this->form_ajout_appellation->getValue('appellation'))))));
                }
            }
        }

        return $this->renderText(json_encode(array('action' => 'render',
                'data' => $this->getPartial('ajoutAppellationForm', array('onglets' => $this->onglets ,'form' => $this->form_ajout_appellation)))));
    }

    public function executeAjoutLieuAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());

        $this->initOnglets($request);

        if ($request->hasParameter('force_appellation')) {
            $this->forward404Unless($this->declaration->recolte->getConfig()->exist($request->getParameter('force_appellation')));
            $this->url_ajout_lieu = array_merge($this->onglets->getUrl('recolte_add_lieu', null, null, null, null), array('force_appellation' => $request->getParameter('force_appellation')));
            $this->form_ajout_lieu = new RecolteAjoutLieuForm($this->declaration->recolte->add($request->getParameter('force_appellation')));
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form_ajout_lieu->bind($request->getParameter($this->form_ajout_lieu->getName()));
            if ($this->form_ajout_lieu->isValid()) {
                $this->form_ajout_lieu->save();
                return $this->renderText(json_encode(array('action' => 'redirect',
                        'data' => $this->generateUrl('recolte_add', $this->onglets->getUrlParams($this->form_ajout_lieu->getObject()->getKey(), $this->form_ajout_lieu->getValue('lieu'))))));
            }
        }

        return $this->renderText(json_encode(array('action' => 'render',
                'data' => $this->getPartial('ajoutLieuForm', array('onglets' => $this->onglets ,'form' => $this->form_ajout_lieu, 'url' => $this->url_ajout_lieu)))));


    }

    public function executeAjoutAcheteurAjax(sfWebRequest $request) {
        if ($request->isXmlHttpRequest() && $request->isMethod(sfWebRequest::POST)) {
            $cvi = $request->getParameter('cvi');
            $form_name = $request->getParameter('form_name');
            $form = RecolteForm::getNewAcheteurItemAjax($form_name, $cvi);
            return $this->renderPartial('formAcheteursItem', array('form' => $form, 'type' => $form_name));
        } else {
            $this->forward404();
        }
    }

    public function executeRecapitulatif(sfWebRequest $request) {

        $this->help_popup_action = "help_popup_recapitulatif_ventes";

        $this->initOnglets($request);
        $this->initPrecDR();
        
        $dr = $this->getUser()->getDeclaration();
        $this->appellationlieu = $this->onglets->getLieu();
        if($this->onglets->getCurrentAppellation()->getKey() == 'appellation_GRDCRU'){
            $this->isGrandCru = true;
        }else{
            $this->isGrandCru = false;
        }
        $this->form = new RecapitulatifForm($this->appellationlieu);

        $forms = $this->form->getEmbeddedForms();
        if (!count($forms) && $request->getParameter('redirect')) {
            return $this->redirect($this->onglets->getNextUrl());
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                return $this->redirect($this->onglets->getNextUrl());
            }
        }

    }
    
    public function executeRendementsMaxAjax(sfWebRequest $request) {
    	$this->forward404Unless($request->isXmlHttpRequest());

    	$dr = $this->declaration;
    	$this->rendement = array();
    	$this->min_quantite = null;
    	$this->max_quantite = null;
    	foreach ($dr->recolte->getConfig()->filter('appellation_') as $key_appellation => $appellation_config) {
    		if ($dr->recolte->exist($key_appellation)) {
    			$appellation = $dr->recolte->get($key_appellation);
    			foreach ($appellation->getLieux() as $lieu) {
    				if ($lieu->getConfig()->getRendementAppellation() == -1)
    				continue;
    				if ($lieu->getConfig()->hasRendementCouleur()) {
    					//$this->rendement[$appellation->getLibelle()]['appellation'][''][$lieu->getLibelle()] = 1;
    					foreach ($lieu->getConfig()->getCouleurs() as $couleurConfig) {
	    					$rd = $couleurConfig->getRendementCouleur();
    						$this->rendement[$appellation->getLibelle()]['cepage'][$rd][$couleurConfig->getLibelle()] = 1;
    					}
    				} else {
    					
	    				if ($lieu->getConfig()->getRendementAppellation()) {
	    					$rd = $lieu->getConfig()->getRendementAppellation();
	    					$this->rendement[$appellation->getLibelle()]['appellation'][$rd][$lieu->getLibelle()] = 1;
	    				}
						foreach($lieu->getCouleurs() as $couleur) {
	    				foreach ($couleur->getConfig()->filter('^cepage') as $key => $cepage_config) {
	    					if($cepage_config->hasMinQuantite()) {
	    						$this->min_quantite = $cepage_config->min_quantite * 100 ;
	    						$this->max_quantite = $cepage_config->max_quantite * 100 ;
	    					}
	    					if($cepage_config->getRendement()) {
	    						$rd = $cepage_config->getRendement();
	    						if($appellation->getConfig()->hasManyLieu()) {
	    							$this->rendement[$appellation->getLibelle()]['cepage'][$rd][$lieu->getLibelle()] = 1;
	    						}else {
	    							$this->rendement[$appellation->getLibelle()]['cepage'][$rd][$cepage_config->getLibelle()] = 1;
	    						}
	    					}
	    				}
						}
    				}
    			}
    		}
    	}
    	return $this->renderPartial('recolte/popupRendementsMax', array('rendement'=> $this->rendement,
                                                                        'min_quantite'=> $this->min_quantite, 
                                                                        'max_quantite'=> $this->max_quantite));
    }

    protected function processFormDetail($form, $request) {
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
            $detail = $form->save();
            if (!$this->onglets->getCurrentCepage()->getConfig()->hasNoMotifNonRecolte() && $detail->exist('motif_non_recolte')) {
                $this->getUser()->setFlash('open_popup_ajout_motif', $detail->getKey());
            }
            $this->redirect($this->onglets->getUrl('recolte'));
        }
    }

    protected function initOnglets(sfWebRequest $request) {

        preg_match('/(?P<appellation>\w+)-?(?P<lieu>\w*)/', $request->getParameter('appellation_lieu', null), $appellation_lieu);
        $appellation = null;
        if (isset($appellation_lieu['appellation'])) {
            $appellation = $appellation_lieu['appellation'];
        }
        $lieu = null;
        if (isset($appellation_lieu['lieu'])) {
            $lieu = $appellation_lieu['lieu'];
        }
        
        preg_match('/(?P<couleur>(\w*-)|)(?P<cepage>\w+)/', $request->getParameter('couleur_cepage', null), $couleur_cepage);
        $couleur = 'couleur';
        if (isset($couleur_cepage['couleur']) && $couleur_cepage['couleur']) {
            $couleur = preg_replace('/-$/', '', $couleur_cepage['couleur']);
        }
        $cepage = null;
        if (isset($couleur_cepage['cepage'])) {
            $cepage = $couleur_cepage['cepage'];
        }
        
        if(!$cepage) {
           $couleur = null; 
        }

        if ($this->declaration->exist('validee') && $this->declaration->validee) {
            $this->getUser()->setFlash('msg_info', 'Vous consultez une DR validée ('.$this->declaration->validee.')!!');
        }

        $this->onglets = new RecolteOnglets($this->declaration, $this->_etapes_config->previousUrl(), $this->_etapes_config->nextUrl());
        $this->onglets->init($appellation, $lieu, $couleur, $cepage);
        /*if (!$this->onglets || ($request->getParameter('appellation_lieu', null) == '' && $request->getParameter('couleur_cepage', null) == '') || !$this->onglets->init($appellation, $lieu, $cepage)) {
            $this->redirect($this->onglets->getUrl('recolte', null, null, null, null));
        }*/

        /*** AjOUT APPELLATION ***/
        $this->form_ajout_appellation = new RecolteAjoutAppellationForm($this->declaration->recolte);
        $this->form_ajout_lieu = null;
        $this->url_ajout_lieu = null;
        if ($this->onglets->getCurrentAppellation()->getConfig()->hasManyLieu()) {
            $this->form_ajout_lieu = new RecolteAjoutLieuForm($this->onglets->getCurrentAppellation());
            $this->url_ajout_lieu = $this->onglets->getUrl('recolte_add_lieu', null, null, null, null, null);
        }
    }

    protected function initDetails() {
        $this->details = $this->onglets->getCurrentLieu()->add($this->onglets->getCurrentKeyCouleur())->add($this->onglets->getCurrentKeyCepage())->add('detail');
        $this->nb_details_current = $this->details->count();

        $this->detail_key = null;
        $this->detail_action_mode = null;
        $this->form_detail = null;
    }

    protected function initAcheteurs() {
        $this->has_acheteurs_mout = ($this->onglets->getCurrentAppellation()->getConfig()->mout == 1);
        $this->acheteurs = $this->declaration->get('acheteurs')->get($this->onglets->getCurrentKeyAppellation());
    }

    protected function initPrecDR(){
        $this->campagnes = $this->getUser()->getTiers('Recoltant')->getDeclarationsArchivesSince(($this->getUser()->getCampagne()-1));
        krsort($this->campagnes);
    }

    protected function getFormDetailsOptions() {
        return array(
                'lieu_required' => $this->onglets->getCurrentAppellation()->getConfig()->hasLieuEditable(),
                'superficie_required' => $this->onglets->getCurrentCepage()->getConfig()->isSuperficieRequired(),
                'acheteurs_negoce' => $this->acheteurs->negoces,
                'acheteurs_cooperative' => $this->acheteurs->cooperatives,
                'acheteurs_mout' => $this->acheteurs->mouts,
                'has_acheteurs_mout' => $this->has_acheteurs_mout);
    }
}