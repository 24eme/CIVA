<?php
class VracProduitForm extends acCouchdbObjectForm 
{
   	public function configure()
    {
  		$this->setWidgets(array(
        	'millesime' => new sfWidgetFormInputText(),
  		    'denomination' => new sfWidgetFormInputText(),
        	'prix_unitaire' => new sfWidgetFormInputFloat(),
        	'volume_propose' => new sfWidgetFormInputFloat()
    	));
        $this->widgetSchema->setLabels(array(
        	'millesime' => 'Millésime:',
        	'denomination' => 'Dénomination:',
        	'prix_unitaire' => 'Prix unitaire:',
        	'volume_propose' => 'Volume estimé:'
        ));
        $this->setValidators(array(
        	'millesime' => new sfValidatorString(array('required' => false, 'max_length' => 4, 'min_length' => 4), array('max_length' =>  '4 caractères max.', 'min_length' =>  '4 caractères min.')),
        	'denomination' => new sfValidatorString(array('required' => false)),
        	'prix_unitaire' => new sfValidatorNumber(array('required' => false)),
        	'volume_propose' => new sfValidatorNumber(array('required' => false))
        ));
        if ($this->getObject()->getDocument()->type_contrat == VracClient::TYPE_BOUTEILLE) {
        	unset($this['volume_propose']);
        	$centilisations = array('' => '')+VracClient::getCentilisations();
        	$this->setWidget('centilisation', new sfWidgetFormChoice(array('choices' => $centilisations)));
        	$this->setWidget('nb_bouteille', new sfWidgetFormInputText());
        	$this->widgetSchema->setLabel('centilisation', 'Centilisation');
        	$this->widgetSchema->setLabel('nb_bouteille', 'Nombre de bouteille');
        	$this->setValidator('centilisation', new sfValidatorChoice(array('choices' => array_keys($centilisations), 'required' => false)));
        	$this->setValidator('nb_bouteille', new sfValidatorInteger(array('required' => false)));
        }
  		$this->widgetSchema->setNameFormat('vrac_conditions[%s]');
    }
    
    public function getVtSgn()
    {
    	return array('' => '', 'VT' => 'VT', 'SGN' =>'SGN'); 
    }
    
    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
        if (isset($values['centilisation'])) {
        	$this->getObject()->centilisation = floatval($values['centilisation']);
        }
        $this->getObject()->defineActive();
        $this->getObject()->updateProduit();
        if (!$this->getObject()->actif) {
        	$this->getObject()->clear();
        }
    }
}