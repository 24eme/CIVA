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
    	$acheteurChoices = $this->getAcheteurs();
    	$vendeurChoices = $this->getVendeurs();
        $this->setWidgets(array(
        	'acheteur_identifiant' => new sfWidgetFormChoice(array('choices' => $acheteurChoices)),
        	'vendeur_identifiant' => new sfWidgetFormChoice(array('choices' => $vendeurChoices))
    	));
        $this->widgetSchema->setLabels(array(
        	'acheteur_identifiant' => 'CVI*:',
        	'vendeur_identifiant' => 'CVI*:'
        ));
        $this->setValidators(array(
        	'acheteur_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($acheteurChoices))),
        	'vendeur_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($vendeurChoices)))
        ));
        //$this->validatorSchema->setPostValidator(new VracSoussignesValidator());
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');
    }
    
    public function getAnnuaire()
    {
    	return $this->annuaire;
    }
    
    public function getAcheteurs()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	return array_merge(array('' => ''), $annuaire->acheteurs->toArray());
    }
    
    public function getVendeurs()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	return array_merge(array('' => ''), $annuaire->vendeurs->toArray());
    }
}