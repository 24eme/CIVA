<?php
class VracSoussignesForm extends acCouchdbObjectForm 
{    
	protected $annuaire;
	
	public function __construct(acCouchdbJson $object, $annuaire = null, $options = array(), $CSRFSecret = null) 
	{
		$this->annuaire = $annuaire;
        parent::__construct($object, $options, $CSRFSecret);
    }
	public function configure()
    {
    	$types = $this->getTypes();
    	$recoltantChoices = $this->getRecoltants();
    	$negociantChoices = $this->getNegociants();
    	$caveCooperativeChoices = $this->getCavesCooperatives();
        $this->setWidgets(array(
        	'acheteur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'vendeur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'acheteur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => $recoltantChoices)),
        	'acheteur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => $negociantChoices)),
        	'acheteur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => $caveCooperativeChoices)),
        	'vendeur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => $recoltantChoices)),
        	'vendeur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => $negociantChoices)),
        	'vendeur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => $caveCooperativeChoices))
    	));
        $this->setValidators(array(
        	'acheteur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'vendeur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'acheteur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'acheteur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'acheteur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'vendeur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'vendeur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'vendeur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices)))
        ));
        $this->setDefault('acheteur_type', AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY);
        $this->setDefault('vendeur_type', AnnuaireClient::ANNUAIRE_RECOLTANT_KEY);
        //$this->validatorSchema->setPostValidator(new VracSoussignesValidator());
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');
    }
    
    protected function getTypes()
    {
    	return AnnuaireClient::getAnnuaireTypes();
    }
    
    public function getAnnuaire()
    {
    	return $this->annuaire;
    }
    
    public function getRecoltants()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	return array_merge(array('' => ''), $annuaire->recoltants->toArray());
    }
    
    public function getNegociants()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	return array_merge(array('' => ''), $annuaire->negociants->toArray());
    }
    
    public function getCavesCooperatives()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	return array_merge(array('' => ''), $annuaire->caves_cooperatives->toArray());
    }
}