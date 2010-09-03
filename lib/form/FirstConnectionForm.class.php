<?php
class FirstConnectionForm extends BaseForm {
    public function configure() {

        $this->setWidgets(array(
                'cvi'   => new sfWidgetFormInputText(),
                'mdp'   => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'cvi'  => 'Numéro CVI : ',
                'mdp'  => 'Code de création : '
        ));

        $this->widgetSchema->setNameFormat('firstConnection[%s]');

        $this->setValidators(array(
                'cvi' => new sfValidatorString(array('required' => true)),
                'mdp' => new sfValidatorString(array('required' => true)),
        ));

        $this->validatorSchema['cvi']->setMessage('required', 'Champ obligatoire');
        $this->validatorSchema['mdp']->setMessage('required', 'Champ obligatoire');


        $this->validatorSchema->setPostValidator(new ValidatorFirstConnection());
    }
}

?>