<?php

class SVStartupForm extends acCouchdbForm
{
    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('action', new bsWidgetFormChoice([
            'expanded' => true,
            'choices' => $this->getChoices()
        ]));

        $this->setValidator('action', new sfValidatorChoice([
            'choices' => array_keys($this->getChoices()),
            'required' => true
        ]));

        $this->widgetSchema->setNameFormat('sv_startup[%s]');
    }

    public function getChoices()
    {
        return [
            'reprendre' => 'Continuer ma déclaration',
            'supprimer' => 'Supprimer ma déclaration'
        ];
    }

    public function save($con = null)
    {
        return ($this->getValue('action') === 'supprimer') ? false : true;
    }
}
