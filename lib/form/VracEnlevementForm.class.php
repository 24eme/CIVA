<?php
class VracEnlevementForm extends acCouchdbObjectForm {
	protected static $_francize_date = array(
    	'date',
    );
	public function configure()
	{
		$this->disableLocalCSRFProtection();
		$this->setWidgets(array(
		   'volume' => new sfWidgetFormInputFloat(),
		   'date' => new sfWidgetFormInputText()
		));
		$this->widgetSchema->setLabels(array(
	       'volume' => 'Volume:',
	       'date' => 'Date retiraison:'
		));
		$this->setValidators(array(
	       'volume' => new sfValidatorNumber(array('required' => false)),
	       'date' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => false), array('invalid' => 'Format valide : dd/mm/aaaa'))
		));
		$this->widgetSchema->setNameFormat('enlevement[%s]');
	}
    
	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        foreach (self::$_francize_date as $field) {
        	if (isset($defaults[$field]) && !empty($defaults[$field])) {
        		$defaults[$field] = Date::francizeDate($defaults[$field]);
        	}
        }   
        $this->setDefaults($defaults);      
    }
}