<?php
    class FirstConnectionForm extends BaseForm {
        public function configure() {

            $this->setWidgets(array(
              'cvi'            => new sfWidgetFormInputText(),
              'mdp'   => new sfWidgetFormInputPassword()
            ));

            $this->widgetSchema->setNameFormat('firstConnection[%s]');

            $this->setValidators(array(
                'cvi' => new sfValidatorString(array('required' => false)),
                'mdp' => new sfValidatorString(array('required' => false)),
            ));
            
             $this->validatorSchema->setPostValidator(new ValidatorFirstConnection());
        }
    }

?>