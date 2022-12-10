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
        $this->setWidget('rebeches', new bsWidgetFormInputFloat([], ['disabled' => ($this->getDocument()->hasRebechesInProduits()) ? true : false]));

        $this->setValidator('lies', new sfValidatorNumber(['required' => false, 'min' => 0]));
        $this->setValidator('rebeches', new sfValidatorNumber(['required' => false, 'min' => 0]));

        $this->widgetSchema->setLabels([
            'lies' => 'Volume total lies et bourbes produit',
            'rebeches' => 'Volume total rebêches',
        ]);

        $this->setDefault('lies', $this->getDocument()->lies);
        $this->setDefault('rebeches', $this->getDocument()->calculateRebeches());

        $this->widgetSchema->setNameFormat('sv_autres[%s]');
    }

    public function save()
    {
        $values = $this->getValues();
        $this->getDocument()->lies = $values['lies'];

        if ($this->getDocument()->hasRebechesInProduits()) {
            $this->getDocument()->rebeches = $this->getDocument()->calculateRebeches();
        } else {
            $this->getDocument()->rebeches = $values['rebeches'];
        }

        $this->getDocument()->save();
    }
}
