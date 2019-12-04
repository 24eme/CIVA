<?php
class VracProduitsForm extends acCouchdbObjectForm 
{    
	public function configure()
    {
        $this->embedForm('produits', new VracProduitCollectionForm($this->getObject()->declaration->getProduitsDetailsSorted()));
        $this->widgetSchema->setNameFormat('vrac_produits[%s]');
    }
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
        	$embedForm->doUpdateObject($values[$key]);
        }
        $this->getObject()->updateTotaux();
    }

	public function hasBio() {
		foreach($this['produits'] as $item) {
			if(isset($item['bio'])) {
				return true;
			}
		}

		return false;
	}
}
