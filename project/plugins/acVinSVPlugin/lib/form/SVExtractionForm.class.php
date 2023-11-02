<?php

class SVExtractionForm extends acCouchdbForm
{
    public function __construct(SV $doc, $options = [], $CSRFSecret = null)
    {
        parent::__construct($doc, [], $options, $CSRFSecret);
    }

    public function configure()
    {
        $formProduit = new BaseForm();

        foreach ($this->getDocument()->getRecapProduits() as $hash => $produit) {
            $noeud = str_replace('/declaration/', '', $hash);

            if (array_key_exists($noeud, $formProduit->getEmbeddedForms())) {
                continue;
            }

            $produitRecap = $this->getDocument()->extraction->add($noeud);

            $formProduitTauxExtraction = new BaseForm();
            $formProduitTauxExtraction->setWidget('volume_revendique_total', new bsWidgetFormInputFloat([], []));
            $formProduitTauxExtraction->setValidator('volume_revendique_total', new sfValidatorNumber(['required' => false]));
            $formProduitTauxExtraction->setWidget('taux_extraction', new bsWidgetFormInputFloat([], ["readonly" => "readonly"]));
            $formProduitTauxExtraction->setValidator('taux_extraction', new sfValidatorNumber(['required' => false]));

            $formProduitTauxExtraction->setDefault('taux_extraction', $produitRecap->taux_extraction);
            $formProduitTauxExtraction->setDefault('volume_revendique_total', $produitRecap->volume_revendique_total);
            $formProduit->embedForm($noeud, $formProduitTauxExtraction);
        }

        $this->embedForm('produits', $formProduit);
        $this->widgetSchema->setNameFormat('sv_extraction[%s]');
    }

    public function save()
    {
        $values = $this->getValues();
        $this->getDocument()->remove('extraction');
        $this->getDocument()->add('extraction');
        foreach ($values['produits'] as $hash => $value) {
            if(is_null($value['taux_extraction'])) {
                continue;
            }
            $produitExtraction = $this->getDocument()->extraction->add($hash);
            $produitExtraction->add('volume_revendique_total', $value['volume_revendique_total']);
        }

        $this->getDocument()->recalculeVolumesRevendiques();
        $this->getDocument()->save();
    }
}
