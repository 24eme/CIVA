<?php

class AdminBackToTheFutureForm extends BaseForm {

    public function configure() {
        $this->setWidgets(array(
                'campagne'   => new sfWidgetFormChoice(array('choices' => array('', '2013' => '2013', '2012' => '2012', '2011' => '2011', '2010' => '2010'))),
        ));

        $this->widgetSchema->setLabels(array(
                'campagne'  => 'Campagne : ',
        ));

        $this->setValidators(array(
                'campagne' => new sfValidatorChoice(array('required' => true, 'choices' => array('2013', '2012', '2011', '2010'))),
        ));
        
        $this->widgetSchema->setNameFormat('admin_back_to_the_future[%s]');

        $this->validatorSchema['campagne']->setMessage('required', 'Champ obligatoire');
    }

}

