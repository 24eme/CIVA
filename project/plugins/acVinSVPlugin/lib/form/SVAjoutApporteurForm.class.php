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
        $this->widgetSchema->setLabel('cvi', "Ajout ou mise à jour d'un apporteur");

        $this->widgetSchema->setNameFormat('sv_ajout_apporteur[%s]');
    }

    public function save($con = null)
    {
        $values = $this->getValues();

        if (in_array(substr($values['cvi'], 0, 2), ['68', '67']) === false) {
            $this->getDocument()->addApporteurHorsRegion($values['cvi']);
        } else {
            $dr = DRClient::getInstance()->find('DR-' . $values['cvi'] . '-' . $this->getDocument()->getPeriode());
            $this->getDocument()->addProduitsFromDR($dr);
        }

        $this->getDocument()->save();
    }
}
