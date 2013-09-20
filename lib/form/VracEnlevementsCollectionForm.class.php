<?php
class VracEnlevementsCollectionForm extends acCouchdbObjectForm
{
	public $virgin_object = null;
	
	public function configure()
	{
		$this->disableLocalCSRFProtection();
		if (count($this->getObject()) == 0) {
			$this->virgin_object = $this->getObject()->add();
		}
		foreach ($this->getObject() as $key => $object) {
			$this->embedForm ($key, new VracEnlevementForm($object));
		}
	}
    
    public function doUpdateObject($values) 
    {
        parent::doUpdateObject($values);
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
        	$embedForm->doUpdateObject($values[$key]);
        }
    }

	public function bind(array $taintedValues = null, array $taintedFiles = null)
	{
		foreach ($this->embeddedForms as $key => $form) {
			if(!array_key_exists($key, $taintedValues)) {
				$this->unEmbedForm($key);
			}
		}
		foreach($taintedValues as $key => $values) {
			if(!is_array($values) || array_key_exists($key, $this->embeddedForms)) {
				continue;
			}

			$this->embedForm($key, new VracEnlevementForm($this->getObject()));
		}

		//parent::bind($taintedValues, $taintedFiles);
	}
	
	public function updateEmbedForm($name, $form) {
    	$this->widgetSchema[$name] = $form->getWidgetSchema();
        $this->validatorSchema[$name] = $form->getValidatorSchema();
    }

	public function unEmbedForm($key)
	{
		unset($this->widgetSchema[$key]);
		unset($this->validatorSchema[$key]);
		unset($this->embeddedForms[$key]);
		$this->getObject()->remove($key);
	}
	
	public function offsetUnset($offset) {
		parent::offsetUnset($offset);
		if (!is_null($this->virgin_object)) {
			$this->virgin_object->delete();
		}
    }
}