<?php
class VracValidationForm extends acCouchdbObjectForm 
{    
	public function configure()
    {
        $this->setWidgets(array(
        	'date_validation_mandataire' => new sfWidgetFormInputHidden(),
        	'statut' => new sfWidgetFormInputHidden(),
    	));
        $this->setValidators(array(
        	'date_validation_mandataire' => new sfValidatorPass(array('required' => true)),
        	'statut' => new sfValidatorPass(array('required' => true)),
        ));
        $this->widgetSchema->setNameFormat('vrac_validation[%s]');
    }

    
	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        $defaults['date_validation_mandataire'] = date('Y-m-d');
        $defaults['statut'] = Vrac::STATUT_VALIDE_PARTIELLEMENT;
        $this->setDefaults($defaults); 
    }
}