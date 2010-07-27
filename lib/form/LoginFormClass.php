<?php
    class LoginForm extends BaseForm {
        public function setup() {
            
            $this->setWidgets(array(
                'cvi' => new sfWidgetFormInputText(),
            ));

            $this->setValidators(array(
                'cvi' => new sfValidatorString(array('required' => true)),
            ));

            $this->widgetSchema->setNameFormat('recoltant[%s]');
        }
    }

?>