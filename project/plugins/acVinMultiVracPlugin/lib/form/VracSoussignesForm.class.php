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
        	'acheteur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'vendeur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
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
        	'acheteur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'vendeur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'acheteur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'acheteur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'acheteur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'vendeur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($recoltantChoices))),
        	'vendeur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($negociantChoices))),
        	'vendeur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($caveCooperativeChoices))),
        	'interlocuteur_commercial' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($commerciauxChoices)))
        ));

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
		}

        if (!$this->getObject()->isNew()) {
        	unset($this['type_contrat']);
        }
        $this->validatorSchema->setPostValidator(new VracSoussignesValidator($this->getObject()));
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');
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
    	if ($this->getObject()->isNew()) {
    		$this->getObject()->type_contrat = $values['type_contrat'];
            $this->getObject()->type_archive = null;
            $this->getObject()->getTypeArchive();
    		$this->getObject()->initProduits();
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
