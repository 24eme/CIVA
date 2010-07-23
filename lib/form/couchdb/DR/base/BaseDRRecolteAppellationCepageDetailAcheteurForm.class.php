<?php

abstract class BaseDRRecolteAppellationCepageDetailAcheteurForm extends sfCouchdbFormDocumentTree {

    public function setup() {
        $this->setWidgets(array(
            'cvi' => new sfWidgetFormInputText(),
            'quantite_vendue' => new sfWidgetFormInputText(),
        ));

        $this->setValidators(array(
            'cvi' => new sfValidatorString(array('required' => true)),
            'quantite_vendue' => new sfValidatorNumber(array('required' => false)),
        ));

        //$this->validatorSchema->setPostValidator();

        $this->widgetSchema->setNameFormat('dr_recolte_appellation_cepage_detail_acheteur[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        //$this->setupInheritance();

        parent::setup();
    }

    public function getModelName() {
        return 'DRRecolteAppellationCepageDetailAcheteur';
    }

}
