<?php
class annuaireActions extends sfActions {

	public function executeIndex(sfWebRequest $request)
	{
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
		echo $this->annuaire."\n";
    }

	public function executeSelectionner(sfWebRequest $request)
	{
		$this->type = $request->getParameter('type');
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
		$this->form = new AnnuaireAjoutForm($this->annuaire);
		$this->form->setDefault('type', $this->type);
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$values = $this->form->getValues();
        		$tiers = $this->form->getTiers();
        		return $this->redirect('annuaire_ajouter', array('type' => $values['type'], 'identifiant' => $values['identifiant']));
        	}
        }
    }

	public function executeAjouter(sfWebRequest $request)
	{
		$this->type = $request->getParameter('type');
		$this->identifiant = $request->getParameter('identifiant');
		if ($this->type && $this->identifiant) {
			$this->compte = $this->getUser()->getCompte();
			$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
			$this->form = new AnnuaireAjoutForm($this->annuaire);
			$this->form->setDefault('type', $this->type);
			$this->form->setDefault('identifiant', $this->identifiant);
			$this->tiers = AnnuaireClient::getInstance()->findTiersByTypeAndIdentifiant($this->type, $this->identifiant);
		}
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$tiers = $this->form->getTiers();
        		$values = $this->form->getValues();
        		if ($this->tiers->_id == $tiers->_id) {
       				$this->form->save();
       				if ($vrac = $this->getUser()->getAttribute('vrac_object')) {
       					$vrac = unserialize($vrac);
       					$acteur = $this->getUser()->getAttribute('vrac_acteur');
       					$vrac->addActeur($acteur, $this->tiers);
       					$vrac->addType($acteur, $values['type']);
       					$this->getUser()->setAttribute('vrac_object', serialize($vrac));
       					$this->getUser()->setAttribute('vrac_acteur', null);
       					$etapes = VracEtapes::getInstance();
       					return $this->redirect('vrac_etape', array('numero_contrat' => !$vrac->isNew() ? $vrac->numero_contrat : VracRoute::NOUVEAU, 'etape' => $etapes->getFirst()));
       				}
       				return $this->redirect('@annuaire');
        		}
        		return $this->redirect('annuaire_ajouter', array('type' => $values['type'], 'identifiant' => $values['identifiant']));
        	}
        }
    }

	public function executeAjouterCommercial(sfWebRequest $request)
	{
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
		$this->form = new AnnuaireAjoutCommercialForm($this->annuaire);
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$values = $this->form->getValues();
       			$this->form->save();
       			if ($vrac = $this->getUser()->getAttribute('vrac_object')) {
       				$vrac = unserialize($vrac);
              $vrac->storeInterlocuteurCommercialInformations($values['identite'], $value['contact']);
       				$this->getUser()->setAttribute('vrac_object', serialize($vrac));
       				$etapes = VracEtapes::getInstance();
       				return $this->redirect('vrac_etape', array('numero_contrat' => !$vrac->isNew() ? $vrac->numero_contrat : VracRoute::NOUVEAU, 'etape' => $etapes->getFirst()));
       			}
       			return $this->redirect('@annuaire');
        	}
        }
    }

    public function executeRetour(sfWebRequest $request)
    {
    	if ($vrac = $this->getUser()->getAttribute('vrac_object')) {
       		$vracIdentifiant = ($vrac->_id)? $vrac->_id : VracRoute::NOUVEAU;
       		$etapes = VracEtapes::getInstance();
    		return $this->redirect('vrac_etape', array('numero_contrat' => $vracIdentifiant, 'etape' => $etapes->getFirst()));
    	}
    	return $this->redirect('@annuaire');
    }

	public function executeSupprimer(sfWebRequest $request)
	{
		$type = $request->getParameter('type');
		$id = $request->getParameter('id');
		if ($type !== null && $id !== null) {
			$compte = $this->getUser()->getCompte();
			$annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($compte->login);
			if ($annuaire && $annuaire->exist($type)) {
				if ($annuaire->get($type)->exist($id)) {
					$annuaire->get($type)->remove($id);
					$annuaire->save();
					return $this->redirect('@annuaire');
				}
			}
		}
		throw new sfError404Exception('La paire "'.$type.'"/"'.$id.'" n\'existe pas dans l\'annuaire');
    }
}
