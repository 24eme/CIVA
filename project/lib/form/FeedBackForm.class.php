<?php

class FeedBackForm extends sfForm {

    public function configure() {
	    $this->setWidgets(array(
            'message' => new sfWidgetFormTextarea(),
        ));

        $this->setValidators(array(
                'message' => new sfValidatorString(array('required' => true)),
        ));

        $this->widgetSchema->setNameFormat('message[%s]');
        $this->validatorSchema['message']->setMessage('required', 'Le message est obligatoire');
    }

}
