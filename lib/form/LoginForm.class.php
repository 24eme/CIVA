<?php
    class LoginForm extends BaseForm {
        public function setup() {
            
            $this->setWidgets(array(
                'cvi' => new sfWidgetFormInputText(),
            ));

            $this->setValidators(array(
                'cvi' => new sfValidatorString(array('required' => false)),
            ));
            
            $this->validatorSchema->setPostValidator(new ValidatorRecoltantLogin());
            
            $this->widgetSchema->setNameFormat('recoltant[%s]');
        }
    }

?>