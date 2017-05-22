<?php
class VracValidationForm extends acCouchdbObjectForm
{
	public function configure()
    {
        $this->setWidgets(array(
        	'conditions_paiement' => new sfWidgetFormInputText(),
        	'conditions_particulieres' => new sfWidgetFormInputText(),
    	));

        $this->widgetSchema->setLabels(array(
        	'conditions_paiement' => 'Conditions de paiement :',
        	'conditions_particulieres' => 'Conditions particulières :',
        ));

		$this->setValidators(array(
        	'conditions_paiement' => new sfValidatorString(array('required' => false)),
        	'conditions_particulieres' => new sfValidatorString(array('required' => false)),
        ));

		if($this->getObject()->exist('clause_reserve_propriete')) {
			$this->setWidget('clause_reserve_propriete', new sfWidgetFormInputCheckbox());
			$this->setValidator('clause_reserve_propriete', new sfValidatorBoolean(array('required' => false)));
			$this->getWidgetSchema()->setLabel('clause_reserve_propriete', "Clause de réserve de propriété :");
		}

        $this->widgetSchema->setNameFormat('vrac_validation[%s]');
    }

    public function doUpdateObject($values) {
    	parent::doUpdateObject($values);
        $this->getObject()->signerProrietaire();
    }
}
