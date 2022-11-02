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
            $formProduitTauxExtraction->setWidget('taux', new sfWidgetFormInput());
            $formProduitTauxExtraction->setValidator('taux', new sfValidatorNumber());
            $formProduitTauxExtraction->widgetSchema->setLabel('taux', $produit->libelle);

            $default_taux = $produit->getTauxExtractionDefault();
            $formProduitTauxExtraction->setDefault('taux', $default_taux);
            $formProduit->embedForm($noeud, $formProduitTauxExtraction);
        }

        $this->embedForm('produits', $formProduit);
        $this->widgetSchema->setNameFormat('sv_extraction[%s]');
    }

    public function save()
    {
        $values = $this->getValues();

        foreach ($values['produits'] as $hash => $taux) {
            $this->getDocument()->extraction->add($hash)->taux = current($taux);
        }

        $this->getDocument()->save();
    }
}
