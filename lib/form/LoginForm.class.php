<?php
    class LoginForm extends BaseForm {
        public function setup() {
            
            $this->setWidgets(array(
				    'cvi' => new sfWidgetFormInputText(array('label' => 'CVI'))
            ));

            $this->setValidators(array(
                'cvi' => new sfValidatorString(array('required' => false)),
            ));
            
            $this->validatorSchema->setPostValidator(new ValidatorTiersLogin());
            
            $this->widgetSchema->setNameFormat('tiers[%s]');
        }
    }

?>