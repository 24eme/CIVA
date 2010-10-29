<?php
    class LoginForm extends BaseForm {
        public function setup() {
            $this->setWidgets(array(
				    'cvi' => new sfWidgetFormInputText(array('label' => 'CVI'))
            ));

            $this->setValidators(array(
                'cvi' => new sfValidatorString(array('required' => false)),
            ));

            $this->validatorSchema->setPostValidator(new ValidatorTiersLogin(array('create_required' => $this->getOption('need_create', true))));

            $this->widgetSchema->setNameFormat('tiers[%s]');
        }
    }