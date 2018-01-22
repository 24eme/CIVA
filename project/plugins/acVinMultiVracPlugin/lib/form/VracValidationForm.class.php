<?php
class VracValidationForm extends acCouchdbObjectForm
{
	protected $annuaire;

	public function __construct(acCouchdbJson $object, $annuaire = null, $options = array(), $CSRFSecret = null)
	{
		$this->annuaire = $annuaire;
		parent::__construct($object, $options, $CSRFSecret);
	}

	public function configure()
    {
		if($this->getObject()->isPapier()) {
			$this->setWidget('numero_papier', new sfWidgetFormInputText());
			$this->setValidator('numero_papier',  new sfValidatorString(array('required' => true)));
			$this->getWidgetSchema()->setLabel('numero_papier', "Numéro de contrat papier :");
			$this->getValidator('numero_papier')->setMessage('required', 'Le numéro de contrat papier est requis');

			$this->setWidget('date_signature', new sfWidgetFormInputText());
			$this->setValidator('date_signature',  new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date d\'édition doit être jj/mm/aaaa')));
			$this->getWidgetSchema()->setLabel('date_signature', "Date de la signature :");
			$this->getValidator('date_signature')->setMessage('required', 'La date de signature est requise');
		} else {
			$this->setWidgets(array(
	        	'conditions_paiement' => new sfWidgetFormInputText(),
	        	'conditions_particulieres' => new sfWidgetFormInputText(),
	    	));

	        $this->widgetSchema->setLabels(array(
	        	'conditions_paiement' => 'Conditions de paiement :',
	        	'conditions_particulieres' => 'Conditions particulières :',
	        ));

			$this->setValidators(array(
	        	'conditions_paiement' => new sfValidatorString(array('required' => false)),
	        	'conditions_particulieres' => new sfValidatorString(array('required' => false)),
	        ));

			if($this->getObject()->exist('clause_reserve_propriete')) {
				$this->setWidget('clause_reserve_propriete', new sfWidgetFormInputCheckbox());
				$this->setValidator('clause_reserve_propriete', new sfValidatorBoolean(array('required' => false)));
				$this->getWidgetSchema()->setLabel('clause_reserve_propriete', "Clause de réserve de propriété :");
			}
		}

        $this->widgetSchema->setNameFormat('vrac_validation[%s]');
    }

    public function doUpdateObject($values) {
    	parent::doUpdateObject($values);

		if($this->getObject()->isPapier()) {
			$this->getObject()->signerPapier(Date::getIsoDateFromFrenchDate($values['date_signature']));
		} else {
			$this->getObject()->signerProrietaire();
		}

		$annuaireUpdated = false;
		if(!$this->getObject()->isAcheteurProprietaire() && $this->annuaire && !$this->annuaire->exist($this->getObject()->acheteur_type."/".$this->getObject()->acheteur_identifiant)) {
			$this->annuaire->add($this->getObject()->acheteur_type)->add($this->getObject()->acheteur_identifiant, ($this->getObject()->acheteur->intitule)? $this->getObject()->acheteur->intitule.' '.$this->getObject()->acheteur->raison_sociale
 : $this->getObject()->acheteur->raison_sociale);
			$annuaireUpdated = true;
		}

		if(!$this->getObject()->isVendeurProprietaire() && $this->annuaire && !$this->annuaire->exist($this->getObject()->vendeur_type."/".$this->getObject()->vendeur_identifiant)) {
			$this->annuaire->add($this->getObject()->vendeur_type)->add($this->getObject()->vendeur_identifiant, ($this->getObject()->vendeur->intitule)? $this->getObject()->vendeur->intitule.' '.$this->getObject()->vendeur->raison_sociale
 : $this->getObject()->vendeur->raison_sociale);
			$annuaireUpdated = true;
		}

		if($annuaireUpdated) {
			$this->annuaire->save();
		}

    }
}
