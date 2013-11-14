<?php
class VracValidationForm extends acCouchdbObjectForm 
{    
	public function configure()
    {
        $this->setWidgets(array(
        	'date_validation_mandataire' => new sfWidgetFormInputHidden(),
        	'date_validation_acheteur' => new sfWidgetFormInputHidden(),
        	'statut' => new sfWidgetFormInputHidden(),
        	'conditions_paiement' => new sfWidgetFormInputText(),
        	'conditions_particulieres' => new sfWidgetFormInputText()
    	));
        $this->widgetSchema->setLabels(array(
        	'conditions_paiement' => 'Conditions de paiement:',
        	'conditions_particulieres' => 'Conditions particulières:'
        ));
        $this->setValidators(array(
        	'date_validation_mandataire' => new sfValidatorPass(array('required' => true)),
        	'date_validation_acheteur' => new sfValidatorPass(array('required' => true)),
        	'statut' => new sfValidatorPass(array('required' => true)),
        	'conditions_paiement' => new sfValidatorString(array('required' => false)),
        	'conditions_particulieres' => new sfValidatorString(array('required' => false))
        ));
        $this->widgetSchema->setNameFormat('vrac_validation[%s]');
    }

    
	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        if ($this->getObject()->isAcheteurProprietaire()) {
        	$defaults['date_validation_acheteur'] = date('Y-m-d');
        } else {
        	$defaults['date_validation_mandataire'] = date('Y-m-d');
        }
        $defaults['statut'] = Vrac::STATUT_VALIDE_PARTIELLEMENT;
        $this->setDefaults($defaults); 
    }
    
    public function doUpdateObject($values) {
    	parent::doUpdateObject($values);
        if ($this->getObject()->isAcheteurProprietaire()) {
        	$this->getObject()->valide->date_validation_acheteur = date('Y-m-d');
        } else {
        	$this->getObject()->valide->date_validation_mandataire = date('Y-m-d');
        }
    	$this->getObject()->valide->statut = Vrac::STATUT_VALIDE_PARTIELLEMENT;
    }
}