<?php
class VracForm extends acCouchdbObjectForm 
{    
	public function configure()
    {
        $this->embedForm('produits', new VracProduitCollectionForm($this->getObject()->declaration->getProduitsDetailsSorted()));
        $this->widgetSchema->setNameFormat('vrac[%s]');
    }
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
        	$embedForm->doUpdateObject($values[$key]);
        }
    }
}