<?php

class VracClient extends acCouchdbClient {

	const VRAC_PREFIXE_ID = 'VRAC-';
	const APP_CONFIGURATION = 'app_configuration_vrac';
	const APP_CONFIGURATION_VRAC_PRODUITS = 'produits_vrac_statiques';
	const APP_CONFIGURATION_BOUTEILLE_PRODUITS = 'produits_bouteille_statiques';
	const APP_CONFIGURATION_ETAPES = 'etapes';
	const NB_MAX_CONTRAT_DB2 = 99999;
	const TYPE_VRAC = 'VRAC';
	const TYPE_BOUTEILLE = 'BOUTEILLE';
	const TYPE_VRAC_LIBELLE = 'Vrac';
	const TYPE_BOUTEILLE_LIBELLE = 'Bouteille';

	protected static $_contrat_types = array(
									self::TYPE_VRAC => self::TYPE_VRAC_LIBELLE,
                                    self::TYPE_BOUTEILLE => self::TYPE_BOUTEILLE_LIBELLE
                                 );

  protected static $_roles = array(
                  Vrac::ROLE_VENDEUR => "Vendeur",
                  Vrac::ROLE_ACHETEUR => "Acheteur",
                  Vrac::ROLE_MANDATAIRE => "Mandataire",
                  );

	protected static $_centilisations = array(
									'75' => '75 cl',
									'50' => '50 cl',
                                    '100' => '100 cl',
									'37.5' => '37,5 cl',
									'150' => '150 cl',
									'18.7' => '18,7 cl',
									'20' => '20 cl',
									'25' => '25 cl',
									'300' => '300 cl',
									'600' => '600 cl'
                                 );

    public static function getInstance()
    {
      return acCouchdbManager::getClient("Vrac");
    }

    public static function getConfigurationProduits($type)
    {
    	if ($type == self::TYPE_BOUTEILLE) {
    		return self::APP_CONFIGURATION_BOUTEILLE_PRODUITS;
    	}
    	return self::APP_CONFIGURATION_VRAC_PRODUITS;
    }

	public static function getContratTypes()
    {
      return self::$_contrat_types;
    }

  public static function getRoles()
    {
      return self::$_roles;
    }

	public static function getCentilisations()
    {
      return self::$_centilisations;
    }

    public static function getLibelleCentilisation($centilisation)
    {
    	$centilisations = self::getCentilisations();
    	$centilisation = ''.$centilisation;
    	return (isset($centilisations[$centilisation]))? $centilisations[$centilisation] : null;
    }

    public static function canBeHaveVrac($tiers)
    {
      if($tiers->type == 'MetteurEnMarche' && $tiers->hasAcheteur()) {

        return false;
      }

      return true;
    }

    public static function getConfig()
    {
    	if ($config = sfConfig::get(self::APP_CONFIGURATION)) {
    		return $config;
    	}
    	throw new sfException('Aucune configuration vrac définie dans l\'app!');
    }

	  public function buildId($numero_contrat) {

        return sprintf(self::VRAC_PREFIXE_ID.'%s', $numero_contrat);
    }

	public function getFirstEtablissement($societe) {
		foreach($societe->getEtablissementsObject() as $etablissement) {
			if($etablissement->hasDroit(Roles::TELEDECLARATION_VRAC_CREATION)) {
				return $etablissement;
			}
		}

		return null;
	}

	public function getEtablissements($societe) {

		return $societe->getEtablissementsObject(false);
	}

    protected function getDate($date = null)
    {
    	return ($date)? $date : date('Y-m-d');
    }

    protected function getCampagne($campagne = null)
    {
    	return ($campagne)? $campagne : ConfigurationClient::getInstance()->buildCampagneVrac($this->getDate());
    }

    public function getNumeroContratSuivant($date = null)
    {
    	$date = $this->getDate($date);
        $date = date('Ymd', strtotime($date));
        $contrats = $this->getAtDate($date, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $id = sprintf('%s%03d', $date, 1);
        if (count($contrats) > 0) {
        	$suffixe = substr(max($contrats), -3) + 1;
            $id = sprintf('%s%03d', $date, $suffixe);
        }
        return $id;
    }

    public function getNumeroSuivant()
    {
        $contrats = VracTousView::getInstance()->findAll();
        $numeroSuivant = count($contrats) + 1;
        if ($numeroSuivant > self::NB_MAX_CONTRAT_DB2) {
        	throw new sfException('ATTENTION : Limite de contrat possible pour db2 atteinte (plus aucun contrat ne peut être créé).');
        }
        return sprintf('%05d', $numeroSuivant);
    }

    public function getAtDate($date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(self::VRAC_PREFIXE_ID.$date.'000')->endkey(self::VRAC_PREFIXE_ID.$date.'999')->execute($hydrate);
    }

	public function buildCampagneVrac($date) {
      $campagne_manager = new CampagneManager('12-01');

      return $campagne_manager->getCampagneByDate($date);
    }

	public function isSoussigneInscrit($tiers) {
		foreach($tiers->getSociete()->getContactsObj() as $compte) {
			if(!$compte->isActif() || !$compte->hasDroit(Roles::TELEDECLARATION_VRAC) || !$compte->isInscrit()) {
				continue;
			}

			return true;
		}

		return false;
	}

    public function createVrac($createurIdentifiant, $date = null)
    {
    	$date = $this->getDate($date);
    	$config = self::getConfig();
        $campagne = $this->buildCampagneVrac($date);
        $numeroContrat = $this->getNumeroContratSuivant($date);
        $vrac = new Vrac();
        $vrac->initVrac($config, $createurIdentifiant, $numeroContrat, $date, $campagne);
        return $vrac;
    }

    public function findByNumeroContrat($numeroContrat)
    {
      return $this->find($this->getId($numeroContrat));
    }

		public function findByNumContrat($numeroContrat){
			return $this->findByNumeroContrat($numeroContrat);
		}


    public function getId($numeroContrat)
    {
      return self::VRAC_PREFIXE_ID.$numeroContrat;
    }


  	public function getStatutLibelle($statut)
  	{
  		$libelles = Vrac::getStatutsLibelles();
  		return $libelles[$statut];
  	}

  	public function getStatutLibelleAction($statut, $proprietaire = false, $hasValidated = false)
  	{
  		$libelles = Vrac::getStatutsLibellesActions();
  		if ($proprietaire) {
  			$libelles = Vrac::getStatutsLibellesActionsProprietaire();
  		}
  		if ($statut == Vrac::STATUT_VALIDE_PARTIELLEMENT && $hasValidated) {
  			return $libelles[Vrac::STATUT_CLOTURE];
  		}
  		return ($statut)? $libelles[$statut] : $libelles[VRAC::STATUT_CREE];
  	}

}
