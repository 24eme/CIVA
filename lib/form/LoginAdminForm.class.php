<?php
    class LoginAdminForm extends BaseForm {
        public function setup() {
            $need_login = $this->getOption('need_login', true);
            $this->setWidget('cvi', new sfWidgetFormInputText(array('label' => 'CVI')));
            $this->setValidator('cvi', new sfValidatorString(array('required' => false)));

            if ($need_login) {
                $this->setWidget('username', new sfWidgetFormInputText(array('label' => 'Identifiant :')));
                $this->setWidget('password', new sfWidgetFormInputPassword(array('label' => 'Mot de passe :', 'always_render_empty' => false)));
                $this->setWidget('cvi', new sfWidgetFormInputText(array('label' => 'CVI de la DR à éditer:')));
                $this->setValidator('username', new sfValidatorString(array('required' => false)));
                $this->setValidator('password', new sfValidatorString(array('required' => false)));
            }

            $this->validatorSchema->setPostValidator(new ValidatorTiersLoginAdmin(array('need_login' => $need_login)));
            
            $this->widgetSchema->setNameFormat('login-admin[%s]');
        }
    }

?>