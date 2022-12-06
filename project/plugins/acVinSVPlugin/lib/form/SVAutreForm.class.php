<?php

class SVAutreForm extends acCouchdbForm
{
    protected $produits_rebeches = null;

    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('lies', new bsWidgetFormInputFloat());
        $this->setWidget('rebeches', new bsWidgetFormInputFloat([], ['disabled' => ($this->hasRebechesInProduits()) ? true : false]));

        $this->setValidator('lies', new sfValidatorNumber(['min' => 0]));
        $this->setValidator('rebeches', new sfValidatorNumber(['required' => false, 'min' => 0]));

        $this->widgetSchema->setLabels([
            'lies' => 'Volume total lies et bourbes produit',
            'rebeches' => 'Volume total rebêches',
        ]);

        $this->setDefault('lies', $this->getDocument()->lies);
        $this->setDefault('rebeches', $this->calculateRebeches());

        $this->widgetSchema->setNameFormat('sv_autres[%s]');
    }

    public function hasRebechesInProduits()
    {
        if ($this->produits_rebeches !== null) {
            return empty($this->produits_rebeches) === false;
        }

        $this->produits_rebeches = array_filter($this->getDocument()->getRecapProduits(), function ($v, $k) { return strpos($k, '/cepages/RB') !== false; }, ARRAY_FILTER_USE_BOTH);
        return empty($this->produits_rebeches) === false;
    }

    public function calculateRebeches()
    {
        $total_rebeches = $this->getDocument()->rebeches ?? null;

        if ($this->hasRebechesInProduits()) {
            $total_rebeches = array_reduce($this->produits_rebeches, function ($total, $p) { return $total += $p->volume_recolte; }, 0);
        }

        return $total_rebeches;
    }

    public function save()
    {
        $values = $this->getValues();
        $this->getDocument()->lies = $values['lies'];

        if ($this->hasRebechesInProduits()) {
            $this->getDocument()->rebeches = $this->calculateRebeches();
        } else {
            $this->getDocument()->rebeches = $values['rebeches'];
        }

        $this->getDocument()->save();
    }
}
