<?php

class SVAjoutApporteurForm extends acCouchdbForm
{
    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('cvi', new sfWidgetFormInputText());
        $this->setValidator('cvi', new sfValidatorString(['min_length' => 10, 'max_length' => 10]));

        $this->widgetSchema->setNameFormat('sv_ajout_apporteur[%s]');
    }

    public function save($con = null)
    {
        $values = $this->getValues();

        $this->getDocument()->addProduitsFromDR('DR-' . $values['cvi'] . '-' . $this->getDocument()->getPeriode());
        $this->getDocument()->save();
    }
}
