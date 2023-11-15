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
        $this->setValidator('cvi', new sfValidatorRegex(['pattern' => '/[0-9]{10}|[A-Z]{2}[A-Z0-9]{8,12}/'], ['invalid' => 'Le format CVI n\'est pas correct']));
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
