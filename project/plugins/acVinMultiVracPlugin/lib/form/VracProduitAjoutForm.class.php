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
    	$this->getObject()->addDynamiqueDetail(HashMapper::inverse($values['hash']), $values['lieu_dit'], $values['vtsgn']);
    }

    public function getAppellations()
    {
        $appellations = array_filter(ConfigurationClient::getCurrent()->declaration->getArrayAppellations(), function($appellation, $key) {
            if ($this->getObject()->type_contrat == VracClient::TYPE_MOUT && $appellation->getKey() != 'CREMANT') {
                return false;
            }

            if (in_array($appellation->getCertification()->getKey(), ["AOC_ALSACE", "VINSSIG"]) === false) {
                return false;
            }

            if ($appellation->getGenre()->getKey() === "VCI") { return false; }

            if ($appellation->getCertification()->getKey() === "VINSSIG" && ($appellation->getGenre()->getKey() === "EFF" || $appellation->getKey() !== "VINTABLE")) {
                return false;
            }

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        return $appellations;
    }
}
