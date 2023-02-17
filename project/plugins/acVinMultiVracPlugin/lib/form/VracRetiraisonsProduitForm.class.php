<?php
class VracRetiraisonsProduitForm extends acCouchdbObjectForm
{
	protected static $_francize_date = array(
    	'retiraison_date_debut',
        'retiraison_date_limite'
    );
   	public function configure()
    {
  		$this->setWidgets(array(
        	'retiraison_date_debut' => new sfWidgetFormInputText(),
  		    'retiraison_date_limite' => new sfWidgetFormInputText(),
    	));
        $this->widgetSchema->setLabels(array(
        	'retiraison_date_debut' => 'Début de retiraison:',
        	'retiraison_date_limite' => 'Limite de retiraison:'
        ));

        if ($this->getObject()->getDocument()->isPluriannuelCadre()) {
            $this->setValidators(array(
            	'retiraison_date_debut' => new sfValidatorRegex(array('required' => false, 'pattern' => "/^([0-9]){2}\/([0-9]){2}$/"), array('invalid' => 'Format valide : dd/mm')),
            	'retiraison_date_limite' => new sfValidatorRegex(array('required' => false, 'pattern' => "/^([0-9]){2}\/([0-9]){2}$/"), array('invalid' => 'Format valide : dd/mm')),
            ));
        } else {
            $this->setValidators(array(
                'retiraison_date_debut' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => false), array('invalid' => 'Format valide : dd/mm/aaaa')),
                'retiraison_date_limite' => new sfValidatorDate(array('date_output' => 'Y-m-d', 'date_format' => '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~', 'required' => false), array('invalid' => 'Format valide : dd/mm/aaaa')),
            ));
        }
  		$this->widgetSchema->setNameFormat('produit_retiraisons[%s]');
    }

	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        foreach (self::$_francize_date as $field) {
        	if (isset($defaults[$field]) && !empty($defaults[$field]) && $this->getObject()->getDocument()->isPonctuel()) {
        		$defaults[$field] = Date::francizeDate($defaults[$field]);
        	}
        	if (isset($defaults[$field]) && !empty($defaults[$field]) && $this->getObject()->getDocument()->isPluriannuelCadre()) {
        		$defaults[$field] = $this->getFormattedPeriode($defaults[$field]);
        	}
        }

        if($this->getObject()->getDocument()->isPluriannuelCadre() && !isset($defaults['retiraison_date_debut']) && ! isset($defaults['retiraison_date_limite'])) {
            $defaults['retiraison_date_debut'] = '31/12';
            $defaults['retiraison_date_limite'] = '31/07';
        }
        $this->setDefaults($defaults);
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
        if (isset($values['retiraison_date_debut'])) {
        	$this->getObject()->retiraison_date_debut = $this->getFormattedPeriode($values['retiraison_date_debut']);
        }
        if (isset($values['retiraison_date_limite'])) {
        	$this->getObject()->retiraison_date_limite = $this->getFormattedPeriode($values['retiraison_date_limite']);
        }
    }

    private function getFormattedPeriode($periode) {
        if ($this->getObject()->getDocument()->isPluriannuelCadre() && strlen($periode) == 5) {
            return (strpos($periode, '-') !== false)? substr($periode, -2).'/'.substr($periode, 0, 2) : substr($periode, -2).'-'.substr($periode, 0, 2);
        }
        return $periode;
    }

}
