<?php
class VracProduitAjoutForm extends acCouchdbObjectForm 
{
   	public function configure()
    {
  		$this->setWidgets(array(
  			'vrac' => new sfWidgetFormInputHidden(),
  			'hash' => new sfWidgetFormInputHidden(), 
  		    'lieu_dit' => new sfWidgetFormInputText(),
        	'vtsgn' => new sfWidgetFormChoice(array('choices' => $this->getVtSgn()))
    	));
        $this->widgetSchema->setLabels(array(
        	'vrac' => 'Identifiant vrac:',
        	'hash' => 'Hash:',
        	'lieu_dit' => 'Lieu dit:',
        	'vtsgn' => 'VT/SGN:'
        ));
        $this->setValidators(array(
        	'vrac' => new sfValidatorString(array('required' => true)),
        	'hash' => new sfValidatorString(array('required' => true)),
        	'lieu_dit' => new sfValidatorString(array('required' => false)),
        	'vtsgn' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getVtSgn())))
        ));
        $this->setDefault('vrac', $this->getObject()->_id);
        $this->validatorSchema->setPostValidator(new VracProduitAjoutValidator());
  		$this->widgetSchema->setNameFormat('vrac_produit_ajout[%s]');
    }
    
    public function getVtSgn()
    {
    	return array('' => '', 'VT' => 'VT', 'SGN' =>'SGN'); 
    }
    
    public function doUpdateObject($values) {
    	$this->getObject()->addDynamiqueDetail($values['hash'], $values['lieu_dit'], $values['vtsgn']);
    }
}