<?php
class VracProduitEnlevementCollectionForm extends sfForm 
{
	protected $details;
	
	public function __construct($details, $defaults = array(), $options = array(), $CSRFSecret = null)
  	{
  		$this->details = $details;
		parent::__construct($defaults, $options, $CSRFSecret);
  	}
  	
   	public function configure()
    {
    	$this->disableLocalCSRFProtection();
    	foreach ($this->details as $details) {
			foreach ($details as $hash => $detail) {
				$this->embedForm($hash, new VracProduitEnlevementForm($detail));
			}
    	}
    }

    public function doUpdateObject($values) 
    {
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
        	$embedForm->doUpdateObject($values[$key]);
        }
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