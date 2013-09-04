<?php
class annuaireActions extends sfActions {

	public function executeIndex(sfWebRequest $request) 
	{
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
    }

	public function executeAjouter(sfWebRequest $request) 
	{
		$this->compte = $this->getUser()->getCompte();
		$this->annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($this->compte->login);
		$this->form = new AnnuaireAjoutForm($this->annuaire);
        if ($request->isMethod(sfWebRequest::POST)) {
        	$this->form->bind($request->getParameter($this->form->getName()));
        	if ($this->form->isValid()) {
       			$this->form->save();
       			return $this->redirect('@annuaire');
        	}
        }
    }
}
