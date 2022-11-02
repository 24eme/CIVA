<?php

class SVAutreForm extends acCouchdbForm
{
    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, [], $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('lies', new sfWidgetFormInput());
        $this->setWidget('mouts', new sfWidgetFormInput());

        $this->setValidator('lies', new sfValidatorNumber());
        $this->setValidator('mouts', new sfValidatorNumber());

        $this->widgetSchema->setNameFormat('sv_autres[%s]');
    }

    public function save()
    {
        $this->getDocument()->save();
    }
}
