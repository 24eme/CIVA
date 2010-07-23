<?php

abstract class BaseDRRecolteAppellationCepageDetailForm extends sfCouchdbFormDocumentTree {

    public function setup() {
        $this->setWidgets(array(
            'appellation' => new sfWidgetFormInputText(),
            'cepage' => new sfWidgetFormInputText(),
            'denomination' => new sfWidgetFormInputText(),
            'vtsgn' => new sfWidgetFormInputText(),
            'code_lieu' => new sfWidgetFormInputText(),
            'surface' => new sfWidgetFormInputText(),
            'volume' => new sfWidgetFormInputText(),
            'cave_particuliere' => new sfWidgetFormInputText(),
            'volume_revendique' => new sfWidgetFormInputText(),
            'volume_dplc' => new sfWidgetFormInputText(),
        ));

        $this->setValidators(array(
            'appellation' => new sfValidatorString(array('required' => true)),
            'cepage' => new sfValidatorString(array('required' => true)),
            'denomination' => new sfValidatorString(array('required' => false)),
            'vtsgn' => new sfValidatorString(array('required' => false)),
            'code_lieu' => new sfValidatorString(array('required' => false)),
            'surface' => new sfValidatorNumber(array('required' => false)),
            'volume' => new sfValidatorNumber(array('required' => false)),
            'cave_particuliere' => new sfValidatorNumber(array('required' => false)),
            'volume_revendique' => new sfValidatorNumber(array('required' => false)),
            'volume_dplc' => new sfValidatorNumber(array('required' => false)),
        ));

        //$this->validatorSchema->setPostValidator();

        $this->widgetSchema->setNameFormat('dr_recolte_appellation_cepage_detail[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        //$this->setupInheritance();

        parent::setup();
    }

    public function getModelName() {
        return 'DRRecolteAppellationCepageDetail';
    }

}
