<?php

/**
 * recolte actions.
 *
 * @package    civa
 * @subpackage recolte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dr_recolteActions extends _DRActions {

    public function preExecute() {
        parent::preExecute();
        $this->setCurrentEtape('recolte');
        $this->declaration = $this->getRoute()->getDR();
        $this->help_popup_action = "help_popup_DR";
        if (!$this->declaration->recolte->getNoeudAppellations()->hasOneOrMoreAppellation()) {
            $this->redirectToNextEtapes($this->declaration);
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        foreach($this->declaration->getAppellationsAvecVtsgn() as $appellation) {
            return $this->redirect('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' => $appellation["hash"]));
        }

        return $this->redirect('dr_repartition_acheteurs', $this->declaration);
    }


    public function executeProduit(sfWebRequest $request) {
        if(!$this->declaration->exist($request->getParameter('hash'))) {

            return $this->redirect('dr_recolte_produit_ajout', array('id' => $this->declaration->_id, 'hash' => $request->getParameter('hash')));
        }

        $this->produit = $this->declaration->get($request->getParameter('hash'));
        $this->etablissement = $this->declaration->getEtablissement();
        $this->initDetails();
        $this->initAcheteurs();
        $this->initPrecDR();

        $this->setTemplate("recolte");
    }

    public function executeProduitAjout(sfWebRequest $request) {
        $this->produit = $this->declaration->getOrAdd($request->getParameter('hash'));
        $this->etablissement = $this->declaration->getEtablissement();
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

    public function executeProduitEdition(sfWebRequest $request) {
        $this->produit = $this->declaration->get($request->getParameter('hash'));
        $this->etablissement = $this->declaration->getEtablissement();
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

    public function executeProduitSuppression(sfWebRequest $request) {
        $this->produit = $this->declaration->get($request->getParameter('hash'));
        $this->initDetails();

        $detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($detail_key));

        $this->details->remove($detail_key);
        $this->declaration->update();
        $this->declaration->utilisateurs->edition->add($this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id'), date('d/m/Y'));
        $this->declaration->save();

        $this->redirect('dr_recolte_produit', array('sf_subject' => $this->declaration, 'hash' => $this->produit->getHash()));
    }

    public function executeProduitNoeud(sfWebRequest $request) {
        if(
        preg_match("|(mention[A-Z]*)|", $request->getParameter('origine'), $matchesOrigine) &&
        preg_match("|(mention[A-Z]*)|", $request->getParameter('hash'), $matchesDestination) &&
        $matchesOrigine[1] != $matchesDestination[1] &&
        preg_match("|".str_replace($matchesDestination[1], $matchesOrigine[1], $request->getParameter('hash'))."|",  $request->getParameter('origine'))) {

            return $this->redirect("dr_recolte_correspondance_mention", array('id' => $this->declaration->_id, 'hash' => $request->getParameter('origine'), 'mention' => $matchesDestination[1], 'hash_fallback' => $request->getParameter('hash')));
        }

        if(in_array($request->getParameter('hash'), array("mentionVT", "mentionSGN"))) {
            foreach($this->declaration->recolte->getMentions() as $mention) {
                if($mention->getKey() != $request->getParameter('hash')) {
                    continue;
                }

                $this->noeud = $mention;
                break;
            }
        }


        if(!isset($this->noeud)) {
            $this->noeud = $this->declaration->getOrAdd($request->getParameter('hash'));
        }

        if($this->noeud instanceof DRRecolteMention) {
            foreach($this->noeud->getConfig()->getLieux() as $lieuConfig) {
                $hash = HashMapper::inverse($lieuConfig->getHash());
                if(!$this->declaration->exist($hash)) {

                    continue;
                }
                $this->noeud = $this->declaration->get(HashMapper::inverse($lieuConfig->getHash()));
                break;
            }
        }

        foreach($this->noeud->getConfig()->getProduits() as $produitConfig) {
            if($produitConfig->exist('attributs/no_dr') && $produitConfig->get('attributs/no_dr')) {
                continue;
            }

            $hash = HashMapper::inverse($produitConfig->getHash());

            if (!count($this->noeud->getProduitsDetails())) {

                return $this->redirect('dr_recolte_produit', array('sf_subject' => $this->declaration, 'hash' => $hash));
            }

            if($this->declaration->exist($hash) && count($this->declaration->get($hash)->getProduitsDetails())) {

                return $this->redirect('dr_recolte_produit', array('sf_subject' => $this->declaration, 'hash' => $hash));
            }
        }
    }

    public function executeProduitCorrepondanceMention(sfWebRequest $request) {
        $hashMention = preg_replace("|/mention[A-Z]*|", "/".$request->getParameter('mention'), $request->getParameter('hash'));
        $hashMentionConfig = HashMapper::convert($hashMention);

        if($this->declaration->getConfig()->exist(HashMapper::convert($hashMention)) &&
           $this->declaration->exist(HashMapper::inverse($this->declaration->getConfig()->get($hashMentionConfig)->getLieu()->getHash()))) {
            return $this->redirect('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' => $hashMention));
        }

        $noeud = $this->declaration->getOrAdd($request->getParameter('hash'))->getLieu();
        $hashMention = preg_replace("|/mention[A-Z]*|", "/".$request->getParameter('mention'), $noeud->getHash());

        if(!$this->declaration->exist($hashMention)) {

            return $this->redirect('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' => $request->getParameter('hash_fallback')));
        }

        return $this->redirect('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' => $hashMention));
    }

    public function executeProduitNoeudPrecedent(sfWebRequest $request) {
        $this->noeud = $this->declaration->getOrAdd($request->getParameter('hash'));
        $this->etablissement = $this->declaration->getEtablissement();
        if($this->noeud->getPreviousSister()) {
            return $this->redirect('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' => $this->noeud->getPreviousSister()->getHash()));
        }

        if($this->noeud->getParent() instanceof DRRecolte) {

            return $this->redirect('dr_autres', array('id' => $this->declaration->_id));
        }

        return $this->redirect('dr_recolte_noeud_precedent', array('sf_subject' => $this->declaration, 'hash' => $this->noeud->getParent()->getHash()));
    }


    public function executeProduitNoeudSuivant(sfWebRequest $request) {
        $this->noeud = $this->declaration->getOrAdd($request->getParameter('hash'));
        $this->etablissement = $this->declaration->getEtablissement();
        if($this->noeud->getNextSister()) {
            return $this->redirect('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' => $this->noeud->getNextSister()->getHash()));
        }

        if($this->noeud->getParent() instanceof DRRecolte) {

            return $this->redirect('dr_autres', array('id' => $this->declaration->_id));
        }

        return $this->redirect('dr_recolte_noeud_suivant', array('sf_subject' => $this->declaration, 'hash' => $this->noeud->getParent()->getHash()));
    }

    public function executeRecapitulatif(sfWebRequest $request) {
        $this->help_popup_action = "help_popup_recapitulatif_ventes";
        $this->noeud = $this->declaration->getOrAdd($request->getParameter('hash'));
        $this->appellations = $this->declaration->getAppellationsAvecVtsgn();

        $this->initPrecDR();

        $this->appellationlieu = $this->noeud;
        $this->isGrandCru = $this->noeud->getAppellation()->getConfig()->hasManyLieu();
        $this->form = new RecapitulatifContainerForm($this->appellationlieu);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                if($request->getParameter("is_validation_interne")) {
                    $this->getUser()->setFlash('recapitulatif_confirmation', 'Votre saisie a bien été enregistrée');

                    return $this->redirect('dr_recolte_recapitulatif', array('sf_subject' => $this->declaration, 'hash' => $this->noeud->getHash()));
                } else {

                    return $this->redirect('dr_recolte_noeud_suivant', array('sf_subject' => $this->declaration, 'hash' => $this->noeud->getHash()));
                }
            }
        }
    }

    public function executeMotifNonRecolte(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->produit = $this->declaration->get($request->getParameter('hash'));
        $this->initDetails();

        $this->detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($this->detail_key));

        if(preg_match("|/appellation_ALSACEBLANC/mention/|", $this->produit->getHash())) $nonEdel = true;
        else $nonEdel = false;

        $this->form = new RecolteMotifNonRecolteForm($this->details->get($this->detail_key), array('nonEdel'=> $nonEdel));

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form ->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();

                return $this->renderText(json_encode(array('action' => 'redirect',
                        'data' => $this->generateUrl('dr_recolte_noeud', array("id" => $this->declaration->_id, 'hash' => $this->produit->getHash())))));
            }

            return $this->renderText(json_encode(array('action' => 'render',
                    'data' => $this->getPartial('dr_recolte/motifNonRecolteForm', array('produit' => $this->produit, 'form' => $this->form, 'detail_key' => $this->detail_key)))));
        }

        return $this->renderText($this->getPartial('dr_recolte/motifNonRecolteForm', array('produit' => $this->produit,'form' => $this->form, 'detail_key' => $this->detail_key)));
    }

    public function executeAjoutAppellationAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->initDetails();

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form_ajout_appellation->bind($request->getParameter($this->form_ajout_appellation->getName()));
            if ($this->form_ajout_appellation->isValid()) {
                $this->form_ajout_appellation->save();
                if ($this->form_ajout_appellation->needLieu()) {
                    $request->setParameter('force_appellation', $this->form_ajout_appellation->getValue('appellation_hash'));
                    $request->setMethod(sfWebRequest::GET);

                    return $this->forward('dr_recolte', 'ajoutLieuAjax');
                } else {
                    return $this->renderText(json_encode(array('action' => 'redirect',
                            'data' => $this->generateUrl('dr_recolte_noeud', array('sf_subject' => $this->declaration, 'hash' =>  $this->form_ajout_appellation->getValue('appellation_hash'))))));
                }
            }
        }

        return $this->renderText(json_encode(array('action' => 'render',
                'data' => $this->getPartial('ajoutAppellationForm', array('onglets' => $this->onglets ,'form' => $this->form_ajout_appellation)))));
    }

    public function executeAjoutLieuAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        $this->initDetails();

        if ($request->hasParameter('force_appellation')) {
            $this->url_ajout_lieu = $this->generateUrl('dr_recolte_add_lieu', array('force_appellation' => $request->getParameter('force_appellation'), 'id' => $this->declaration->_id));
            $this->form_ajout_lieu = new RecolteAjoutLieuForm($this->declaration->get($request->getParameter('force_appellation'))->getAppellation());
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form_ajout_lieu->bind($request->getParameter($this->form_ajout_lieu->getName()));
            if ($this->form_ajout_lieu->isValid()) {
                $this->form_ajout_lieu->save();

                return $this->renderText(json_encode(array('action' => 'redirect',
                        'data' => $this->generateUrl('dr_recolte_noeud', array('id' => $this->declaration->_id, 'hash' => $this->form_ajout_lieu->getValue('lieu_hash'))))));
            }
        }

        return $this->renderText(json_encode(array('action' => 'render',
                'data' => $this->getPartial('ajoutLieuForm', array('form' => $this->form_ajout_lieu, 'url' => $this->url_ajout_lieu)))));
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

    public function executeRendementsMaxAjax(sfWebRequest $request) {
    	//$this->forward404Unless($request->isXmlHttpRequest());
    	$dr = $this->declaration;
    	$this->rendement = array();
    	$this->min_quantite = null;
    	$this->max_quantite = null;
    	foreach ($dr->getAppellationsAvecVtsgn() as $key_appellation => $appellationInfos) {
			foreach ($appellationInfos["noeuds"] as $mention) {
                $appellation = $mention->getAppellation();

                if(in_array($key_appellation, array("mentionVT", "mentionSGN"))) {
                    foreach($appellation->getConfig()->getMentions() as $mentionConfig) {
                        if($mentionConfig->getRendementCepage() <= 0) {
                            continue;
                        }
                        $this->rendement["Mention"]['cepage'][$mentionConfig->getRendementCepage()][$mentionConfig->getLibelle()] = 1;
                    }
                    continue;
                }

                foreach($mention->getLieux() as $lieu) {
                    if ($lieu->getConfig()->getRendementNoeud() == -1) {
                        continue;
                    }
    				if ($lieu->getConfig()->existRendementCouleur()) {
    					foreach ($lieu->getConfig()->getCouleurs() as $couleurConfig) {
        					$rd = $couleurConfig->getRendementCouleur();
    						$this->rendement[$appellation->getLibelle()]['cepage'][$rd][$couleurConfig->getLibelle()] = 1;
    					}
    				} else {
        				if ($lieu->getConfig()->getRendementNoeud()) {
        					$rd = $lieu->getConfig()->getRendementNoeud();
        					$this->rendement[$appellation->getLibelle()]['appellation'][$rd][$lieu->getLibelle()] = 1;
        				}
    					foreach($lieu->getCouleurs() as $couleur) {
    	    				foreach ($couleur->getConfig()->getCepages() as $key => $cepage_config) {
    	    					if($cepage_config->hasMinQuantite()) {
    	    						$this->min_quantite = $cepage_config->attributs->min_quantite * 100 ;
    	    						$this->max_quantite = $cepage_config->attributs->max_quantite * 100 ;
    	    					}
    	    					if($cepage_config->getRendementCepage()) {
    	    						$rd = $cepage_config->getRendementCepage();
    	    						if($appellation->getConfig()->hasManyLieu()) {
    	    							$this->rendement[$appellation->getLibelle()]['cepage'][$rd][$lieu->getLibelle()] = 1;
    	    						} else {
    	    							$this->rendement[$appellation->getLibelle()]['cepage'][$rd][$cepage_config->getLibelle()] = 1;
    	    						}
    	    					}
    	    				}
    					}
    				}
                }
			}
    	}
    	return $this->renderPartial('dr_recolte/popupRendementsMax', array('rendement'=> $this->rendement,
                                                                        'min_quantite'=> $this->min_quantite,
                                                                        'max_quantite'=> $this->max_quantite));
    }

    protected function processFormDetail($form, $request) {
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
            $form->getObject()->getCouchdbDocument()->utilisateurs->edition->add($this->getUser()->getCompte(CompteSecurityUser::NAMESPACE_COMPTE_AUTHENTICATED)->get('_id'), date('d/m/Y'));
            $detail = $form->save();
            if (!$this->produit->getConfig()->hasNoMotifNonRecolte() && $detail->exist('motif_non_recolte')) {
                $this->getUser()->setFlash('open_popup_ajout_motif', $detail->getKey());
            }


            $this->redirect('dr_recolte_produit', array('sf_subject' => $this->declaration, 'hash' => $this->produit->getHash()));
        }
    }

    protected function initDetails() {
        if(isset($this->produit)) {
            $this->details = $this->produit->add('detail');
            $this->nb_details_current = $this->details->count();
            foreach($this->produit->getLieu()->getConfig()->getCouleurs() as $couleur) {
                $this->declaration->getOrAdd(HashMapper::inverse($couleur->getHash()));
            }
        }

        $this->detail_key = null;
        $this->detail_action_mode = null;
        $this->form_detail = null;

        /*** AjOUT APPELLATION ***/
        $this->form_ajout_appellation = new RecolteAjoutAppellationForm($this->declaration->recolte);
        $this->form_ajout_lieu = null;
        $this->url_ajout_lieu = null;
        if (isset($this->produit) && $this->produit->getAppellation()->getConfig()->hasManyLieu()) {
            $this->form_ajout_lieu = new RecolteAjoutLieuForm($this->produit->getAppellation());
            $this->url_ajout_lieu = $this->generateUrl('dr_recolte_add_lieu', array('id' => $this->declaration->_id, 'force_appellation' => $this->produit->getMention()->getHash()));
        }

        $this->appellations = $this->declaration->getAppellationsAvecVtsgn();
    }

    protected function initAcheteurs() {
        $this->has_acheteurs_mout = ($this->produit->getAppellation()->getConfig()->mout == 1);
        $this->acheteurs = $this->declaration->get('acheteurs')->getNoeudAppellations()->get($this->produit->getAppellation()->getKey());
    }

    protected function initPrecDR(){
        $this->campagnes = acCouchdbManager::getClient('DR')->getArchivesSince($this->getUser()->getTiers('Recoltant')->cvi, ($this->getUser()->getCampagne()-1), 4);
    }

    protected function getFormDetailsOptions() {

        return array(
                'lieu_required' => $this->produit->getAppellation()->getConfig()->hasLieuEditable(),
                'superficie_required' => $this->produit->getConfig()->isSuperficieRequired(),
                'acheteurs_negoce' => $this->acheteurs->negoces,
                'acheteurs_cooperative' => $this->acheteurs->cooperatives,
                'acheteurs_mout' => $this->acheteurs->mouts,
                'has_acheteurs_mout' => $this->has_acheteurs_mout);
    }
}
