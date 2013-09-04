<?php
class VracProduitForm extends acCouchdbObjectForm 
{
   	public function configure()
    {
  		$this->setWidgets(array(
        	'millesime' => new sfWidgetFormInputText(),
  		    'denomination' => new sfWidgetFormInputText(),
        	'vtsgn' => new sfWidgetFormChoice(array('choices' => $this->getVtSgn())),
        	'prix_unitaire' => new sfWidgetFormInputFloat(),
        	'volume_propose' => new sfWidgetFormInputFloat()
    	));
        $this->widgetSchema->setLabels(array(
        	'millesime' => 'Millésime:',
        	'denomination' => 'Dénomination:',
        	'vtsgn' => 'VT/SGN:',
        	'prix_unitaire' => 'Prix unitaire:',
        	'volume_propose' => 'Volume proposé:'
        ));
        $this->setValidators(array(
        	'millesime' => new sfValidatorString(array('required' => false)),
        	'denomination' => new sfValidatorString(array('required' => false)),
        	'vtsgn' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getVtSgn()))),
        	'prix_unitaire' => new sfValidatorNumber(array('required' => false)),
        	'volume_propose' => new sfValidatorNumber(array('required' => false))
        ));
		if ($this->getObject()->getCepage()->no_vtsgn) {
			unset($this['vtsgn']);
		}
  		//$this->validatorSchema->setPostValidator(new VracConditionsValidator());
  		$this->widgetSchema->setNameFormat('vrac_conditions[%s]');
    }
    
    public function getVtSgn()
    {
    	return array('VT' => 'VT', 'SGN' =>'SGN'); 
    }
    
    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
    }
}