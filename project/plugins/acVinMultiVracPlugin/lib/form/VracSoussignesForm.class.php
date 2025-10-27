<?php
class VracSoussignesForm extends acCouchdbObjectForm
{
	protected $annuaire;

	public function __construct(acCouchdbJson $object, $annuaire = null, $options = array(), $CSRFSecret = null)
	{
		$this->annuaire = $annuaire;
        parent::__construct($object, $options, $CSRFSecret);
    }
	public function configure()
    {
    	$contratTypes = $this->getContratTypes();
    	$types = $this->getTypes();
    	$recoltantChoices = $this->getRecoltants();
    	$negociantChoices = $this->getNegociants();
    	$caveCooperativeChoices = $this->getCavesCooperatives();
    	$commerciauxChoices = $this->getCommerciaux();
        $this->setWidgets(array(
            'type_contrat' => new sfWidgetFormChoice(array('choices' => $contratTypes, 'expanded' => true)),
			'contrat_pluriannuel' => new sfWidgetFormChoice(array('choices' => ["Annuel", "Pluriannuel"], 'expanded' => true)),
        	'acheteur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
            'acheteur_assujetti_tva' => new sfWidgetFormChoice(array('choices' => ["1" => "Oui", "0" => "Non"], 'expanded' => true)),
        	'vendeur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
            'vendeur_assujetti_tva' => new sfWidgetFormChoice(array('choices' => ["1" => "Oui", "0" => "Non"], 'expanded' => true)),
        	'acheteur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'interlocuteur_commercial' => new sfWidgetFormChoice(array('choices' => array_merge($commerciauxChoices, array('add' => 'Ajouter un contact'))))
    	));
        $this->setValidators(array(
        	'type_contrat' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($contratTypes))),
            'contrat_pluriannuel' => new sfValidatorChoice(array('choices' => [0,1])),
        	'acheteur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
            'acheteur_assujetti_tva' => new sfValidatorChoice(array('choices' => [1,0], 'required' => false)),
        	'vendeur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
            'vendeur_assujetti_tva' => new sfValidatorChoice(array('choices' => [1,0], 'required' => false)),
        	'acheteur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'acheteur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'acheteur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'vendeur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'vendeur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'vendeur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'interlocuteur_commercial' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($commerciauxChoices)))
        ));

		if($this->getObject()->mandataire_identifiant != $this->getObject()->createur_identifiant) {
            unset($this->widgetSchema['interlocuteur_commercial']);
            unset($this->validatorSchema['interlocuteur_commercial']);
        }

		if($this->getObject()->isPapier()) {
			$this->setWidget('vendeur_recoltant_identifiant', new WidgetEtablissementSelect(array('interpro_id' => 'INTERPRO-declaration', 'familles' => array(EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))));
			$this->setValidator('vendeur_recoltant_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => array(EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))));
			$this->setWidget('vendeur_negociant_identifiant', new WidgetEtablissementSelect(array('interpro_id' => 'INTERPRO-declaration', 'familles' => array(EtablissementFamilles::FAMILLE_NEGOCIANT))));
			$this->setValidator('vendeur_negociant_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => array(EtablissementFamilles::FAMILLE_NEGOCIANT))));
			$this->setWidget('vendeur_cave_cooperative_identifiant', new WidgetEtablissementSelect(array('interpro_id' => 'INTERPRO-declaration', 'familles' => array(EtablissementFamilles::FAMILLE_COOPERATIVE))));
			$this->setValidator('vendeur_cave_cooperative_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => array(EtablissementFamilles::FAMILLE_COOPERATIVE))));

			$this->setWidget('acheteur_recoltant_identifiant', new WidgetEtablissementSelect(array('interpro_id' => 'INTERPRO-declaration', 'familles' => array(EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))));
			$this->setValidator('acheteur_recoltant_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => array(EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))));
			$this->setWidget('acheteur_negociant_identifiant', new WidgetEtablissementSelect(array('interpro_id' => 'INTERPRO-declaration', 'familles' => array(EtablissementFamilles::FAMILLE_NEGOCIANT))));
			$this->setValidator('acheteur_negociant_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => array(EtablissementFamilles::FAMILLE_NEGOCIANT))));
			$this->setWidget('acheteur_cave_cooperative_identifiant', new WidgetEtablissementSelect(array('interpro_id' => 'INTERPRO-declaration', 'familles' => array(EtablissementFamilles::FAMILLE_COOPERATIVE))));
			$this->setValidator('acheteur_cave_cooperative_identifiant', new ValidatorEtablissement(array('required' => false, 'familles' => array(EtablissementFamilles::FAMILLE_COOPERATIVE))));

			unset($this->widgetSchema['interlocuteur_commercial']);
			unset($this->validatorSchema['interlocuteur_commercial']);
		}

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

        $this->validatorSchema->setPostValidator(new VracSoussignesValidator($this->getObject()));
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');

        $this->setWidget('pluriannuel_campagne_debut', new sfWidgetFormChoice(array('choices' => $this->getCampagnesChoices())));
        $this->setWidget('pluriannuel_contrat_duree', new sfWidgetFormChoice(array('choices' => $this->getDureeContratCurrentMillesime())));
        $this->setWidget('pluriannuel_contrat_duree_select', new sfWidgetFormInputHidden());

        $this->getWidget('pluriannuel_campagne_debut')->setLabel('Conclu à partir de la campagne');
        $this->getWidget('pluriannuel_contrat_duree')->setLabel('Pour une durée de');

        $this->setValidator('pluriannuel_campagne_debut', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getCampagnesChoices()))));
        $this->setValidator('pluriannuel_contrat_duree', new ValidatorVracChoices(array('required' => false, 'choices' => array_keys($this->getDureeContratCurrentMillesime()))));
        $this->setValidator('pluriannuel_contrat_duree_select', new sfValidatorString(array('required' => false)));

    }

    public static function getCurrentCampagne() {
        $campagne_manager = new CampagneManager('12-01');
        return $campagne_manager->getCampagneByDate(date('Y-m-d'));
    }

    public static function getDureeContratCurrentMillesime() {
        list($millesime, $campagnes) = self::getCurrentMillesime();
        $nbAnnee = VracClient::getConfigVar('nb_campagnes_pluriannuel');
        for($i=$millesime+$nbAnnee; $i<=$millesime+10; $i++) {
            $campagnes[$millesime . '-' . $i] =  $nbAnnee++ . ' ans ' . '(' . $millesime . ' à ' . $i . ')';
        }
        return $campagnes;
    }

    public static function getDureeContratNextMillesime() {
        list($millesime, $campagnes) = self::getCurrentMillesime();
        $nbAnnee = VracClient::getConfigVar('nb_campagnes_pluriannuel');
        $millesime++;
        for($i=$millesime+$nbAnnee; $i<=$millesime+10; $i++) {
            $campagnes[$millesime . '-' . $i] =  $nbAnnee++ . ' ans ' . '(' . $millesime . ' à ' . $i . ')';
        }
        return $campagnes;
    }

    public static function getCampagnesChoices() {
        list($millesime, $campagnes) = self::getCurrentMillesime();

        for($i=$millesime; $i<=$millesime+1; $i++) {
            $campagnes[$i.'-'.($i+1)] = $i.'-'.(($i+VracClient::getConfigVar('nb_campagnes_pluriannuel',0) -2));
        }
        return $campagnes;
    }

    public static function getCurrentMillesime() {
        $campagne = self::getCurrentCampagne();
        $millesime = substr($campagne, 0, 4) * 1;
		if (date('m') == 12||date('Y') > $millesime) {
			$millesime++;
		}
		$campagnes = [];
        return [$millesime, $campagnes];
    }
	protected function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $defaults = $this->getDefaults();
        if ($this->getObject()->acheteur_identifiant) {
        	$defaults['acheteur_'.str_replace('s', '', $this->getObject()->acheteur_type).'_identifiant'] = $this->getObject()->acheteur_identifiant;
        }
        if ($this->getObject()->vendeur_identifiant) {
        	$defaults['vendeur_'.str_replace('s', '', $this->getObject()->vendeur_type).'_identifiant'] = $this->getObject()->vendeur_identifiant;
        }
        if ($this->getObject()->isNew() && !$this->getObject()->type_contrat) {
        	$defaults['type_contrat'] = VracClient::TYPE_VRAC;
        }
        if ($this->getObject()->isNew()) {
        	$defaults['contrat_pluriannuel'] = 0;
            $defaults['campagne'] = self::getCurrentCampagne();
            $defaults['contrat_pluriannuel_mode_surface'] = 0;
        }
        $this->setDefaults($defaults);
    }

    protected function doUpdateObject($values)
    {
    	$acheteur = $values['acheteur'];
    	$vendeur = $values['vendeur'];
    	if ($values['interlocuteur_commercial']) {
            $this->getObject()->storeInterlocuteurCommercialInformations(
                                        $values['interlocuteur_commercial'],
                                        $this->annuaire->commerciaux->get($values['interlocuteur_commercial']));
    	} else {
    		$this->getObject()->remove('interlocuteur_commercial');
    		$this->getObject()->add('interlocuteur_commercial');
    	}
		if($values['acheteur_type']) {
    		$this->getObject()->acheteur_type = $values['acheteur_type'];
		}
		if($values['vendeur_type']) {
    		$this->getObject()->vendeur_type = $values['vendeur_type'];
		}
    	$this->getObject()->acheteur_identifiant = $acheteur->_id;
    	$this->getObject()->vendeur_identifiant = $vendeur->_id;
    	$this->getObject()->storeAcheteurInformations($acheteur);
    	$this->getObject()->storeVendeurInformations($vendeur);
        $this->getObject()->acheteur_assujetti_tva = (isset($values['acheteur_assujetti_tva']) && $values['acheteur_assujetti_tva'])? 1 : 0;
        $this->getObject()->vendeur_assujetti_tva = (isset($values['vendeur_assujetti_tva']) && $values['vendeur_assujetti_tva'])? 1 : 0;
        $this->getObject()->contrat_pluriannuel_mode_surface = (isset($values['contrat_pluriannuel_mode_surface']) && $values['contrat_pluriannuel_mode_surface'] == 1)? 1 : 0;

        if ($this->getObject()->isNew()) {
    		$this->getObject()->type_contrat = $values['type_contrat'];
            $this->getObject()->contrat_pluriannuel = (isset($values['contrat_pluriannuel']) && $values['contrat_pluriannuel'])? 1 : 0;
            $this->getObject()->type_archive = null;
            $this->getObject()->getTypeArchive();
    		$this->getObject()->initProduits();
    	}

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

    protected function getTypes()
    {
    	return AnnuaireClient::getAnnuaireTypes();
    }

    public function getAnnuaire()
    {
    	return $this->annuaire;
    }

    public function getRecoltants()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$result = array();
    	foreach ($annuaire->recoltants as $key => $value) {
			if(!preg_match("/ETABLISSEMENT/", $key)) {
				continue;
			}
    		$num = explode('-', $key);
    		$result[$key] = $value." (".$num[1].")";
    	}
    	return array_merge(array('' => ''), $result);
    }

    public function getNegociants()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$result = array();
    	foreach ($annuaire->negociants as $key => $value) {
			if(!preg_match("/ETABLISSEMENT/", $key)) {
				continue;
			}
    		$num = explode('-', $key);
    		$result[$key] = $value." (".$num[1].")";
    	}
    	return array_merge(array('' => ''), $result);
    }

    public function getCavesCooperatives()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$result = array();
    	foreach ($annuaire->caves_cooperatives as $key => $value) {
			if(!preg_match("/ETABLISSEMENT/", $key)) {
				continue;
			}
    		$num = explode('-', $key);
    		$result[$key] = $value." (".$num[1].")";
    	}
    	return array_merge(array('' => ''), $result);
    }

    public function getCommerciaux()
    {
    	$annuaire = $this->getAnnuaire();
    	if (!$annuaire) {
    		return array();
    	}
    	$commerciaux = $annuaire->commerciaux->toArray();
    	$choices = array();
    	foreach ($commerciaux as $key => $commercial) {
    		$choices[$key] = $key;
    	}
    	return array_merge(array('' => ''), $choices);
    }
}
