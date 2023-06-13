<?php
/**
 * Model for Vrac
 *
 */

class Vrac extends BaseVrac implements InterfaceArchivageDocument
{

	const STATUT_CREE = 'CREE';
	const STATUT_PROJET_VENDEUR = 'PROJET_VENDEUR';
	const STATUT_PROJET_ACHETEUR = 'PROJET_ACHETEUR';
	const STATUT_REFUS_PROJET = 'REFUS_PROJET';
	const STATUT_PROPOSITION = 'PROPOSITION';
	const STATUT_SIGNE = 'SIGNE';
	const STATUT_VALIDE_PARTIELLEMENT = 'VALIDE_PARTIELLEMENT';
	const STATUT_VALIDE = 'VALIDE';
	const STATUT_VALIDE_CADRE = 'VALIDE_CADRE';
	const STATUT_ANNULE = 'ANNULE';
	const STATUT_ENLEVEMENT = 'ENLEVEMENT';
	const STATUT_CLOTURE = 'CLOTURE';
	const PREFIXE_NUMERO = 8;
	const CEPAGE_EDEL = "cepage_ED";
	const CEPAGE_EDEL_LIBELLE_COMPLEMENT = " (Edel)";
	const CEPAGE_MUSCAT = "cepage_MU";
	const CEPAGE_MUSCAT_LIBELLE = "Muscat";
    const CAMPAGNE_ARCHIVE = 'UNIQUE';
    const APPELLATION_PINOTNOIRROUGE = "PINOTNOIRROUGE";
	const APPELLATION_CREMANT = "CREMANT";
    const CEPAGE_PR = "cepage_PR";
	const CEPAGE_PR_LIBELLE_COMPLEMENT = " (rouge)";

    const ROLE_VENDEUR = 'vendeur';
    const ROLE_ACHETEUR = 'acheteur';
    const ROLE_MANDATAIRE = 'mandataire';

    const VENDEUR_PROJET_FILENAME = 'projet_vendeur.json';

	protected $_config;
	protected $archivage_document;
    protected $diff_with_mother = null;

	static $statuts_libelles = array(
		self::STATUT_CREE => 'Créé',
		self::STATUT_PROJET_VENDEUR => 'Projet',
		self::STATUT_PROJET_ACHETEUR => 'Projet',
		self::STATUT_PROPOSITION => 'Proposition',
		self::STATUT_VALIDE_PARTIELLEMENT => 'En attente de validation',
		self::STATUT_VALIDE => 'Validé',
		self::STATUT_VALIDE_CADRE => 'Validé',
		self::STATUT_ANNULE => 'Annulé',
		self::STATUT_ENLEVEMENT => 'En cours d\'enlèvement',
		self::STATUT_CLOTURE => 'Cloturé'
	);

	static $statuts_libelles_actions = array(
		self::STATUT_CREE => 'Continuer',
		self::STATUT_PROJET_VENDEUR => 'Visualiser',
		self::STATUT_PROJET_ACHETEUR => 'Signer le projet',
		self::STATUT_PROPOSITION => 'Visualiser pour signer',
		self::STATUT_VALIDE_PARTIELLEMENT => 'Visualiser pour signer',
		self::STATUT_VALIDE => 'Visualiser',
		self::STATUT_VALIDE_CADRE => 'Visualiser',
		self::STATUT_ANNULE => 'Visualiser',
		self::STATUT_ENLEVEMENT => 'Visualiser',
		self::STATUT_CLOTURE => 'Visualiser'
	);

	static $statuts_libelles_actions_proprietaire = array(
		self::STATUT_CREE => 'Continuer',
		self::STATUT_PROJET_VENDEUR => 'Valider le projet',
		self::STATUT_PROJET_ACHETEUR => 'Visualiser',
		self::STATUT_PROPOSITION => 'Visualiser pour signer',
		self::STATUT_VALIDE_PARTIELLEMENT => 'Visualiser',
		self::STATUT_VALIDE => 'Enlever',
		self::STATUT_VALIDE_CADRE => 'Gérer',
		self::STATUT_ENLEVEMENT => 'Enlever',
	);

    static $statuts_libelles_historique = array(
		self::STATUT_CREE => "Projet de contrat initié",
		self::STATUT_PROJET_VENDEUR => "Projet soumis à l'acheteur ou au courtier pour validation",
		self::STATUT_PROJET_ACHETEUR => "Projet de contrat validé et soumis au vendeur pour signature",
		self::STATUT_REFUS_PROJET => "Projet de contrat refusé",
		self::STATUT_SIGNE => "Signature",
		self::STATUT_PROPOSITION => "Proposition de contrat soumise aux autres soussignés pour signature",
		self::STATUT_VALIDE_PARTIELLEMENT => null,
		self::STATUT_VALIDE => "Contrat de vente visé",
		self::STATUT_VALIDE_CADRE => "Contrat de vente pluriannuel visé",
		self::STATUT_ANNULE => "Contrat annulé",
		self::STATUT_ENLEVEMENT => null,
		self::STATUT_CLOTURE => "Contrat clôturé",
	);

    static $statuts_template_historique = array(
        "Projet de contrat initié" => self::STATUT_CREE,
        "Projet soumis à l'acheteur ou au courtier pour validation (isVendeurProprietaire)" => self::STATUT_PROJET_VENDEUR,
        "Projet de contrat validé et soumis au vendeur pour signature" => self::STATUT_PROJET_ACHETEUR,
        "Proposition de contrat soumise aux autres soussignés pour signature" => self::STATUT_PROPOSITION,
        "Signature des soussignés" => self::STATUT_SIGNE,
        "Contrat de vente visé" => self::STATUT_VALIDE,
        "Contrat clôturé" => self::STATUT_CLOTURE
    );

	static $statuts_supprimable = array(
		self::STATUT_CREE,
		self::STATUT_PROJET_VENDEUR,
		self::STATUT_PROJET_ACHETEUR,
		self::STATUT_PROPOSITION,
		self::STATUT_VALIDE_PARTIELLEMENT,
		self::STATUT_VALIDE,
		self::STATUT_VALIDE_CADRE,
	);

	static $types_tiers = array(
		self::ROLE_VENDEUR,
		self::ROLE_ACHETEUR,
		self::ROLE_MANDATAIRE,
	);

	public static $cepages_exclus_cremant = array(
		'RB',
		'BL',
		'RS',
	);

  	public static function getStatutsLibellesActions()
  	{
  		return self::$statuts_libelles_actions;
  	}

  	public static function getStatutsLibellesActionsProprietaire()
  	{
  		return array_merge(self::$statuts_libelles_actions, self::$statuts_libelles_actions_proprietaire);
  	}

  	public static function getStatutsLibelles()
  	{
  		return self::$statuts_libelles;
  	}

  	public static function getStatutsSupprimable()
  	{
  		return self::$statuts_supprimable;
  	}

  	public static function getTypesTiers()
  	{
  		return self::$types_tiers;
  	}

    public function  __construct() {
        parent::__construct();
        $this->setArchivageDocument();
    }

    public function setArchivageDocument() {
        $this->archivage_document = new ArchivageDocument($this);
    }

  	public function getStatutLibelle()
  	{
  		$libelles = self::getStatutsLibelles();
  		return $libelles[$this->valide->statut];
  	}

  	public function getStatutLibelleAction($proprietaire = false, $hasValidated = false)
  	{
  		$libelles = self::getStatutsLibellesActions();
  		if ($proprietaire) {
  			$libelles = self::getStatutsLibellesActionsProprietaire();
  		}
  		if ($this->valide->statut == self::STATUT_VALIDE_PARTIELLEMENT && $hasValidated) {
  			return $libelles[self::STATUT_CLOTURE];
  		}
  		return ($this->valide->statut)? $libelles[$this->valide->statut] : $libelles[self::STATUT_CREE];
  	}

    public function initVrac($config, $createurIdentifiant, $numeroContrat, $date, $campagne)
    {
        $this->_config = $config;
        $this->campagne = $campagne;
        $this->campagne_archive = self::CAMPAGNE_ARCHIVE;
        $this->type_archive = $this->getTypeForArchive();
        $this->numero_contrat = $numeroContrat;
        $this->valide->date_saisie = $date;
        $this->setStatut(self::STATUT_CREE, $createurIdentifiant);
        $this->acheteur_type = AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
        $this->vendeur_type = AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
        $this->createur_identifiant = $createurIdentifiant;
    }

    public function getCampagneArchive() {
        if(!$this->_get('campagne_archive')) {
            $this->_set('campagne_archive', self::CAMPAGNE_ARCHIVE);
        }
        return $this->_get('campagne_archive');
    }

    public function getTypeArchive() {
        if(!$this->_get('type_archive')) {
            $this->_set('type_archive', $this->getTypeForArchive());
        }
        return $this->_get('type_archive');
    }

    public function getTypeForArchive() {
    	return ucfirst(strtolower($this->type_contrat));
    }

    public function constructId()
    {
        $this->set('_id', VracClient::getInstance()->buildId($this->numero_contrat));
    }

    public function getNumeroArchive()
    {
    	return $this->_get('numero_archive');
    }

	public function getConfiguration()
	{
        $campagne = substr($this->campagne,0,4);

        $conf_2012 = ConfigurationClient::getConfiguration('2012');
        if($campagne > '2012'){
            return $conf_2012;
        }
        $conf = acCouchdbManager::getClient('Configuration')->retrieveConfiguration($campagne);
        return $conf;
     }

    public function initProduits()
    {
        if($this->type_contrat == VracClient::TYPE_RAISIN && $dr = DRClient::getInstance()->findLastByCvi($this->vendeur->cvi)) {
            $i = 1;
            foreach ($dr->getProduits() as $cepage) {
                if($cepage->getAppellation()->getKey() == "appellation_CREMANT" && strpos($cepage->getCepage()->getKey(), "cepage_RB") !== false) {
                    continue;
                }
                $this->addDetail($cepage->getHash(), array('supprimable' => 0, 'position' => $i));
                $i++;
            }
        }

        if(count($this->declaration->getProduitsDetailsSorted())) {
            return;
        }

    	if (isset($this->_config[VracClient::getConfigurationProduits($this->type_contrat)])) {
    		$produits = $this->_config[VracClient::getConfigurationProduits($this->type_contrat)];
    		foreach ($produits as $produit_hash => $produit_config) {
    			$this->addDetail($produit_hash, $produit_config);
    		}
    	}
    }

	public function addProduit($hash)
	{
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $hash = preg_replace("/(mentionVT|mentionSGN)/", "mention", $hash);
        $produit = $this->getOrAdd($hash);
        $config = $produit->getConfig();
        $produit->libelle = $config->getLibelle();
        if (($produit->getAppellation()->getKey() == 'appellation_'.self::APPELLATION_PINOTNOIRROUGE) &&  ($produit->getKey() == self::CEPAGE_PR)) {
        	$produit->libelle .= self::CEPAGE_PR_LIBELLE_COMPLEMENT;
        }
        if ($produit->getKey() == self::CEPAGE_EDEL) {
        	$produit->libelle .= self::CEPAGE_EDEL_LIBELLE_COMPLEMENT;
        }
        if ($produit->getKey() == self::CEPAGE_MUSCAT) {
        	$produit->libelle = self::CEPAGE_MUSCAT_LIBELLE;
        }
        $produit->getCouleur()->libelle = $produit->getConfig()->getCouleur()->libelle;
        $produit->getLieu()->libelle = $produit->getConfig()->getLieu()->libelle;
        $produit->getMention()->libelle = $produit->getConfig()->getMention()->libelle;
        $produit->getAppellation()->libelle = $produit->getConfig()->getAppellation()->libelle;
        $produit->no_vtsgn = (int) !$config->hasVtsgn();
        return $produit;
    }

    public function addDetail($hash, $config = null)
    {
        $produit = $this->addProduit($hash);
        if(!$produit) {
            return null;
        }
        $detail = $produit->addDetail($config);
        $complement = null;
        if (strpos($hash, 'mentionVT')) $complement = 'VT';
        if (strpos($hash, 'mentionSGN')) $complement = 'SGN';
        $detail->denomination = $complement;
        return $detail;
    }

    public function addDynamiqueDetail($hash, $lieuDit = null, $vtsgn = null, $config = null)
    {
        $detail = $this->addDetail($hash, $config);
        $detail->lieu_dit = $lieuDit;
        $detail->vtsgn = $vtsgn;
        $detail->supprimable = 1;
        $detail->position = ($this->declaration->getPositionNouveauProduitDetail() - 1);
    }

    public function addActeur($type, $tiers)
    {
    	$noeud = $type.'_identifiant';
    	if ($this->exist($noeud)) {
    		$this->{$noeud} = $tiers->_id;
    		call_user_func(array('Vrac', 'store'.ucfirst($type).'Informations'), $tiers);
    	}
    }

    public function addType($acteur, $type)
    {
    	$noeud = $acteur.'_type';
    	if ($this->exist($noeud)) {
    		$this->{$noeud} = $type;
    	}
    }

    public function storeAcheteurInformations($tiers)
    {
    	$compte = $tiers->getContact();

        /*if((!$tiers->getNumInterne() || !$tiers->no_accises) && $compte) {
            if ($metteurEnMarche = $compte->getTiersType('MetteurEnMarche')) {
                      $tiers->no_accises = $metteurEnMarche->no_accises;
                      $tiers->civaba = $metteurEnMarche->civaba;
            }
        }*/

    	$this->acheteur->intitule = ($tiers->exist("intitule"))? $tiers->intitule : null;
    	$this->acheteur->raison_sociale = $tiers->nom;
    	$this->acheteur->siret = $tiers->siret;
    	$this->acheteur->cvi = $tiers->cvi;
    	$this->acheteur->num_accise = $tiers->no_accises;
    	$this->acheteur->civaba = $tiers->getNumInterne();
    	$this->acheteur->adresse = $tiers->siege->adresse;
    	$this->acheteur->code_postal = $tiers->siege->code_postal;
    	$this->acheteur->commune = $tiers->siege->commune;
    	$this->acheteur->telephone = $tiers->telephone;
    	$this->acheteur->famille = $tiers->getFamille();
    	$this->acheteur->identifiant = $tiers->_id;

        $this->acheteur->remove('emails');
        $this->acheteur->add('emails');

		$emails = array();
		foreach($tiers->getSociete()->getContactsObj() as $compte) {
        	if(!$compte->getEmail() || !$compte->mot_de_passe || !$compte->isActif() || !$compte->hasDroit(Roles::TELEDECLARATION_VRAC)) {
				continue;
			}

			$emails[] = $compte->email;
		}

		$emails = array_values(array_unique($emails));

		$this->acheteur->emails = $emails;
    }

    public function storeVendeurInformations($tiers)
    {
    	$compte = $tiers->getContact();

    	$this->vendeur->intitule = ($tiers->exist("intitule"))? $tiers->intitule : null;
    	$this->vendeur->raison_sociale = $tiers->nom;
    	$this->vendeur->siret = $tiers->siret;
    	$this->vendeur->cvi = $tiers->cvi;
    	$this->vendeur->num_accise = $tiers->no_accises;
    	$this->vendeur->civaba = $tiers->getNumInterne();
    	$this->vendeur->adresse = $tiers->siege->adresse;
    	$this->vendeur->code_postal = $tiers->siege->code_postal;
    	$this->vendeur->commune = $tiers->siege->commune;
    	$this->vendeur->telephone = $tiers->telephone;
    	$this->vendeur->famille = $tiers->getFamille();
    	$this->vendeur->identifiant = $tiers->_id;

        $this->vendeur->remove('emails');
        $this->vendeur->add('emails');

		$emails = array();
		foreach($tiers->getSociete()->getContactsObj() as $compte) {
        	if(!$compte->getEmail() || !$compte->mot_de_passe || !$compte->isActif() || !$compte->hasDroit(Roles::TELEDECLARATION_VRAC)) {
				continue;
			}

			$emails[] = $compte->email;
		}

		$emails = array_values(array_unique($emails));

		$this->vendeur->emails = $emails;
    }

    public function storeMandataireInformations($tiers)
    {
    	$this->mandataire->intitule = ($tiers->exist("intitule"))? $tiers->intitule : null;
    	$this->mandataire->nom = $tiers->nom;
    	$this->mandataire->raison_sociale = $tiers->nom;
    	$this->mandataire->siret = $tiers->siret;
    	$this->mandataire->carte_pro = $tiers->carte_pro;
    	$this->mandataire->adresse = $tiers->siege->adresse;
    	$this->mandataire->code_postal = $tiers->siege->code_postal;
    	$this->mandataire->commune = $tiers->siege->commune;
    	$this->mandataire->telephone = $tiers->telephone;
    	$this->mandataire->famille = $tiers->getFamille();
    	$this->mandataire->identifiant = $tiers->_id;
    	$this->mandataire->num_db2 = $tiers->num_interne;

        $this->mandataire->remove('emails');
        $this->mandataire->add('emails');

		$emails = array();
		foreach($tiers->getSociete()->getContactsObj() as $compte) {
        	if(!$compte->getEmail() || !$compte->mot_de_passe || !$compte->isActif() || !$compte->hasDroit(Roles::TELEDECLARATION_VRAC)) {
				continue;
			}

			$emails[] = $compte->email;
		}

		$emails = array_values(array_unique($emails));

		$this->mandataire->emails = $emails;
    }

    public function storeInterlocuteurCommercialInformations($nom, $contact) {
        $email = trim(preg_replace("/\([0-9]+\)/", "", $contact));

        $telephone = null;
        if(preg_match("/\(([0-9]+)\)/", $contact, $matches)) {
            $telephone = $matches[1];
        }

        $this->interlocuteur_commercial->nom = $nom;
        $this->interlocuteur_commercial->email = ($email) ? $email : null;
        if(!$this->interlocuteur_commercial->exist('telephone')) {
            $this->interlocuteur_commercial->add('telephone');
        }
        $this->interlocuteur_commercial->telephone = ($telephone) ? $telephone : null;
    }

    public function setStatut($statut, $auteur = null) {
        if($statut != $this->valide->_get('statut')) {
            $this->addHistorique($statut, $auteur);
        }

        return $this->valide->_set('statut', $statut);
    }

    public function isSupprimable()
    {

        return in_array($this->valide->statut, self::getStatutsSupprimable());
    }

    public function isBrouillon()
    {

    	return (!$this->valide->statut || in_array($this->valide->statut, array(self::STATUT_CREE, self::STATUT_PROJET_VENDEUR)))? true : false;
    }

	public function isProjetVendeur()
    {
        return ($this->valide->statut == self::STATUT_PROJET_VENDEUR);
    }

    public function isProjetAcheteur()
    {
        return ($this->valide->statut == self::STATUT_PROJET_ACHETEUR);
    }

    public function isValide()
    {
    	return ($this->numero_visa)? true : false;
    }

    public function isCloture()
    {
    	return ($this->valide->statut == self::STATUT_CLOTURE);
    }

    public function isAnnule()
    {
    	return ($this->valide->statut == self::STATUT_ANNULE);
    }

    public function getTypeDureeLibelle() {
        if($this->isPluriannuel()) {

            return 'Pluriannuel';
        }

        return 'Annuel';
    }

    public function getTypeDocumentLibelle() {
        if(in_array($this->valide->statut, array(self::STATUT_CREE, self::STATUT_PROJET_VENDEUR, self::STATUT_PROJET_ACHETEUR))) {

            return "Projet de contrat";
        }

        if(in_array($this->valide->statut, array(self::STATUT_PROPOSITION))) {

            return "Proposition de contrat";
        }

        return "Contrat";
    }

    public function getTypeTiers($userId)
    {
    	$types = self::getTypesTiers();
    	$type = null;
    	foreach ($types as $t) {
    		if ($this->get($t.'_identifiant') == $userId) {
    			$type = $t;
    			break;
    		}
    	}
    	return $type;
    }

    public function isActeur($userId) {
        if(!$this->getTypeTiers($userId)) {
            return false;
        }

        return true;
    }

    public function hasSigne($userId)
    {

        return $this->hasValide($userId);
    }

    public function hasValide($userId)
    {
    	try {
    		$date = $this->getUserDateValidation($userId);
    	} catch (sfException $e) {
    		throw new sfException($e->getMessage());
    	}
    	return ($date)? true : false;
    }

    public function getUserDateValidation($userId)
    {
    	$type = $this->getTypeTiers($userId);
    	if (!$type) {
    		throw new sfException('Le tiers "'.$userId.'" n\'est pas un acteur du contrat : '.$this->_id);
    	}
    	return ($this->valide->get('date_validation_'.$type))? $this->valide->get('date_validation_'.$type) : null;
    }

    public function validate()
    {
        if(!$this->isBrouillon()) {
            return;
        }

		if($this->isVendeurProprietaire()) {
            $this->setStatut(self::STATUT_PROJET_VENDEUR, $this->createur_identifiant);
            $this->createur_identifiant = $this->acheteur_identifiant;
            return;
        }

        $this->setStatut(self::STATUT_PROJET_ACHETEUR, $this->createur_identifiant);
    }

	public function signerPapier($date) {
		$this->valide->date_validation_vendeur = $date;
		$this->valide->date_validation_acheteur = $date;
		$this->valide->date_validation_mandataire = $date;
		$this->declaration->cleanAllNodes();
		$this->updateValideStatut();
		$this->forceClotureContrat(false);
		$this->valide->date_validation = $date;
		$this->valide->date_cloture = $date;
		$this->valide->email_cloture = true;
	}

    public function refusProjet($tiers_id) {

        $this->setStatut(self::STATUT_REFUS_PROJET, $tiers_id);
        $this->setStatut(self::STATUT_PROJET_VENDEUR);
    }

    public function signer($tiers_id)
    {
    	$type = $this->getTypeTiers($tiers_id);
    	if (!$type) {
    		throw new sfException('Le tiers "'.$tiers_id.'" n\'est pas un acteur du contrat : '.$this->_id);
    	}

        $this->declaration->cleanAllNodes();

        $this->setStatut(self::STATUT_SIGNE, $tiers_id);

        if($type == 'vendeur') {
            $this->setStatut(self::STATUT_PROPOSITION);
        } else {
            $this->setStatut(self::STATUT_VALIDE_PARTIELLEMENT);
        }

    	$this->valide->set('date_validation_'.$type, date('Y-m-d'));

        $this->updateValideStatut();
    }

	public function signerAutomatiquement($date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
		$this->valide->date_validation_vendeur = $date;
		$this->valide->date_validation_acheteur = $date;
        if ($this->mandataire_identifiant) {
		    $this->valide->date_validation_mandataire = $date;
        }
		$this->declaration->cleanAllNodes();
		$this->updateValideStatut();
		$this->valide->date_validation = $date;
    }

    public function updateValideStatut()
    {
    	$valide = true;
    	if ($this->vendeur_identifiant && !$this->valide->date_validation_vendeur) {
    		$valide = false;
    	}
    	if ($this->acheteur_identifiant && !$this->valide->date_validation_acheteur) {
    		$valide = false;
    	}
    	if ($this->mandataire_identifiant && !$this->valide->date_validation_mandataire) {
    		if (!$this->isAcheteurProprietaire()) {
    			$valide = false;
    		}
    	}
        if ($valide && $this->isPluriannuelCadre()) {
    		$this->valide->statut = self::STATUT_VALIDE_CADRE;
            $this->valide->email_cloture = date('Y-m-d');
    		$this->valide->date_validation = date('Y-m-d');
    	    return;
        }
    	if ($valide) {
    		$this->valide->statut = self::STATUT_VALIDE;
    		$this->valide->date_validation = date('Y-m-d');
    	}
    	if ($valide && !$this->needRetiraison()) {
    		$this->valide->email_cloture = date('Y-m-d');
    		$this->clotureContrat();
    	}
    }

    public function updateEnlevementStatut()
    {
    	if ($this->hasRetiraisons() && $this->valide->statut == self::STATUT_VALIDE) {
			$this->valide->statut = self::STATUT_ENLEVEMENT;
    	}
    }

	public function canForceClotureContrat()
	{

		return (!$this->volume_enleve_total && $this->isValide() && !$this->isCloture());
	}

	public function forceClotureContrat($fillDate = true)
    {
		$this->autoFillRetiraisons($fillDate);
		$this->updateTotaux();
        $this->updateEnlevementStatut();
		$this->clotureContrat();
    }

    public function clotureContrat()
    {
    	$this->setStatut(self::STATUT_CLOTURE, $this->createur_identifiant);
    	$this->valide->date_cloture = date('Y-m-d');
        if ($contratCadre = $this->getContratPluriannuelCadre()) {
            $applications = $contratCadre->getContratsApplication();
            if (!$applications) return;
            foreach($applications as $millesime => $application) {
                if (!$application) return;
                if (!$application->isCloture() && $application->_id != $this->_id) return;
            }
            $contratCadre->valide->statut = self::STATUT_CLOTURE;
            $contratCadre->valide->date_cloture = date('Y-m-d');
            $contratCadre->save();
        }
    }

    public function clotureProduits()
    {
    	$this->declaration->clotureProduits();
    	$this->valide->statut = self::STATUT_CLOTURE;
    	$this->valide->date_cloture = date('Y-m-d');
    }

    public function getTotalVolumeEnleve()
    {
    	return $this->volume_enleve_total;
    }

    public function getTotalVolumePropose()
    {
    	return $this->volume_propose_total;
    }

    public function getTotalSurfacePropose()
    {
    	return $this->surface_propose_total;
    }

    public function getTotalPrixEnleve()
    {
    	return $this->prix_reel_total;
    }

    public function getTotalPrixPropose()
    {
    	return $this->prix_total;
    }

    public function allProduitsClotures()
    {
    	return $this->declaration->allProduitsClotures();
    }

	public function autoFillRetiraisons($fillDate = true)
    {
    	return $this->declaration->autoFillRetiraisons($fillDate);
    }

    public function hasRetiraisons()
    {
    	return $this->declaration->hasRetiraisons();
    }

    public function isProprietaire($identifiant)
    {
    	return ($this->createur_identifiant == $identifiant)? true : false;
    }

    public function isAcheteurProprietaire()
    {
    	return ($this->createur_identifiant == $this->acheteur_identifiant)? true : false;
    }

    public function isVendeurProprietaire()
    {
    	return ($this->createur_identifiant == $this->vendeur_identifiant)? true : false;
    }

    public function hasVendeurSigne()
    {
    	return (bool) $this->valide->date_validation_vendeur;
    }

    public function hasAcheteurSigne()
    {
    	return (bool) $this->valide->date_validation_acheteur;
    }

    public function hasCourtierSigne()
    {
    	return (bool) $this->valide->date_validation_mandataire;
    }

    public function hasCourtier() {
        return ($this->mandataire_identifiant)? true : false;
    }

    public function getActeurs($withCreator = true) {
    	$acteurs = array();
    	if ($this->vendeur_identifiant) {
    		if ($withCreator) {
    			$acteurs[self::ROLE_VENDEUR] = $this->vendeur;
    		} elseif($this->vendeur_identifiant != $this->createur_identifiant) {
    			$acteurs[self::ROLE_VENDEUR] = $this->vendeur;
    		}
    	}
    	if ($this->acheteur_identifiant) {
    		if ($withCreator) {
    			$acteurs[self::ROLE_ACHETEUR] = $this->acheteur;
    		} elseif($this->acheteur_identifiant != $this->createur_identifiant) {
    			$acteurs[self::ROLE_ACHETEUR] = $this->acheteur;
    		}
    	}
    	if ($this->mandataire_identifiant) {
    		if ($withCreator) {
    			$acteurs[self::ROLE_MANDATAIRE] = $this->mandataire;
    		} elseif($this->mandataire_identifiant != $this->createur_identifiant) {
    			$acteurs[self::ROLE_MANDATAIRE] = $this->mandataire;
    		}
    	}
    	return $acteurs;
    }

    public function getEmails($withCreator = true) {
    	$acteurs = $this->getActeurs($withCreator);
    	$emails = array();
    	foreach ($acteurs as $type => $acteur) {
        	foreach($acteur->emails as $email) {
        		$emails[] = $email;
        	}
    	}

    	if ($this->interlocuteur_commercial->email && !in_array($this->interlocuteur_commercial->email, $emails)) {
    		$emails[] = $this->interlocuteur_commercial->email;
    	}

		$emails = array_values(array_unique($emails));

    	return $emails;
    }

    public function getEmailsActeur($acteurIdentifiant) {
    	$acteurs = $this->getActeurs(true);
    	$type = $this->getTypeTiers($acteurIdentifiant);
    	$emails = array();
    	if (isset($acteurs[$type])) {
        	foreach($acteurs[$type]->emails as $email) {
        		$emails[] = $email;
        	}
    	}
    	if ($acteurIdentifiant == $this->createur_identifiant && $this->interlocuteur_commercial->email) {
    		$emails[] = $this->interlocuteur_commercial->email;
    	}

        $emails = array_values(array_unique($emails));

    	return $emails;
    }

    public function getCreateurInformations()
    {
    	if ($this->createur_identifiant == $this->mandataire_identifiant) {
    		return $this->mandataire;
    	}
    	if ($this->createur_identifiant == $this->vendeur_identifiant) {
    		return $this->vendeur;
    	}
    	if ($this->createur_identifiant == $this->acheteur_identifiant) {
    		return $this->acheteur;
    	}
    	return null;
    }

    public function updateTotaux()
    {
    	$this->volume_enleve_total = $this->declaration->getTotalVolumeEnleve();
    	$this->volume_propose_total = $this->declaration->getTotalVolumePropose();
        $this->surface_propose_total = $this->declaration->getTotalSurfacePropose();
    	$this->prix_reel_total = $this->declaration->getTotalPrixEnleve();
    	$this->prix_total = $this->declaration->getTotalPrixPropose();
    }

    public function hasProduits()
    {
    	return $this->declaration->hasProduits();
    }

    protected function preSave()
    {
        $this->archivage_document->preSave();
        $visaChange = false;
        if (!$this->numero_visa && $this->numero_archive) {
    		$this->numero_visa = $this->numero_archive;
    		$prefixe = self::PREFIXE_NUMERO;
    		if ($this->type_contrat == VracClient::TYPE_BOUTEILLE) {
    			$prefixe++;
    		}
            $this->numero_db2 = sprintf('%06d', $prefixe.$this->numero_archive);
            $visaChange = true;
    	}
        if(!$this->numero_db2 && $this->numero_archive){
                $this->numero_visa = $this->numero_archive;
	    		$prefixe = self::PREFIXE_NUMERO;
	    		if ($this->type_contrat == VracClient::TYPE_BOUTEILLE) {
	    			$prefixe++;
	    		}
                $this->numero_db2 = sprintf('%06d', $prefixe.$this->numero_archive);
                $visaChange = true;
        }
        if ($this->isApplicationPluriannuel() && $visaChange) {
            $reference = $this->getContratDeReference();
            $this->numero_visa = $reference->numero_visa.'-'.substr($this->campagne, 0, 4);
        }
    }

    protected function doSave()
    {
    	if ($this->valide->statut == self::STATUT_ENLEVEMENT || $this->valide->statut == self::STATUT_CLOTURE) {
        	$this->date_modification = date('Y-m-d');
    	}

		if($this->isNew()) {
			$this->add('clause_reserve_propriete', null);
			$this->add('clause_mandat_facturation', null);
			$this->add('vendeur_frais_annexes');
			$this->add('acheteur_primes_diverses');
			$this->add('clause_resiliation');
            if($this->type_contrat == VracClient::TYPE_VRAC) {
				$this->add('suivi_qualitatif');
                $this->add('delais_retiraison');
			}
            if($this->type_contrat == VracClient::TYPE_MOUT) {
                $this->add('delais_retiraison');
			}
            if ($this->isPluriannuelCadre()) {
			    $this->add('clause_evolution_prix');
            }
		}
    }

    public function forceSave()
    {
    	$this->definitionValidation();
        if ($this->isModified()) {
    		$ret = acCouchdbManager::getClient()->save($this);
        	$this->_rev = $ret->rev;
        	$this->_serialize_loaded_json = serialize(new acCouchdbJsonNative($this->getData()));
         	return $ret;
        }
        return false;
    }

    public function isArchivageCanBeSet()
    {
        return ($this->valide->statut == self::STATUT_VALIDE || $this->valide->statut == self::STATUT_VALIDE_CADRE || $this->valide->statut == self::STATUT_CLOTURE);
    }

    public function setTiersQualite($noeud, $qualite)
    {
    	if ($qualite == EtablissementFamilles::FAMILLE_NEGOCIANT) {
    		$this->{$noeud} = AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
    	} elseif ($qualite == EtablissementFamilles::FAMILLE_COOPERATIVE) {
    		$this->{$noeud} = AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY;
    	} else {
			$this->{$noeud} = AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
		}

    }

    public function setAcheteurQualite($qualite)
    {
    	$this->setTiersQualite('acheteur_type', $qualite);
    }

    public function setVendeurQualite($qualite)
    {
    	$this->setTiersQualite('vendeur_type', $qualite);
    }

	public function isPapier() {

		return $this->exist('papier') && $this->papier;
	}

	public function isInterne() {

		return $this->exist('interne') && $this->interne;
	}

	public function getRepartitionCVOCoef($vendeurIdentifiant,$date){
		/**
		 * Pas de repartition CVO dans les mvts
		 */
		return 0.0;
	}

	public function getMercurialeValue() {
		//La vraie valeur est mise dans la vue couchdb : _design/VRAC/_view/contrats
		if ($this->isInterne()) {
			return "I";
		}
		if ($this->vendeur_type == 'caves_cooperatives') {
			return "C";
		}
		if ($this->vendeur_type == 'negociants') {
			return "X";
		}
		if ($this->acheteur_type == 'recoltants') {
			return "V";
		}
		return "M";
	}

	public function isPonctuel() {
		return !$this->contrat_pluriannuel;
	}

	public function isPluriannuel() {
		return !$this->isPonctuel();
	}

    public function hasReferencePluriannuel() {
        return ($this->exist('reference_contrat_pluriannuel') && $this->reference_contrat_pluriannuel);
    }

    public function isPluriannuelCadre() {
        return (!$this->isPonctuel() && !$this->hasReferencePluriannuel());
    }

	public function isApplicationPluriannuel() {
        return (!$this->isPonctuel() && $this->hasReferencePluriannuel());
	}

    public function isInModeSurface() {
        return $this->contrat_pluriannuel_mode_surface||($this->type_contrat == VracClient::TYPE_RAISIN);
    }

    public function hasContratApplication() {
        if ($this->isPluriannuelCadre() && $this->isValide()) {
            $nbCampagnes = VracClient::getConfigVar('nb_campagnes_pluriannuel');
			$millesime = substr($this->campagne, 0, 4) * 1;
            $maxMillesime = $millesime+$nbCampagnes;
            while($millesime < $maxMillesime) {
                $numContrat = $this->numero_contrat.$millesime;
                if (VracClient::getInstance()->findByNumeroContrat($numContrat))
                    return true;
                $millesime++;
            }
		}
        return false;
    }

	public function getContratsApplication() {
		$contrats = array();
		if ($this->isPluriannuelCadre() && $this->isValide()) {
            $nbCampagnes = VracClient::getConfigVar('nb_campagnes_pluriannuel');
			$millesime = substr($this->campagne, 0, 4) * 1;
            $maxMillesime = $millesime+$nbCampagnes;
            while($millesime < $maxMillesime) {
                $numContrat = $this->numero_contrat.$millesime;
                $contrats[$numContrat] = VracClient::getInstance()->findByNumeroContrat($numContrat);
                $millesime++;
            }
		}
		return $contrats;
	}

	public function getContratPluriannuelCadre() {
		return ($this->isApplicationPluriannuel())? VracClient::getInstance()->find($this->reference_contrat_pluriannuel) : null;
	}

    public function getContratDeReference() {
        if ($pluriannuelCadre = $this->getContratPluriannuelCadre()) {
            return $pluriannuelCadre;
        }
        return $this;
    }

    public function getNextNumContratApplication() {
        $contratsApplication = $this->getContratsApplication();
        if (!$contratsApplication)
            throw new Exception('Le contrat '.$this->_id.' ne permet pas la création de contrat d\'application');
        foreach($contratsApplication as $numContratApplication => $contratApplication) {
            if (!$contratApplication) return $numContratApplication;
        }
        return null;
    }

    public function getLastContratApplication() {
        $contratsApplication = $this->getContratsApplication();
		$last = null;
        if (!$contratsApplication)
            return null;
        foreach($contratsApplication as $numContratApplication => $contratApplication) {
            if ($contratApplication) $last = $contratApplication;
        }
        return $last;
    }

	public function generateNextPluriannuelApplication() {
        $numContratApplication = $this->getNextNumContratApplication();
        if (!$numContratApplication)
            throw new Exception('L\'ensemble des campagnes d\'application du contrat '.$this->_id.' ont été générées');
		if ($last = $this->getLastContratApplication()) {
			if (!$last->isValide()) throw new Exception('Un contrat d\'application du contrat '.$this->_id.' est en cours de validation');
            if (!$last->isCloture()) throw new Exception('Un contrat d\'application du contrat '.$this->_id.' est n\'est pas cloturé');
		}
        $millesime = substr($numContratApplication, -4) * 1;
		$vrac = clone $this;
        $vrac->remove('_attachments');
        $vrac->setArchivageDocument();
        $vrac->campagne = $millesime.'-'.($millesime+1);
        $vrac->numero_contrat = "$numContratApplication";
        $vrac->numero_archive = null;
        $vrac->numero_visa = null;
        $vrac->numero_db2 = null;
		$vrac->contrat_pluriannuel_mode_surface = 0;
        $vrac->remove('valide');
        $vrac->add('valide');
        $vrac->valide->statut = self::STATUT_CREE;
		$vrac->valide->date_saisie = date('Y-m-d');
        $vrac->constructId();
		$vrac->add('reference_contrat_pluriannuel', $this->_id);
		foreach($vrac->declaration->getProduitsDetails() as $key => $detail) {
            $detail->millesime = $millesime;
        }
        $vrac->remove('historique');
        return $vrac;
	}

	public function getPourcentageTotalDesClausesEvolutionPrix() {
		if ($this->exist('clause_evolution_prix') && $this->clause_evolution_prix) {
            $total = 0;
            $clauses = explode(PHP_EOL, $this->clause_evolution_prix);
            foreach($clauses as $clause) {
                $pos = strpos($clause, '%');
                if ($pos !== false) {
                    $total += (substr($clause, 0, $pos) * 1);
                }
            }
            return $total;
        }
        return 100;
	}

	public function isPremiereApplication() {
		if ($cadre = $this->getContratPluriannuelCadre()) {
            $applications = $cadre->getContratsApplication();
            $premiereApplication = array_keys($applications);
			if (isset($premiereApplication[0]) && $premiereApplication[0]) {
				return ($this->numero_contrat == $premiereApplication[0]);
			}
		}
		return false;
	}

    public function hasVersion() {
        return @file_get_contents($this->getAttachmentUri(self::VENDEUR_PROJET_FILENAME)) !== false;
    }

    public function getMother() {
        $mother = new Vrac();
        if ($projet = file_get_contents($this->getAttachmentUri(self::VENDEUR_PROJET_FILENAME))) {
            $mother->loadFromCouchdb(json_decode($projet));
        }
        return $mother;
    }

    public function getDiffWithMother() {
        if (is_null($this->diff_with_mother)) {
            $mother = $this->getMother();
            $this->diff_with_mother = $this->getDiffWithAnotherDocument($mother->getData());
        }
        return $this->diff_with_mother;
    }

    public function isModifiedMother($hash_or_object, $key = null) {
        if(!$this->document->hasVersion()) {
            return false;
        }
        $hash = ($hash_or_object instanceof acCouchdbJson) ? $hash_or_object->getHash() : $hash_or_object;
        $hash .= ($key) ? "/".$key : null;

        return array_key_exists($hash, $this->getDiffWithMother());
    }

    protected function getDiffWithAnotherDocument(stdClass $document) {

        $other_json = new acCouchdbJsonNative($document);
        $current_json = new acCouchdbJsonNative($this->document->getData());

        return $current_json->diff($other_json);
    }

    public function getPrixUnite() {
        if($this->exist('prix_unite') && $this->_get('prix_unite')) {

            return $this->_get('prix_unite');
        }
        if($this->type_contrat == VracClient::TYPE_BOUTEILLE) {

            return VracClient::PRIX_BOUTEILLE;
        }

        return VracClient::PRIX_HL;
    }

    public function getPrixUniteLibelle() {

        return VracClient::$prix_unites[$this->getPrixUnite()];
    }

    public function isType($type) {
        return ($this->type_contrat == $type);
    }

    public function needRetiraison() {

        return !$this->isPluriannuelCadre() && in_array($this->type_contrat, array(VracClient::TYPE_VRAC));
    }

    public function getTauxCvo() {
        foreach($this->declaration->getProduitsDetails() as $detail) {
            $date = $this->valide->date_saisie ? $this->valide->date_saisie : date('Y-m-d');
            $conf = ConfigurationClient::getConfiguration($date)->get(HashMapper::convert($detail->getCepage()->getHash()));
            $tx = round($conf->getTauxCvo($this->valide->date_saisie ? $this->valide->date_saisie : date('Y-m-d')) / 2, 2);
            return ($tx >= 0)? $tx : null;
        }

        return null;
    }

	public function storeAnnexe($file, $filename) {
        if (!is_file($file)) {
            throw new Exception($file." n'est pas un fichier valide");
        }
        $pathinfos = pathinfo($file);
        $extension = (isset($pathinfos['extension']) && $pathinfos['extension'])? strtolower($pathinfos['extension']): null;
        if ($extension) {
            $filename .= '.'.$extension;
        }
        if ($this->deleteAnnexe($filename)) {
            $this->save();
        }
        $this->storeAttachment($file, mime_content_type($file), $filename);
    }

    public function deleteAnnexe($annexe) {
        if ($filename = $this->getAnnexeFilename($annexe)) {
            $this->_attachments->remove($filename);
            return true;
        }
        return false;
    }

    public function getAnnexeFilename($annexe) {
        if(!$this->exist('_attachments')) {
            return null;
        }
        foreach ($this->_attachments as $filename => $fileinfos) {
            if (strpos($filename, $annexe) !== false) return $filename;
        }
        return null;
    }

    public function addHistorique($statut, $auteur = null) {
        if(!isset(self::$statuts_libelles_historique[$statut]) || !self::$statuts_libelles_historique[$statut]) {
            return;
        }
        $histo = $this->add('historique')->add(null);
        $histo->date = date('Y-m-d H:i:s');
        $histo->statut = $statut;
        $histo->auteur = $auteur;
        $histo->description = self::$statuts_libelles_historique[$statut];
    }

    public function getAllAnnexesFilename() {
        $annexes = [];
        foreach ($this->_attachments as $filename => $fileinfos) {
            if (strpos($filename, VracClient::VRAC_PREFIX_ANNEXE) !== false) {
                $annexes[$filename] = pathinfo($filename, PATHINFO_FILENAME);
            }
        }
        return $annexes;
    }

    public function hasAnnexes() {
        return ($this->getAllAnnexesFilename())? true : false;
    }

}
