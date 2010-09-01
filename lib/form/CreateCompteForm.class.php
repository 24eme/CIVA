<?php
class CreateCompteForm extends BaseForm {
    public function configure() {

        $this->setWidgets(array(
                'email' => new sfWidgetFormInputText(),
                'mdp1'  => new sfWidgetFormInputPassword(),
                'mdp2'  => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'email' => 'Adresse e-mail: ',
                'mdp1'  => 'Mot de passe: ',
                'mdp2'  => 'Vérification du mot de passe: '
        ));

        $this->widgetSchema->setNameFormat('create_compte[%s]');

        $verif_mdp = $this->getOption('verif_mdp', true);
        $this->setValidators(array(
                'email' => new sfValidatorEmail(array('required' => true),array('required' => 'Champ obligatoire', 'invalid' => 'Adresse email invalide.')),
                'mdp1'  => new sfValidatorString(array('required' => $verif_mdp), array('required' => 'Champ obligatoire.')),
                'mdp2'  => new sfValidatorString(array('required' => $verif_mdp), array('required' => 'Champ obligatoire'))
        ));

        $this->validatorSchema->setPostValidator(new ValidatorCreateCompte());
    }
}

?>