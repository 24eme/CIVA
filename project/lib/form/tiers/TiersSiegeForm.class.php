<?php

class TiersSiegeForm extends acCouchdbObjectForm {

    public function configure() {
        $this->setWidgets(array(
				'code_postal' => new sfWidgetFormInputText(array('label' => 'Code Postal')),
				'adresse' => new sfWidgetFormTextarea(array('label' => 'Adresse')),
				'commune' => new sfWidgetFormInputText(array('label' => 'Commune')),
				));

        $this->setValidators(array(
				  'code_postal' => new sfValidatorRegex(array('required' => true, 'pattern' => '/^[0-9]+$/'), array('invalid' => 'Ne doit contenir que des chiffres', 'required' => 'Champ Requis')),
				   'adresse' => new sfValidatorString(array('required' => true), array('required' => 'Champ Requis')),
				   'commune' => new sfValidatorString(array('required' => true), array( 'required' => 'Champ Requis')),
				   ));
        //$this->validatorSchema->setPostValidator();

        $this->widgetSchema->setNameFormat('siege[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

}
