<?php
class VracProduitEnlevementForm extends acCouchdbObjectForm 
{
   	public function configure()
    {
    	$this->disableLocalCSRFProtection();
  		$this->setWidgets(array(
        	'cloture' => new WidgetFormInputCheckbox()
    	));
        $this->setValidators(array(
        	'cloture' => new ValidatorBoolean(array('required' => false))
        ));
        $enlevements = new VracEnlevementsCollectionForm($this->getObject()->retiraisons);
        $this->embedForm('enlevements', $enlevements);
  		$this->widgetSchema->setNameFormat('produit[%s]');
    }
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
        	$embedForm->doUpdateObject($values[$key]);
        }
    	$this->getObject()->updateVolumeEnleve();
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
}