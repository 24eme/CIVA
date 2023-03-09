<?php

class vracComponents extends sfComponents {

	public function executeMonEspace(sfWebRequest $request)
	{
		$this->getUser()->setAttribute('vrac_object', null);
    	$this->getUser()->setAttribute('vrac_acteur', null);
    	$this->getUser()->setAttribute('vrac_type_tiers', null);
    	$this->getUser()->setAttribute('vrac_createur', null);
    	$this->getUser()->setAttribute('vrac_papier', null);
		$this->tiers = $this->getUser()->getDeclarantsVrac();
        $this->hasDoubt = true;
        $etablissements = VracClient::getInstance()->getEtablissements($this->getUser()->getCompte()->getSociete());

        foreach($etablissements as $etablissement) {
            if($etablissement->getFamille() != EtablissementFamilles::FAMILLE_COURTIER) {
                $this->hasDoubt = false;
            }
            if(count($etablissements) == 1 && !in_array($etablissement->getFamille(), array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))) {
                $this->hasDoubt = false;
            }
        }
        $this->vracs = VracTousView::getInstance()->findSortedByDeclarantsAndCleanPluriannuel($etablissements);
        $this->etapes = VracEtapes::getInstance();
    }

}
