<?php
class VracEnlevementsCollectionForm extends acCouchdbObjectForm
{
	public $virgin_object = null;

	public function configure()
	{
		$this->disableLocalCSRFProtection();
		foreach ($this->getObject() as $key => $object) {
			$this->embedForm($key, new VracEnlevementForm($object));
		}
	}

	public function bind(array $taintedValues = null, array $taintedFiles = null)
	{
        foreach($taintedValues as $key => $values) {
           if(!is_array($values) || array_key_exists($key, $this->embeddedForms)) {
               continue;
           }
           $this->embedForm($key, new VracEnlevementForm($this->getObject()));
       }
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