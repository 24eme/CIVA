<?php

class SVSaisieRevendicationForm extends SVSaisieForm
{
	public function configure()
    {
        $formProduits = new BaseForm();

        foreach($this->getDocument()->getProduits() as $produit) {
            if($this->cvi && $this->cvi != $produit->cvi) {
                continue;
            }
            $formProduits->embedForm($produit->getHash(), new SV12RevendicationProduitForm($produit));
        }

        $this->embedForm('produits', $formProduits);

        $this->widgetSchema->setNameFormat('sv_saisie_revendication[%s]');
    }


        public function hasRevendication()
        {
            return count(array_filter($this->getEmbeddedForm('produits')->getEmbeddedForms(), function ($produit) {
                return isset($produit['volume_revendique']);
            })) > 0;
        }
}
