<?php

class RecolteAcheteurForm extends BaseForm {

    public function configure() {
        $this->setWidgets(array(
            'quantite_vendue' => new sfWidgetFormInputFloat(),
        ));

        $this->setValidators(array(
            'quantite_vendue' => new sfValidatorNumber(array('required' => false)),
        ));
        $this->widgetSchema->setNameFormat('acheteur[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    }

}