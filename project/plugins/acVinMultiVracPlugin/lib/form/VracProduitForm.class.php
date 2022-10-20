<?php
class VracProduitForm extends acCouchdbObjectForm
{
   	public function configure()
    {
        $quantiteType = ($this->getObject()->getDocument()->isInModeSurface())? 'surface' : 'volume';
  		$this->setWidgets(array(
        	'millesime' => new sfWidgetFormInputText(),
  		    'denomination' => new sfWidgetFormInputText(),
 		    'label' => new sfWidgetFormChoice(array('choices' => $this->getBioChoices())),
        	'prix_unitaire' => new sfWidgetFormInputFloat(),
        	$quantiteType.'_propose' => new sfWidgetFormInputFloat()
    	));
        $this->widgetSchema->setLabels(array(
        	'millesime' => 'Millésime:',
        	'denomination' => 'Dénomination:',
            'label' => 'Agriculture biologique:',
        	'prix_unitaire' => 'Prix unitaire:',
        	$quantiteType.'_propose' => ucfirst($quantiteType).' estimé:'
        ));
        $this->setValidators(array(
        	'millesime' => new sfValidatorString(array('required' => false, 'max_length' => 4, 'min_length' => 4), array('max_length' =>  '4 caractères max.', 'min_length' =>  '4 caractères min.')),
        	'denomination' => new sfValidatorString(array('required' => false)),
            'label' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getBioChoices()))),
        	'prix_unitaire' => new sfValidatorNumber(array('required' => false)),
        	$quantiteType.'_propose' => new sfValidatorNumber(array('required' => false))
        ));
        if ($this->getObject()->getDocument()->type_contrat == VracClient::TYPE_BOUTEILLE) {
        	unset($this[$quantiteType.'_propose']);
        	$centilisations = array('' => '')+VracClient::getCentilisations();
        	$this->setWidget('centilisation', new sfWidgetFormChoice(array('choices' => $centilisations)));
        	$this->setWidget('nb_bouteille', new sfWidgetFormInputText());
        	$this->widgetSchema->setLabel('centilisation', 'Centilisation');
        	$this->widgetSchema->setLabel('nb_bouteille', 'Nombre de bouteille');
        	$this->setValidator('centilisation', new sfValidatorChoice(array('choices' => array_keys($centilisations), 'required' => false)));
        	$this->setValidator('nb_bouteille', new sfValidatorInteger(array('required' => false)));
            unset($this->widgetSchema['label']);
            unset($this->validatorSchema['label']);
        }

  		$this->widgetSchema->setNameFormat('vrac_conditions[%s]');
    }

    public function getVtSgn()
    {
    	return array('' => '', 'VT' => 'VT', 'SGN' =>'SGN');
    }

    public function getBioChoices()
    {
      return array('' => '', 'AUCUN' => 'Conventionnel', VracClient::LABEL_BIO => 'Bio', VracClient::LABEL_HVE => 'HVE', VracClient::LABEL_BIO_HVE => 'Bio & HVE');
    }

    protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();

        if($this->getObject()->exist('label') && ($this->getObject()->label === false || $this->getObject()->label === "0")) {
            $this->setDefault('label', 'AUCUN');
        }
    }

    public function doUpdateObject($values) {
        parent::doUpdateObject($values);
        if (isset($values['centilisation'])) {
        	$this->getObject()->centilisation = floatval($values['centilisation']);
        }
        if (array_key_exists('label', $values) && $values['label'] === "AUCUN") {
        	$this->getObject()->add('label', false);
        } elseif(array_key_exists('label', $values) && empty($values['label'])) {
		$this->getObject()->remove('label');
	}
        $this->getObject()->defineActive();
        $this->getObject()->updateProduit();
        if (!$this->getObject()->actif) {
        	$this->getObject()->clear();
        }
    }

}
