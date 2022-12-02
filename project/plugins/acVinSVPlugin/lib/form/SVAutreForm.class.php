<?php

class SVAutreForm extends acCouchdbForm
{
    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('lies', new bsWidgetFormInputFloat());

        $this->setValidator('lies', new sfValidatorNumber(['min' => 0]));

        $this->widgetSchema->setLabels([
            'lies' => 'Volume total lies et bourbes',
        ]);

        $this->setDefault('lies', $this->getDocument()->lies);

        $this->widgetSchema->setNameFormat('sv_autres[%s]');
    }

    public function save()
    {
        $values = $this->getValues();
        $this->getDocument()->lies = $values['lies'];
        $this->getDocument()->save();
    }
}
