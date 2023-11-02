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

            $formProduitTauxExtraction = new BaseForm();
            $formProduitTauxExtraction->setWidget('taux_extraction', new bsWidgetFormInputFloat([], []));
            $formProduitTauxExtraction->setValidator('taux_extraction', new sfValidatorNumber(['required' => false]));
            $formProduitTauxExtraction->widgetSchema->setLabel('taux_extraction', $produit->libelle_html);

            $default_taux = $produit->taux_extraction;
            $formProduitTauxExtraction->setDefault('taux_extraction', $default_taux);
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
            $this->getDocument()->extraction->add($hash)->taux_extraction = $value['taux_extraction'];
        }

        $this->getDocument()->recalculeVolumesRevendiques();
        $this->getDocument()->save();
    }
}
