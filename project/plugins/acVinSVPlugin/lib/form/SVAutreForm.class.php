<?php

class SVAutreForm extends acCouchdbObjectForm
{
    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('lies', new bsWidgetFormInput());
        $this->setWidget('rebeches', new bsWidgetFormInput());

        $this->setValidator('lies', new sfValidatorNumber(['min' => 0]));
        $this->setValidator('rebeches', new sfValidatorNumber(['min' => 0]));

        $this->widgetSchema->setLabels([
            'lies' => 'Lies (en hl)',
            'rebeches' => 'Rebêches'
        ]);

        $this->widgetSchema->setNameFormat('sv_autres[%s]');
    }
}
