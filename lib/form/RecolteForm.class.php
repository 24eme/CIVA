<?php

class RecolteForm extends sfCouchdbFormDocumentJson {

    public function configure() {
        $this->setWidgets(array(
            'denomination' => new sfWidgetFormInputText(),
            'vtsgn' => new sfWidgetFormInputText(),
            'superficie' => new sfWidgetFormInputText(),
            'cave_particuliere' => new sfWidgetFormInputText(),
        ));

        $this->setValidators(array(
            'denomination' => new sfValidatorString(),
            'vtsgn' => new sfValidatorString(),
            'superficie' => new sfValidatorNumber(),
            'cave_particuliere' => new sfValidatorNumber(),
        ));

        //$this->validatorSchema->setPostValidator();

        $this->widgetSchema->setNameFormat('recolte[%s]');
        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    }

}