<?php
class CreateCompteForm extends BaseForm {
    public function configure() {

        $this->setWidgets(array(
                'email'            => new sfWidgetFormInputText(),
                'mot_de_passe1'   => new sfWidgetFormInputPassword(),
                'mot_de_passe2'   => new sfWidgetFormInputPassword()
        ));

        $this->widgetSchema->setLabels(array(
                'email'         => 'Adresse e-mail',
                'mot_de_passe1' => 'Mot de passe',
                'mot_de_passe2' => 'Vérification du mot de passe'
        ));
    }
}

?>