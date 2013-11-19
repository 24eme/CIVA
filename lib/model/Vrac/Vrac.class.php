<?php
/**
 * Model for Vrac
 *
 */

class Vrac extends BaseVrac implements InterfaceArchivageDocument
{
	
	const STATUT_CREE = 'CREE';
	const STATUT_VALIDE_PARTIELLEMENT = 'VALIDE_PARTIELLEMENT';
	const STATUT_VALIDE = 'VALIDE';
	const STATUT_ANNULE = 'ANNULE';
	const STATUT_ENLEVEMENT = 'ENLEVEMENT';
	const STATUT_CLOTURE = 'CLOTURE';
	const PREFIXE_NUMERO = 8;
	const CEPAGE_EDEL = "cepage_ED";
	const CEPAGE_EDEL_LIBELLE_COMPLEMENT = " (Edel)";
	const CEPAGE_MUSCAT = "cepage_MU";
	const CEPAGE_MUSCAT_LIBELLE = "Muscat";
    const CAMPAGNE_ARCHIVE = 'UNIQUE';
	
	protected $_config;
	protected $archivage_document;
	
	static $statuts_libelles = array(
		self::STATUT_CREE => 'Brouillon',
		self::STATUT_VALIDE_PARTIELLEMENT => 'En attente de validation',
		self::STATUT_VALIDE => 'Validé',
		self::STATUT_ANNULE => 'Annulé',
		self::STATUT_ENLEVEMENT => 'En cours d\'enlèvement',
		self::STATUT_CLOTURE => 'Cloturé'
	);
	
	static $statuts_libelles_actions = array(
		self::STATUT_CREE => null,
		self::STATUT_VALIDE_PARTIELLEMENT => 'Visualiser pour signer',
		self::STATUT_VALIDE => 'Visualiser',
		self::STATUT_ANNULE => 'Visualiser',
		self::STATUT_ENLEVEMENT => 'Visualiser',
		self::STATUT_CLOTURE => 'Visualiser'
	);
	
	static $statuts_libelles_actions_proprietaire = array(
		self::STATUT_CREE => 'Continuer',
		self::STATUT_VALIDE_PARTIELLEMENT => 'Visualiser',
		self::STATUT_VALIDE => 'Enlever',
		self::STATUT_ENLEVEMENT => 'Enlever',
	);
	
	static $statuts_supprimable = array(
		self::STATUT_CREE,
		self::STATUT_VALIDE_PARTIELLEMENT,
		self::STATUT_VALIDE
	);
	
	static $types_tiers = array(
		'vendeur',
		'acheteur',
		'mandataire'
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
        $this->numero_contrat = $numeroContrat;
        $this->valide->date_saisie = $date;
        $this->valide->statut = self::STATUT_CREE;
        $this->acheteur_type = AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
        $this->vendeur_type = AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
        $this->createur_identifiant = $createurIdentifiant;
        $this->initProduits();
    }

    public function getCampagneArchive() {
        if(!$this->_get('campagne_archive')) {
            $this->_set('campagne_archive', self::CAMPAGNE_ARCHIVE);
        }
        return $this->_get('campagne_archive');
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
        $conf_2012 = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
        if($campagne > '2012'){
            return $conf_2012;
        }        
        $conf = acCouchdbManager::getClient('Configuration')->retrieveConfiguration($campagne);
        return $conf;
     }

    protected function initProduits() 
    {
    	if (isset($this->_config[VracClient::APP_CONFIGURATION_PRODUITS])) {
    		$produits = $this->_config[VracClient::APP_CONFIGURATION_PRODUITS];
    		foreach ($produits as $produit_hash => $produit_config) {
    			$this->addDetail($produit_hash, $produit_config);
    		}
    	}
    }
    
	public function addProduit($hash) 
	{
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $hash_config = preg_replace('/^declaration/','recolte', $hash);
        $produit = $this->getOrAdd($hash);
        $config = $produit->getConfig();
        $produit->libelle = $config->getLibelle();
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
        return $produit->addDetail($config);
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
    	$compte = $tiers->getCompteObject();

    	$this->acheteur->intitule = ($tiers->exist("intitule"))? $tiers->intitule : null;
    	$this->acheteur->raison_sociale = $tiers->nom;
    	$this->acheteur->siret = $tiers->siret;
    	$this->acheteur->cvi = $tiers->cvi;
    	$this->acheteur->num_accise = $tiers->no_accises;
    	$this->acheteur->civaba = $tiers->civaba;
    	$this->acheteur->adresse = $tiers->siege->adresse;
    	$this->acheteur->code_postal = $tiers->siege->code_postal;
    	$this->acheteur->commune = $tiers->siege->commune;
    	$this->acheteur->telephone = $tiers->telephone;
    	$this->acheteur->famille = null;
    	$this->acheteur->identifiant = $tiers->_id;

        $this->acheteur->remove('emails'); 
        $this->acheteur->add('emails'); 

        if($this->isProprietaire($this->acheteur->identifiant)) {
            $this->acheteur->emails = $tiers->getEmailsByDroit(_CompteClient::DROIT_VRAC_RESPONSABLE);
        } else {
            $this->acheteur->emails = $tiers->getEmailsByDroit(_CompteClient::DROIT_VRAC_SIGNATURE);
        }
    }
    
    public function storeVendeurInformations($tiers)
    {
    	$compte = $tiers->getCompteObject();
        $num_accise = null;
        $civaba = null;
        if ($compte) {
            if ($metteurEnMarche = $compte->getTiersType('MetteurEnMarche')) {
                      $num_accise = $metteurEnMarche->no_accises;
                       $civaba = $metteurEnMarche->civaba;
            }
        } 
       if (!$num_accise) {
               if ($tiers->exist('civaba') && $tiers->civaba) {
                       if ($metteurEnMarche = acCouchdbManager::getClient('MetteurEnMarche')->retrieveByCvi($tiers->civaba)) {
                               $num_accise = $metteurEnMarche->no_accises;
                               $civaba = $metteurEnMarche->civaba;
                       }
               }
        }


    	$this->vendeur->intitule = ($tiers->exist("intitule"))? $tiers->intitule : null;
    	$this->vendeur->raison_sociale = $tiers->nom;
    	$this->vendeur->siret = $tiers->siret;
    	$this->vendeur->cvi = $tiers->cvi;
    	$this->vendeur->num_accise = $num_accise;
    	$this->vendeur->civaba = $civaba;
    	$this->vendeur->adresse = $tiers->siege->adresse;
    	$this->vendeur->code_postal = $tiers->siege->code_postal;
    	$this->vendeur->commune = $tiers->siege->commune;
    	$this->vendeur->telephone = $tiers->telephone;
    	$this->vendeur->famille = null;
    	$this->vendeur->identifiant = $tiers->_id;

        $this->vendeur->remove('emails'); 
        $this->vendeur->add('emails'); 

        if($this->isProprietaire($this->vendeur->identifiant)) {
            $this->vendeur->emails = $tiers->getEmailsByDroit(_CompteClient::DROIT_VRAC_RESPONSABLE);
        } else {
            $this->vendeur->emails = $tiers->getEmailsByDroit(_CompteClient::DROIT_VRAC_SIGNATURE);
        }
    }

    public function storeMandataireInformations($tiers)
    {
    	$this->mandataire->intitule = ($tiers->exist("intitule"))? $tiers->intitule : null;
    	$this->mandataire->nom = $tiers->nom;
    	$this->mandataire->raison_sociale = $tiers->nom;
    	$this->mandataire->siret = $tiers->siret;
    	$this->mandataire->carte_pro = $tiers->no_carte_professionnelle;
    	$this->mandataire->adresse = $tiers->siege->adresse;
    	$this->mandataire->code_postal = $tiers->siege->code_postal;
    	$this->mandataire->commune = $tiers->siege->commune;
    	$this->mandataire->telephone = $tiers->telephone;
    	$this->mandataire->famille = null;
    	$this->mandataire->identifiant = $tiers->_id;

        $this->mandataire->remove('emails'); 
        $this->mandataire->add('emails'); 

        if($this->isProprietaire($this->mandataire->identifiant)) {
            $this->mandataire->emails = $tiers->getEmailsByDroit(_CompteClient::DROIT_VRAC_RESPONSABLE);
        } else {
            $this->mandataire->emails = $tiers->getEmailsByDroit(_CompteClient::DROIT_VRAC_SIGNATURE);
        }
    }
    
    public function isSupprimable($userId)
    {
    	if ($userId == $this->createur_identifiant) {
    		if (in_array($this->valide->statut, self::getStatutsSupprimable())) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function isBrouillon()
    {
    	return (!$this->valide->statut || $this->valide->statut == self::STATUT_CREE)? true : false;
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
    
    public function isSigne()
    {
    	return !($this->valide->statut == self::STATUT_VALIDE_PARTIELLEMENT || $this->valide->statut == self::STATUT_CREE);
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
    
    public function signerProrietaire()
    {   
        $this->signer($this->createur_identifiant);
    }

    public function signer($tiers_id)
    {
    	$type = $this->getTypeTiers($tiers_id);
    	if (!$type) {
    		throw new sfException('Le tiers "'.$tiers_id.'" n\'est pas un acteur du contrat : '.$this->_id);
    	}

        if($this->isProprietaire($tiers_id)) {
            $this->declaration->cleanAllNodes();
            $this->valide->statut = Vrac::STATUT_VALIDE_PARTIELLEMENT;
        }

    	$this->valide->set('date_validation_'.$type, date('Y-m-d'));

        $this->updateValideStatut();
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
    	if ($valide) {
    		$this->valide->statut = self::STATUT_VALIDE;
    		$this->valide->date_validation = date('Y-m-d');
    	}
    }
    
    public function updateEnlevementStatut()
    {
    	if ($this->hasRetiraisons() && $this->valide->statut == self::STATUT_VALIDE) {
    		$this->valide->statut = self::STATUT_ENLEVEMENT;
    	}
    }
    
    public function clotureContrat()
    {
    	$this->valide->statut = self::STATUT_CLOTURE;
    	$this->valide->date_cloture = date('Y-m-d');
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
    
    public function hasCourtier() {
        return ($this->mandataire_identifiant)? true : false;
    }
    
    public function getActeurs($withCreator = true) {
    	$acteurs = array();
    	if ($this->vendeur_identifiant) {
    		if ($withCreator) {
    			$acteurs['vendeur'] = $this->vendeur;
    		} elseif($this->vendeur_identifiant != $this->createur_identifiant) {
    			$acteurs['vendeur'] = $this->vendeur;
    		}
    	}
    	if ($this->acheteur_identifiant) {
    		if ($withCreator) {
    			$acteurs['acheteur'] = $this->acheteur;
    		} elseif($this->acheteur_identifiant != $this->createur_identifiant) {
    			$acteurs['acheteur'] = $this->acheteur;
    		}
    	}
    	if ($this->mandataire_identifiant) {
    		if ($withCreator) {
    			$acteurs['mandataire'] = $this->mandataire;
    		} elseif($this->mandataire_identifiant != $this->createur_identifiant) {
    			$acteurs['mandataire'] = $this->mandataire;
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
    	if ($this->interlocuteur_commercial->email) {
    		$emails[] = $this->interlocuteur_commercial->email;
    	}
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
        if (!$this->numero_visa && $this->numero_archive) {
    		$this->numero_visa = $this->numero_archive;
                $this->numero_db2 = sprintf('%06d', self::PREFIXE_NUMERO.$this->numero_archive);
    	}
        if(!$this->numero_db2 && $this->numero_archive){
                $this->numero_visa = $this->numero_archive;
                $this->numero_db2 = sprintf('%06d', self::PREFIXE_NUMERO.$this->numero_archive);
        }
    }
    
    protected function doSave() 
    {
    	if ($this->valide->statut == self::STATUT_ENLEVEMENT || $this->valide->statut == self::STATUT_CLOTURE) {
        	$this->date_modification = date('Y-m-d');
    	}
    }
    
    public function isArchivageCanBeSet() 
    {
        return ($this->valide->statut == self::STATUT_VALIDE);
    }
    
    public function setAcheteurQualite($qualite)
    {
    	if ($qualite == 'Negociant') {
    		$this->acheteur_type = AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
    	}
    	if ($qualite == 'Cooperative') {
    		$this->acheteur_type = AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY;
    	}
    }
}