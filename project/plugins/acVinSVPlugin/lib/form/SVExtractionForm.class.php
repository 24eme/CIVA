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

        foreach ($this->getDocument()->getProduits() as $hash => $produit) {
            $noeud = str_replace('/declaration/', '', $produit->getProduitHash());

            if (array_key_exists($noeud, $formProduit->getEmbeddedForms())) {
                continue;
            }

            $formProduitTauxExtraction = new BaseForm();
            $formProduitTauxExtraction->setWidget('taux_extraction', new sfWidgetFormInput());
            $formProduitTauxExtraction->setValidator('taux_extraction', new sfValidatorNumber());
            $formProduitTauxExtraction->widgetSchema->setLabel('taux_extraction', $produit->getLibelleHtml());

            $default_taux = $produit->getTauxExtractionDefault();
            $formProduitTauxExtraction->setDefault('taux_extraction', $default_taux);
            $formProduit->embedForm($noeud, $formProduitTauxExtraction);
        }

        $this->embedForm('produits', $formProduit);
        $this->widgetSchema->setNameFormat('sv_extraction[%s]');
    }

    public function save()
    {
        $values = $this->getValues();

        foreach ($values['produits'] as $hash => $taux) {
            $this->getDocument()->extraction->add($hash)->taux_extraction = current($taux);
        }

        $this->getDocument()->save();
    }
}
