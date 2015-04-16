<?php

class AdminBackToTheFutureForm extends BaseForm {

    public function configure() {
        $campagnes = $this->getCampagnes();

        $this->setWidgets(array(
                'campagne'   => new sfWidgetFormChoice(array('choices' => array("" => "") + $campagnes)),
        ));

        $this->widgetSchema->setLabels(array(
                'campagne'  => 'Campagne : ',
        ));

        $this->setValidators(array(
                'campagne' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($campagnes))),
        ));
        
        $this->widgetSchema->setNameFormat('admin_back_to_the_future[%s]');

        $this->validatorSchema['campagne']->setMessage('required', 'Champ obligatoire');
    }

    public function getCampagnes() {
        $campagnes = array();
        $campagne_current = CurrentClient::getCurrent()->campagne;
        for($i = 1; $i <= 4; $i++) {
            $campagnes[($campagne_current - $i).""] = ($campagne_current - $i)."";
        }

        return $campagnes;
    }

}

