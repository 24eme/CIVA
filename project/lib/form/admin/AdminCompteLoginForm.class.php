<?php

class AdminCompteLoginForm extends BaseForm {

    /**
     *
     */
    public function configure() {
        $this->setWidgets(array(
                'login'   => new sfWidgetFormInputText(),
        ));

        if($this->getOption('autocomplete')) {
            $this->setWidget('login', new WidgetCompteSelect(array('interpro_id' => 'INTERPRO-declaration')));
        }

        $this->widgetSchema->setLabels(array(
                'login'  => 'Login : ',
        ));

        $this->setValidators(array(
                'login' => new sfValidatorString(array('required' => true)),
        ));
        
        $this->widgetSchema->setNameFormat('admin[%s]');

        $this->validatorSchema['login']->setMessage('required', 'Champ obligatoire');
        $this->validatorSchema->setPostValidator(new ValidatorAdminCompteLogin(array('comptes_type' => $this->getOption('comptes_type', array()))));
    }

    /**
     * 
     * @return _Tiers;
     */
    public function process() {
        if ($this->isValid()) {
            return $this->getValue('compte');
        } else {
            throw new sfException("must be valid");
        }
    }

}

