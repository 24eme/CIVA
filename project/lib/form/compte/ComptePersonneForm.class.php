<?php
class ComptePersonneForm extends acCouchdbObjectForm {
    
    public function configure() {
        $this->setWidgets(array(
                'nom' => new sfWidgetFormInput(),
                'email' => new sfWidgetFormInput(),
        ));

        $this->setValidators(array(
                'nom'  => new sfValidatorString(array('required' => true)),
                'email'  => new sfValidatorEmailStrict(array('required' => true)),
        ));
        $this->widgetSchema->setNameFormat('compte_personne[%s]');
    }
    
}
