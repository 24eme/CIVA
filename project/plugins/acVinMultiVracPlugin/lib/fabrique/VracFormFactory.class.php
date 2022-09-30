<?php
class VracFormFactory 
{
	public static function create(Vrac $vrac, $etape, $annuaire = null) 
	{
		$form = null;
		switch ($etape){
			case VracEtapes::ETAPE_SOUSSIGNES :
				$form = new VracSoussignesForm($vrac, $annuaire);
				break;
			case VracEtapes::ETAPE_PRODUITS :
				$form = new VracProduitsForm($vrac);
				break;
			case VracEtapes::ETAPE_CONDITIONS :
				$form = new VracConditionsForm($vrac);
				break;
			case VracEtapes::ETAPE_VALIDATION :
				$form = new VracValidationForm($vrac, $annuaire);
				break;
			default:
				throw new sfException ('La fabrique de formulaire vrac ne gère pas le cas "'.$step.'".');
		}
		return $form;
	}
}
