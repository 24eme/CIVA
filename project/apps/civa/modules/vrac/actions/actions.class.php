<?php
class vracActions extends sfActions 
{    
    public function executeNouveau(sfWebRequest $request) 
    {
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
    	$etapes = VracEtapes::getInstance();
    	return $this->redirect('vrac_etape', array('sf_subject' => new Vrac(), 'etape' => $etapes->getFirst()));
    }
	
	public function executeHistorique(sfWebRequest $request)
	{
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
		$this->campagne = $request->getParameter('campagne');
		if (!$this->campagne) {
			throw new sfError404Exception('La campagne doit être spécifiée.');
		}
		$this->statut = $request->getParameter('statut');
		if (!$this->campagne) {
			$this->campagne = ConfigurationClient::getInstance()->buildCampagne(date('Y-m-d'));
		}
		$this->user = $this->getUser()->getDeclarant();
        $this->vracs = VracTousView::getInstance()->findSortedBy($this->user->_id, $this->campagne, $this->statut);
        $this->campagnes = $this->getCampagnes(VracTousView::getInstance()->findBy($this->user->_id), ConfigurationClient::getInstance()->buildCampagne(date('Y-m-d')));
        $this->statuts = $this->getStatuts();
	}
	
	protected function getCampagnes($vracs, $courante)
	{
		$campagnes = array($courante);
		foreach ($vracs as $vrac) {
			if (!in_array($vrac->key[1], $campagnes)) {
				$campagnes[] = $vrac->key[1];
			}
		}
		rsort($campagnes);
		return $campagnes;
	}
	
	protected function getStatuts()
	{
		$statuts = Vrac::getStatutsLibelles();
		$statuts[Vrac::STATUT_VALIDE_PARTIELLEMENT] = $statuts[Vrac::STATUT_VALIDE_PARTIELLEMENT].'/signature';
		return $statuts;
	}
	
	public function executeAnnuaire(sfWebRequest $request)
	{
		$this->type = $request->getParameter('type');
		$this->acteur = $request->getParameter('acteur');
		$types = array_keys(AnnuaireClient::getAnnuaireTypes());
		$acteurs = Vrac::getTypesTiers();
		if (!in_array($this->type, $types)) {
			throw new sfError404Exception('Le type "'.$this->type.'" n\'est pas pris en charge.');
		}
		if (!in_array($this->acteur, $acteurs)) {
			throw new sfError404Exception('L\'acteur "'.$this->acteur.'" n\'est pas pris en charge.');
		}

		$this->vrac = $this->getRoute()->getVrac();	

		$this->annuaire = $this->getAnnuaire();
		$this->form = new VracSoussignesAnnuaireForm($this->vrac, $this->annuaire);
		if ($request->isMethod(sfWebRequest::POST)) {
			$parameters = $request->getParameter($this->form->getName());
			unset($parameters['_csrf_token']);
    		$this->form->bind($parameters);
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->getUpdatedVrac();
        	} else {
        		throw new sfException($this->form->renderGlobalErrors());
        	}
		}
		$this->getUser()->setAttribute('vrac_object', serialize($this->vrac));
		$this->getUser()->setAttribute('vrac_acteur', $this->acteur);
		return $this->redirect('annuaire_selectionner', array('type' => $this->type));
	}
	
	public function executeAnnuaireCommercial(sfWebRequest $request)
	{
		$this->vrac = $this->getRoute()->getVrac();	
		$this->annuaire = $this->getAnnuaire();
		$this->form = new VracSoussignesAnnuaireForm($this->vrac, $this->annuaire);
		if ($request->isMethod(sfWebRequest::POST)) {
			$parameters = $request->getParameter($this->form->getName());
			unset($parameters['_csrf_token']);
    		$this->form->bind($parameters);
        	if ($this->form->isValid()) {
        		$this->vrac = $this->form->getUpdatedVrac();
        	} else {
        		throw new sfException($this->form->renderGlobalErrors());
        	}
		}
		$this->getUser()->setAttribute('vrac_object', serialize($this->vrac));
		return $this->redirect('annuaire_commercial_ajouter');
	}
	
	public function executeCloture(sfWebRequest $request)
	{
		$this->vrac = $this->getRoute()->getVrac();
		$this->validation = new VracValidation($this->vrac);
		if ($this->vrac->allProduitsClotures() && !$this->validation->hasErreurs()) {
			$this->vrac->clotureContrat();
			$this->vrac->save();
			return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
		}
		throw new sfError404Exception('Contrat vrac '.$this->vrac->_id.' n\'est pas cloturable.');
	}
	
	public function executeSupprimer(sfWebRequest $request) 
	{
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
		$this->vrac = $this->getRoute()->getVrac();
		if (!$this->vrac) {
			return $this->redirect('mon_espace_civa');
		}
		$this->user = $this->getUser()->getDeclarant();
		if ($this->vrac->isSupprimable($this->user->_id)) {
			if ($this->vrac->valide->statut == Vrac::STATUT_CREE) {
				$this->vrac->delete();
				return $this->redirect('mon_espace_civa');
			}
			$this->form = new VracSuppressionForm($this->vrac);
			if ($request->isMethod(sfWebRequest::POST)) {
	    		$this->form->bind($request->getParameter($this->form->getName()));
	        	if ($this->form->isValid()) {
	        		$this->vrac = $this->form->save();
		        	$acteurs = $this->vrac->getActeurs();
					foreach ($acteurs as $type => $acteur) {
                        foreach($acteur->emails as $email) {
                            VracMailer::getInstance()->annulationContrat($this->vrac, $email);
                        }
					}
					return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
	        	}
	        }
		} else {
			throw new sfError404Exception('Contrat '.$this->vrac->_id.' non supprimable.');
		}
    }
    
	public function executeFiche(sfWebRequest $request) 
	{
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
		$this->vrac = $this->getRoute()->getVrac();
		$this->user = $this->getUser()->getDeclarant();
		$this->form = $this->getFormRetiraisons($this->vrac, $this->user);
		$this->validation = new VracValidation($this->vrac);
    	if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$vrac = $this->form->save();
				$this->getUser()->setFlash('notice', 'Le contrat a été mis à jour avec succès.');
       			return $this->redirect('vrac_fiche', array('sf_subject' => $vrac));
        	}
        }
    }
    
	public function executeValidation(sfWebRequest $request) 
	{
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
		$this->vrac = $this->getRoute()->getVrac();
		$this->user = $this->getUser()->getDeclarant();
		$this->vrac->valideUser($this->user->_id);
		$this->vrac->updateValideStatut();
		$this->vrac->save();
		
		$this->getUser()->setFlash('notice', 'Votre signature a bien été prise en compte.');
		VracMailer::getInstance()->confirmationSignature($this->vrac, $this->getUser()->getCompte()->email);
		return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
    }
    
    public function executeEtape(sfWebRequest $request) 
    {
		$this->user = $this->getUser()->getDeclarant();
    	$this->etapes = VracEtapes::getInstance();
    	$this->etape = $request->getParameter('etape');
    	$this->referer = ($this->getUser()->getFlash('referer'))? 1 : 0;
    	$this->forward404Unless($this->etapes->exist($this->etape), 'L\'étape "'.$this->etape.'" n\'est pas prise en charge.');
		$this->vrac = $this->getRoute()->getVrac();
        
    	if ($this->etapes->isGt($this->etape, VracEtapes::ETAPE_PRODUITS) && !$this->vrac->hasProduits()) {
    		return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => VracEtapes::ETAPE_PRODUITS));
    	}
		$this->annuaire = $this->getAnnuaire();
    	$this->form = $this->getForm($this->vrac, $this->etape, $this->annuaire);
    	if ($nextEtape = $this->getEtapeSuivante($this->etape, $this->etapes)) {
    		$this->vrac->etape = $nextEtape;
    	}
    	if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			if ($request->isXmlHttpRequest()) {
       				return sfView::NONE;
       			}
    			$this->getUser()->setAttribute('vrac_object', null);
    			$this->getUser()->setAttribute('vrac_acteur', null);
       			if ($nextEtape) {
       				return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => $this->vrac->etape));
       			} else {
					VracMailer::getInstance()->confirmationSignature($this->vrac, $this->getUser()->getCompte()->email);
					$acteurs = $this->vrac->getActeurs(false);
					foreach ($acteurs as $type => $acteur) {
                        foreach($acteur->emails as $email) {
						  VracMailer::getInstance()->demandeSignature($this->vrac, $email);
                        }
					}
					$this->getUser()->setFlash('notice', 'Le contrat a été créé avec succès et votre signature a bien été prise en compte. Chacun des acteurs du contrat va recevoir un email de demande de signature.');
       				return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
       			}
        	}
        }
    }
    
    public function executeAjouterProduit(sfWebRequest $request) 
    {
    	$this->user = $this->getUser()->getDeclarant();
    	$this->config = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
    	$this->appellationsLieuDit = json_encode($this->config->getAppellationsLieuDit());
    	$this->vrac = $this->getRoute()->getVrac();
    	$this->etapes = VracEtapes::getInstance();
    	$this->etape = $request->getParameter('etape');
    	$this->forward404Unless($this->etapes->exist($this->etape), 'L\'étape "'.$this->etape.'" n\'est pas prise en charge.');
    	$this->form = new VracProduitAjoutForm($this->vrac);
    	if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			$this->getUser()->setFlash('referer', 'ajout-produit');
       			return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => $this->etape));
        	}
        }
    }
    
    public function executeSoussigneInformations(sfWebRequest $request)
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$identifiant = $request->getParameter('identifiant', null);
    	if (!$identifiant) {
    		throw new sfException('Id du tiers obligatoire.');
    	}
    	$tiers = _TiersClient::getInstance()->find($identifiant);
    	if (!$tiers) {
    		throw new sfException('Le tiers d\'id "'.$identifiant.'" n\'existe pas.');
    	}

        $acteur = $request->getParameter('acteur');

        $this->vrac = $this->getRoute()->getVrac();

        $this->vrac->addActeur($acteur, $tiers);

    	return $this->renderPartial('vrac/soussigne', array('vrac' => $this->vrac, 'tiers' => $this->vrac->{$acteur}, 'fiche' => false));	
    }
    
    public function executeAjouterProduitLieux(sfWebRequest $request) 
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$appellation = $request->getParameter('appellation', null);
    	if (!$appellation) {
    		throw new sfException('Appellation obligatoire.');
    	}
    	$this->config = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
    	if (!$this->config->recolte->certification->genre->exist($appellation)) {
    		throw new sfException('Appellation "'.$appellation.'" n\'existe pas.');
    	}
    	$result = array();
    	if ($this->config->recolte->certification->genre->get($appellation)->hasManyLieu()) {
			foreach ($this->config->recolte->certification->genre->get($appellation)->getLieux() as $key => $lieu) {
				$result[$key] = $lieu->libelle;
			}
    	}
    	return $this->renderText(json_encode($result));
    }
    
    public function executeAjouterProduitCepages(sfWebRequest $request) 
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$appellation = $request->getParameter('appellation', null);
    	$lieu = $request->getParameter('lieu', 'lieu');
    	if (!$lieu) {
    		$lieu = 'lieu';
    	}
    	if (!$appellation) {
    		throw new sfException('Appellation obligatoire.');
    	}
    	$this->config = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
    	if (!$this->config->recolte->certification->genre->exist($appellation)) {
    		throw new sfException('Appellation "'.$appellation.'" n\'existe pas.');
    	}
    	if (!$this->config->recolte->certification->genre->get($appellation)->mention->exist($lieu)) {
    		throw new sfException('Lieu "'.$lieu.'" n\'existe pas.');
    	}
    	$result = array();
		foreach ($this->config->recolte->certification->genre->get($appellation)->mention->get($lieu)->getCepages() as $key => $cepage) {
			$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = $cepage->libelle;
			if ($key == Vrac::CEPAGE_EDEL) {
				$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = $result[str_replace('/recolte/', 'declaration/', $cepage->getHash())].Vrac::CEPAGE_EDEL_LIBELLE_COMPLEMENT;
			}
			if ($key == Vrac::CEPAGE_MUSCAT) {
				$result[str_replace('/recolte/', 'declaration/', $cepage->getHash())] = Vrac::CEPAGE_MUSCAT_LIBELLE;
			}
		}
    	return $this->renderText(json_encode($result));
    }
    
    public function executeAjouterProduitVtsgn(sfWebRequest $request) 
    {
    	if (!$request->isXmlHttpRequest()) {
    		throw new sfException('Requête ajax obligatoire.');
    	}
    	$hash = $request->getParameter('hash', null);
    	if (!$hash) {
    		throw new sfException('Hash cépage obligatoire.');
    	}
    	$hash = str_replace('declaration/', 'recolte/', $hash);
    	$this->config = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
    	if (!$this->config->exist($hash)) {
    		throw new sfException('Cépage "'.$hash.'" n\'existe pas.');
    	}
    	$cepage = $this->config->get($hash);
    	$vtsgn = ($cepage->exist('no_vtsgn') && $cepage->no_vtsgn)? 0 : 1;
    	return $this->renderText($vtsgn);
    }

	protected function getForm(Vrac $vrac, $etape, $annuaire = null)
	{
		return VracFormFactory::create($vrac, $etape, $annuaire);
	}
    
    protected function getEtapeSuivante($etape, $etapes)
    {
    	$next = null;
    	$nextEtape = $etapes->getNext($etape);
    	if ($nextEtape && $etapes->isLt($etape, $nextEtape)) {
    		$next = $nextEtape;
    	}
    	return $next;
    }
    
    protected function getAnnuaire()
    {
    	$compte = $this->getUser()->getCompte();
		return AnnuaireClient::getInstance()->findOrCreateAnnuaire($compte->login);
    }    
    
    protected function getFormRetiraisons($vrac, $user)
    {
    	if ($vrac->isValide() && !$vrac->isCloture() && $vrac->isProprietaire($user->_id) && !$vrac->isAnnule()) {
    		return new VracProduitsEnlevementsForm($vrac);
    	}
    	return null;
    }

}
