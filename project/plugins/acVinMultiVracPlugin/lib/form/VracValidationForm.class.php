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
		}

        $this->widgetSchema->setNameFormat('vrac_validation[%s]');
    }

    public function doUpdateObject($values) {
    	parent::doUpdateObject($values);
        foreach ($this->getEmbeddedForms() as $key => $embedForm) {
            $embedForm->doUpdateObject($values[$key]);
        }

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
