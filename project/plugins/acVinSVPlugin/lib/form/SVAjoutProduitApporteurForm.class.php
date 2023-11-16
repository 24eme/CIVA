<?php

class SVAjoutProduitApporteurForm extends acCouchdbForm
{
    protected $cvi;

    public function __construct(SV $doc, $cvi, $options = [], $CSRFSecret = null)
    {
        $this->cvi = $cvi;
        parent::__construct($doc, $options, $CSRFSecret);
    }

    public function configure()
    {
        $this->setWidget('produit', new sfWidgetFormChoice(['choices' => array_combine(array_keys($this->getProduits()), $this->getProduits())]));
        $this->setValidator('produit', new sfValidatorChoice(['choices' => array_keys($this->getProduits())]));

        $this->setWidget('denomination_complementaire', new sfWidgetFormInputText());
        $this->setValidator('denomination_complementaire', new sfValidatorString(['required' => false]));

        $this->widgetSchema->setNameFormat('sv_ajout_produit_apporteur[%s]');
    }

    public function save($con = null)
    {
        $values = $this->getValues();
        $hash = $values['produit'];
        $denom = $values['denomination_complementaire'] ?: null;

        if ($this->isAlsace($this->cvi) === false) {
            $apporteur = $this->getDocument()->apporteurs->get($this->cvi);
            $rs = $apporteur->getNom();
            $c = $apporteur->getCommune();
        }

        $newProduit = $this->getDocument()->addProduit($this->cvi, $hash, $denom);

        if ($this->isAlsace($this->cvi) === false) {
            $newProduit->nom = $rs;
            $newProduit->commune = $c;
        }

        $this->getDocument()->save();
    }

    public function getProduits()
    {
        $produits = [];
        foreach (ConfigurationClient::getInstance()->getCurrent()->declaration->getProduitsAll() as $produit) {
            $produits[$produit->getHash()] = $produit->getLibelleFormat();
        }
        return $produits;
    }

    private function isAlsace($cvi)
    {
        return in_array(substr($cvi, 0, 2), ['68', '67']);
    }
}
