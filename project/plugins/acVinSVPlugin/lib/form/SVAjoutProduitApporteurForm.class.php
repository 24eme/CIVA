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

        $this->setWidget('mouts', new sfWidgetFormInputCheckbox());
        $this->setValidator('mouts', new sfValidatorBoolean(['required' => false]));

        $this->widgetSchema->setNameFormat('sv_ajout_produit_apporteur[%s]');
    }

    public function save($con = null)
    {
        $values = $this->getValues();
        $hash = $values['produit'];
        $newProduit = $this->getDocument()->addProduit($this->cvi, $hash);

        if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/RS") !== false) {
            $this->getDocument()->addProduit($this->cvi, str_replace("/cepages/RS", "/cepages/RBRS", $hash));
        }

        if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/BL") !== false) {
            $this->getDocument()->addProduit($this->cvi, str_replace("/cepages/BL", "/cepages/RBBL", $hash));
        }

        if (isset($values['mouts']) && $values['mouts'] && ! $newProduit->exist('volume_mouts')) {
            $newProduit->add('volume_mouts');
            $newProduit->add('volume_mouts_revendique');
            $newProduit->add('superficie_mouts');
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
}
