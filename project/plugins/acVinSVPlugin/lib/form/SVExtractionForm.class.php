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
            $formProduitTauxExtraction->setWidget('volume_extrait', new bsWidgetFormInputFloat([], []));
            $formProduitTauxExtraction->setValidator('volume_extrait', new sfValidatorNumber(['required' => false]));
            $formProduitTauxExtraction->setWidget('taux_extraction', new bsWidgetFormInputFloat([], ["tabindex" => -1]));
            $formProduitTauxExtraction->setValidator('taux_extraction', new sfValidatorNumber(['required' => false]));

            $formProduitTauxExtraction->setDefault('taux_extraction', $produitRecap->taux_extraction);
            $formProduitTauxExtraction->setDefault('volume_extrait', $produitRecap->volume_extrait);
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
            $produitExtraction->add('volume_extrait', $value['volume_extrait']);
        }

        $this->getDocument()->recalculeVolumesRevendiques();
        $this->getDocument()->save();
    }
}
