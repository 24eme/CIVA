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
        $this->setWidget('produit', new bsWidgetFormChoice(['choices' => array_combine(array_keys($this->getProduits()), $this->getProduits())]));
        $this->setValidator('produit', new sfValidatorChoice(['choices' => array_keys($this->getProduits())]));

        $this->setWidget('denomination_complementaire', new sfWidgetFormInputText());
        $this->setValidator('denomination_complementaire', new sfValidatorString(['required' => false]));

        $this->widgetSchema->setNameFormat('sv_ajout_produit_apporteur[%s]');
    }

    public function save($con = null)
    {
        $mouts = false;

        $values = $this->getValues();
        $hash = $values['produit'];
        $denom = $values['denomination_complementaire'] ?: null;

        if (strpos($hash, '/mouts') !== false) {
            $hash = str_replace('/mouts', '', $hash);
            $mouts = true;
        }

        if ($this->isAlsace($this->cvi) === false) {
            $apporteur = $this->getDocument()->apporteurs->get($this->cvi);
            $nom = $apporteur->getNom();
            $commune = $apporteur->getCommune();

            $newProduit = $this->getDocument()->addProduit($this->cvi, $hash, $denom);
            $newProduit->nom = $nom;
            $newProduit->commune = $commune;
        } else {
            $newProduit = $this->getDocument()->addProduit($this->cvi, $hash, $denom);
        }

        if ($mouts && ! $newProduit->exist('volume_mouts')) {
            $newProduit->add('volume_mouts');
            $newProduit->add('volume_mouts_revendique');
            $newProduit->add('superficie_mouts');
        }

        $this->getDocument()->save();
    }

    public function getProduits()
    {
        $produits = ["" => ""];
        foreach (ConfigurationClient::getInstance()->getCurrent()->declaration->getProduitsAll() as $produit) {
            if($produit->getAttribut('no_dr') && strpos($produit->getHash(), '/CREMANT/') === false) {
                continue;
            }
            if(!in_array($produit->getAppellation()->getCertification()->getKey(), array("AOC_ALSACE", "VINSSIG"))) {
                continue;
            }
            if(!$sv->isFromCSV() && strpos($produit->getHash(), '/cepages/RB') !== false) {
                continue;
            }
            if($produit->getAppellation()->getAttribut('no_dr')) {
                continue;
            }
            if($produit->getAppellation()->getGenre()->getKey() == "VCI") {
                continue;
            }
            $produits[$produit->getHash()] = $produit->getLibelleFormat();

            // Si crémant, on rajoute un deuxième produit mouts
            if (strpos($produit->getHash(), '/cepages/RB') === false && strpos($produit->getHash(), '/CREMANT/') !== false && in_array($produit->getCepage()->getKey(), ['BL', 'RS'])) {
                $produits[$produit->getHash().'/mouts'] = 'Moût - '.$produit->getLibelleFormat();
            }
        }
        return $produits;
    }

    private function isAlsace($cvi)
    {
        return in_array(substr($cvi, 0, 2), ['68', '67']);
    }
}
