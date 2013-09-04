<?php
class annuaireActions extends sfActions {

	public function executeIndex(sfWebRequest $request) 
	{
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
    }

	public function executeAjouter(sfWebRequest $request) 
	{
		$this->tiers = null;
		if ($id = $request->getParameter('id')) {
			$this->tiers = _TiersClient::getInstance()->find($id);
			if (!$this->tiers) {
				throw new sfError404Exception('Le tiers d\id "'.$id.'" n\'existe pas');
			}
		}
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
		$this->form = new AnnuaireAjoutForm($this->annuaire, $this->tiers);
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
        		$tiers = $this->form->getTiers();
        		if ($this->tiers && $this->tiers->_id == $tiers->_id) {
       				$this->form->save();
       				return $this->redirect('@annuaire');
        		}
        		return $this->redirect('annuaire_ajouter', array('id' => $tiers->_id));
        	}
        }
    }

	public function executeSupprimer(sfWebRequest $request) 
	{
		$type = $request->getParameter('type');
		$id = $request->getParameter('id');
		if ($type && $id) {
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
