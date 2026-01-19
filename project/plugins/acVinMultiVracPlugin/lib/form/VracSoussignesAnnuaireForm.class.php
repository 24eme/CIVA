<?php
class VracSoussignesAnnuaireForm extends VracSoussignesForm
{
	public function configure()
    {
    	$this->disableCSRFProtection();
    	$types = $this->getTypes();
    	$contratTypes = $this->getContratTypes();
    	$recoltantChoices = $this->getRecoltants();
    	$negociantChoices = $this->getNegociants();
    	$caveCooperativeChoices = $this->getCavesCooperatives();
    	$commerciauxChoices = $this->getCommerciaux();
        $this->setWidgets(array(
            'type_contrat' => new sfWidgetFormChoice(array('choices' => $contratTypes, 'expanded' => true)),
            'contrat_pluriannuel' => new sfWidgetFormChoice(array('choices' => ["Contrat annuel", "Contrat pluriannuel"], 'expanded' => true)),
        	'acheteur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
            'acheteur_assujetti_tva' => new sfWidgetFormChoice(array('choices' => ["Non", "Oui"], 'expanded' => true)),
        	'vendeur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
            'vendeur_assujetti_tva' => new sfWidgetFormChoice(array('choices' => ["Non", "Oui"], 'expanded' => true)),
        	'acheteur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'interlocuteur_commercial' => new sfWidgetFormChoice(array('choices' => array_merge($commerciauxChoices, array('add' => 'Ajouter un contact'))))
    	));
        $this->setValidators(array(
        	'type_contrat' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($contratTypes))),
            'contrat_pluriannuel' => new sfValidatorChoice(array('choices' => [0,1])),
        	'acheteur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
            'acheteur_assujetti_tva' => new sfValidatorChoice(array('required' => false, 'choices' => [0,1])),
        	'vendeur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
            'vendeur_assujetti_tva' => new sfValidatorChoice(array('required' => false, 'choices' => [0,1])),
        	'acheteur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($recoltantChoices, array('add' => ''))))),
        	'acheteur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($negociantChoices, array('add' => ''))))),
        	'acheteur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($caveCooperativeChoices, array('add' => ''))))),
        	'vendeur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($recoltantChoices, array('add' => ''))))),
        	'vendeur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($negociantChoices, array('add' => ''))))),
        	'vendeur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($caveCooperativeChoices, array('add' => ''))))),
        	'interlocuteur_commercial' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($commerciauxChoices, array('add' => '')))))
        ));
        if (!$this->getObject()->isNew()) {
        	unset($this['type_contrat'], $this['contrat_pluriannuel']);
        }

        $this->setWidget('contrat_pluriannuel_mode_surface', new sfWidgetFormChoice(array('choices' => ["Du volume (hl)", "De la surface (ares)"], 'expanded' => true)));
        $this->setValidator('contrat_pluriannuel_mode_surface', new sfValidatorChoice(array('choices' => [0,1], 'required' => false)));
        $this->getWidgetSchema()->setLabel('contrat_pluriannuel_mode_surface', "Vous contractualisez sur :");

        $campagnes = self::getCampagnesChoices();
        $this->setWidget('campagne', new sfWidgetFormChoice(array('choices' => $campagnes)));
        $this->setValidator('campagne', new sfValidatorChoice(array('choices' => array_keys($campagnes), 'required' => false)));
        $this->getWidgetSchema()->setLabel('campagne', "Campagnes d'application :");

        $unites = VracClient::$prix_unites;
        $this->setWidget('prix_unite', new sfWidgetFormChoice(array('choices' => $unites)));
        $this->setValidator('prix_unite', new sfValidatorChoice(array('choices' => array_keys($unites), 'required' => false)));
        $this->getWidgetSchema()->setLabel('prix_unite', "Unité de prix :");

        $this->setWidget('duree_annee', new sfWidgetFormChoice(array('choices' => $this->getDureeContratCurrentMillesime())));
        $this->setWidget('duree_annee_select', new sfWidgetFormInputHidden());

        $this->getWidget('duree_annee')->setLabel('Pour une durée de');

        $this->setValidator('duree_annee', new ValidatorVracChoices(array('required' => false, 'choices' => array_keys($this->getDureeContratCurrentMillesime()))));
        $this->setValidator('duree_annee_select', new sfValidatorString(array('required' => false)));

        $this->validatorSchema->setPostValidator(new VracSoussignesAnnuaireValidator($this->getObject()));
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');
    }

    protected function doUpdateObject($values) 
    {
    	$acheteur = $values['acheteur'];
    	$vendeur = $values['vendeur'];
    	if ($values['interlocuteur_commercial']) {
    		if ($this->annuaire->commerciaux->exist($values['interlocuteur_commercial'])) {
	    		$this->getObject()->storeInterlocuteurCommercialInformations(
                                        $values['interlocuteur_commercial'],
                                        $this->annuaire->commerciaux->get($values['interlocuteur_commercial']));
    		}
    	}
    	$this->getObject()->acheteur_type = $values['acheteur_type'];
    	$this->getObject()->vendeur_type = $values['vendeur_type'];
    	if ($acheteur) {
    		$this->getObject()->acheteur_identifiant = $acheteur->_id;
    		$this->getObject()->storeAcheteurInformations($acheteur);
    	}
    	if ($vendeur) {
	    	$this->getObject()->vendeur_identifiant = $vendeur->_id;
	    	$this->getObject()->storeVendeurInformations($vendeur);
    	}
        if(isset($values['acheteur_assujetti_tva']) && $values['acheteur_assujetti_tva'] !== null) {
            $this->getObject()->acheteur_assujetti_tva = (isset($values['acheteur_assujetti_tva']) && $values['acheteur_assujetti_tva'])? 1 : 0;
        }
        if(isset($values['vendeur_assujetti_tva']) && $values['vendeur_assujetti_tva'] !== null) {
            $this->getObject()->vendeur_assujetti_tva = (isset($values['vendeur_assujetti_tva']) && $values['vendeur_assujetti_tva'])? 1 : 0;
        }
    	if ($this->getObject()->isNew()) {
    		$this->getObject()->type_contrat = $values['type_contrat'];
            $this->getObject()->contrat_pluriannuel = (isset($values['contrat_pluriannuel']) && $values['contrat_pluriannuel'])? 1 : 0;
    	}

        $this->getObject()->contrat_pluriannuel_mode_surface = (isset($values['contrat_pluriannuel_mode_surface']) && $values['contrat_pluriannuel_mode_surface'] == 1)? 1 : 0;

        if (!$this->getObject()->contrat_pluriannuel) {
            $this->getObject()->contrat_pluriannuel_mode_surface = 0;
        } else {
            if ($values['campagne']) {
                $this->getObject()->campagne = $values['campagne'];
            }
        }
		if ($values['prix_unite']) {
			$this->getObject()->prix_unite = $values['prix_unite'];
		}
    }

    protected function getContratTypes()
    {
    	return VracClient::getContratTypes();
    }

	public function getUpdatedVrac()
  	{
  		$this->doUpdateObject($this->getValues());
    	return $this->getObject();
  	}
}
