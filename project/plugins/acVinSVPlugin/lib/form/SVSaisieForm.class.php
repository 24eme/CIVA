<?php

class SVSaisieForm extends acCouchdbForm
{
    protected $cvi = null;
    protected $type = null;

	public function __construct(acCouchdbDocument $doc, $cvi = null, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->cvi = $cvi;
        $this->type = $doc->getType();
        parent::__construct($doc, $defaults, $options, $CSRFSecret);
    }

	public function configure()
    {
        $formProduits = new BaseForm();

        foreach($this->getDocument()->getProduits() as $produit) {
            if($this->cvi && $this->cvi != $produit->cvi) {
                continue;
            }
            $classSVForm = $this->type."ProduitForm";
            $formProduits->embedForm($produit->getHash(), new $classSVForm($produit));
        }

        $this->embedForm('produits', $formProduits);

        $this->widgetSchema->setNameFormat('sv_saisie[%s]');
    }

	public function save() {
		$values = $this->getValues();
		foreach ($this->getEmbeddedForm('produits')->getEmbeddedForms() as $key => $embedForm) {
			$embedForm->doUpdateObject($values['produits'][$key]);
        }
        $this->getDocument()->save();
	}

    public function hasMouts()
    {
        return count(array_filter($this->getEmbeddedForm('produits')->getEmbeddedForms(), function ($produit) {
            return isset($produit['volume_mouts']) || isset($produit['volume_mouts_revendique']);
        })) > 0;
    }
}
