<?php
class vracActions extends sfActions 
{    
	public function executeAnnuaire(sfWebRequest $request)
	{
		$this->vrac = $this->getRoute()->getVrac();
		$this->type = $request->getParameter('type');
		$types = array_keys(AnnuaireClient::getAnnuaireTypes());
		if (!in_array($this->type, $types)) {
			throw new sfError404Exception('Le type "'.$this->type.'" n\'est pas pris en charge.');
		}
		$vracIdentifiant = ($this->vrac->numero_contrat)? $this->vrac->numero_contrat : VracRoute::NOUVEAU;
		$this->getUser()->setAttribute('annuaire_vrac_id', $vracIdentifiant);
		return $this->redirect('annuaire_selectionner', array('type' => $this->type));
	}
	
	public function executeSupprimer(sfWebRequest $request) 
	{
		$this->vrac = $this->getRoute()->getVrac();
		$tiers = $this->getUser()->getDeclarant();
		if ($this->vrac->isSupprimable($tiers->_id)) {
			$this->vrac->delete();
		}
		return $this->redirect('mon_espace_civa');
    }
    
	public function executeFiche(sfWebRequest $request) 
	{
		$this->vrac = $this->getRoute()->getVrac();
		$this->user = $this->getUser()->getDeclarant();
		$this->form = $this->getFormRetiraisons($this->vrac);
		if ($request->getParameter('validation')) {
			$this->vrac->valideUser($this->user);
			$this->vrac->updateValideStatut();
			$this->vrac->save();
			return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
		}
    	if ($request->isMethod(sfWebRequest::POST)) {
    		$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
        	}
        }
    }
    
    public function executeEtape(sfWebRequest $request) 
    {
		$this->user = $this->getUser()->getDeclarant();
    	$this->etapes = VracEtapes::getInstance();
    	$this->etape = $request->getParameter('etape');
    	$this->forward404Unless($this->etapes->exist($this->etape), 'L\'étape "'.$this->etape.'" n\'est pas prise en charge.');
    	$this->vrac = $this->getRoute()->getVrac();
    	if (!$this->vrac) {
    		$this->vrac = $this->getNouveauVrac($this->user);
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
       			if ($nextEtape) {
       				return $this->redirect('vrac_etape', array('sf_subject' => $this->vrac, 'etape' => $this->vrac->etape));
       			} else {
       				return $this->redirect('vrac_fiche', array('sf_subject' => $this->vrac));
       			}
        	}
        }
    }
    
    public function executeAjouterProduit(sfWebRequest $request) 
    {
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
    	return $this->renderPartial('vrac/soussigne', array('tiers' => $tiers));	
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
    
    protected function getFormRetiraisons($vrac)
    {
    	if ($vrac->isValide() && !$vrac->isCloture()) {
    		return new VracProduitsEnlevementsForm($vrac);
    	}
    	return null;
    }
    
    protected function getNouveauVrac($tiers)
    {
		$tiers = $this->getUser()->getDeclarant();
		$vrac = VracClient::getInstance()->createVrac($tiers->_id);
		$vrac->mandataire_identifiant = $tiers->_id;
		$vrac->storeMandataireInformations($tiers);
		return $vrac;
    }
}
