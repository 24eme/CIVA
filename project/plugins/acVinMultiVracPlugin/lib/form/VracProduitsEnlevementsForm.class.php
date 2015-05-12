<?php
class VracProduitsEnlevementsForm extends acCouchdbObjectForm 
{    
	public function configure()
    {
        $this->embedForm('produits', new VracProduitEnlevementCollectionForm($this->getObject()->declaration->getActifProduitsDetailsSorted()));
        $this->widgetSchema->setNameFormat('vrac[%s]');
    }
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
        	$embedForm->doUpdateObject($values[$key]);
        }
        $this->getObject()->updateTotaux();
        $this->getObject()->updateEnlevementStatut();
    }
    
	public function bind(array $taintedValues = null, array $taintedFiles = null)
    {
        foreach ($this->embeddedForms as $key => $form) {
        	$form->bind($taintedValues[$key], $taintedFiles[$key]);
			$this->updateEmbedForm($key, $form);
        }
        parent::bind($taintedValues, $taintedFiles);
    }

    public function updateEmbedForm($name, $form) {
    	$this->widgetSchema[$name] = $form->getWidgetSchema();
        $this->validatorSchema[$name] = $form->getValidatorSchema();
    }

    public function getFormTemplateRetiraisons($detail, $key) 
    {      
        $form_embed = new VracEnlevementForm($detail->retiraisons->add());
        $form = new VracCollectionTemplateForm($this, 'produits]['.$key.'][enlevements', $form_embed);

        return $form->getFormTemplate();
    }
}