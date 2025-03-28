<?php
class VracProduitsEnlevementsForm extends acCouchdbObjectForm
{
	public function configure()
    {
        $this->embedForm('produits', new VracProduitEnlevementCollectionForm($this->getObject()->declaration->getProduitsDetailsSorted()));
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
            if($form) {
        	    $form->bind($taintedValues[$key], isset($taintedFiles[$key])? $taintedFiles[$key] : null);
			    $this->updateEmbedForm($key, $form);
            }
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
