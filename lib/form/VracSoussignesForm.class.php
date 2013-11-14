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
    	$commerciauxChoices = $this->getCommerciaux();
        $this->setWidgets(array(
        	'acheteur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'vendeur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'acheteur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'interlocuteur_commercial' => new sfWidgetFormChoice(array('choices' => array_merge($commerciauxChoices, array('add' => 'Ajouter un contact'))))
    	));
        $this->setValidators(array(
        	'acheteur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'vendeur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'acheteur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'acheteur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'acheteur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'vendeur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'vendeur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'vendeur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'interlocuteur_commercial' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($commerciauxChoices)))
        ));
        $this->validatorSchema->setPostValidator(new VracSoussignesValidator($this->getObject()));
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');
    }

    
	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        if ($this->getObject()->acheteur_identifiant) {
        	$defaults['acheteur_'.str_replace('s', '', $this->getObject()->acheteur_type).'_identifiant'] = $this->getObject()->acheteur_identifiant;
        }
        if ($this->getObject()->vendeur_identifiant) {
        	$defaults['vendeur_'.str_replace('s', '', $this->getObject()->vendeur_type).'_identifiant'] = $this->getObject()->vendeur_identifiant;
        }
        $this->setDefaults($defaults); 
    }

    protected function doUpdateObject($values) 
    {
    	$acheteur = $values['acheteur'];
    	$vendeur = $values['vendeur'];
    	if ($values['interlocuteur_commercial']) {
    		$commercialEmail = $this->annuaire->commerciaux->get($values['interlocuteur_commercial']);
    		$this->getObject()->interlocuteur_commercial->nom = $values['interlocuteur_commercial'];
    		$this->getObject()->interlocuteur_commercial->email = $commercialEmail;
    	}
    	$this->getObject()->acheteur_type = $values['acheteur_type'];
    	$this->getObject()->vendeur_type = $values['vendeur_type'];
    	$this->getObject()->acheteur_identifiant = $acheteur->_id;
    	$this->getObject()->vendeur_identifiant = $vendeur->_id;
    	$this->getObject()->storeAcheteurInformations($acheteur);
    	$this->getObject()->storeVendeurInformations($vendeur);
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
    
    public function getCommerciaux()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$commerciaux = $annuaire->commerciaux->toArray();
    	$choices = array();
    	foreach ($commerciaux as $key => $commercial) {
    		$choices[$key] = $key;
    	}
    	return array_merge(array('' => ''), $choices);
    }
}