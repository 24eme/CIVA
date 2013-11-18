<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class importDSTask
 * @author mathurin
 */
class importAnnuaireVracTask extends importAbstractTask {


    protected function configure() {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('proprietaire', sfCommandArgument::REQUIRED, "Identifiant du compte annuaire"),
            new sfCommandArgument('acteur', sfCommandArgument::REQUIRED, "Identifiant du compte à ajouter à l'annuaire"),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'import';
        $this->name = 'annuaire-vrac';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [importDS|INFO] task does things.
Call it with:

  [php symfony import:annuaire-vrac|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        
        $proprietaire = $arguments['proprietaire'];
        $acteur = $arguments['acteur'];
        
        $proprietaireTiers = _TiersClient::getInstance()->findByCvi($proprietaire);
        $acteurTiers = _TiersClient::getInstance()->findByCvi($acteur);
        if (!$proprietaireTiers) {
        	$proprietaireTiers = _TiersClient::getInstance()->find('MET-'.$proprietaire);
        }
        if (!$acteurTiers) {
        	$acteurTiers = _TiersClient::getInstance()->find('MET-'.$acteur);
        	if ($acteurTiers->hasCvi()) {
        		if ($achat = _TiersClient::getInstance()->find('ACHAT-'.$acteurTiers->cvi)) {
        			$acteurTiers = $achat;
        		}
        	}
        }
        if (!$proprietaireTiers) {
        	$liaisons = acCouchdbManager::getClient()->reduce(false)->startkey(array($proprietaire))->endkey(array($proprietaire, array()))->getView("TIERS", "liaison");
        	foreach ($liaisons->rows as $liaison) {
	        	$proprietaireTiers = _TiersClient::getInstance()->find($liaison->key[2]);
	        }
        }
        if ($proprietaireTiers && $acteurTiers) {
        	if ($proprietaireCompte = $proprietaireTiers->getCompteObject()) {
	        	if ($proprietaireCompte->isInscrit()) {
	        		$annuaire = AnnuaireClient::getInstance()->findOrCreateAnnuaire($proprietaireCompte->login);
	        		$type = $this->getType($acteurTiers);
	        		if ($type) {
	    				$libelle = ($acteurTiers->intitule)? $acteurTiers->intitule.' '.$acteurTiers->nom : $acteurTiers->nom;
	        			$annuaire->get($type)->add($acteurTiers->_id, $libelle);
	        			$annuaire->save();
	    				$this->logSection('Annuaire', "Succès de l'ajout du tiers à l'annuaire : ".$annuaire->_id);
	        		} else {
	        			$this->logSection('Type', "Type non determiné pour le tiers : ".$acteurTiers->_id, null, 'ERROR');
	        		}
	        	} else {
	        		$this->logSection('Compte', "Compte non inscrit : ".$proprietaireCompte->_id, null, 'ERROR');
	        	}
        	} else {
        		$this->logSection('Compte', "Pas de compte : ".$proprietaireTiers->_id, null, 'ERROR');
        	}
        } else {
        	if (!$proprietaireTiers) {
        		$this->logSection('Tiers', "Tiers non présent dans la base : ".$proprietaire, null, 'ERROR');
        	}
        	if (!$acteurTiers) {
        		$this->logSection('Tiers', "Tiers non présent dans la base : ".$acteur, null, 'ERROR');
        	}
        }
    }
    
    private function getType($tiers)
    {
    	$tiersQualites = AnnuaireClient::getTiersQualites();
    	if ($tiers->type == 'Recoltant') {
    		return AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
    	}
    	if ($tiers->type == 'Acheteur') {
    		if ($tiers->qualite == $tiersQualites[AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY]) {
    			return AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
    		}
    		if ($tiers->qualite == $tiersQualites[AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY]) {
    			return AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY;
    		}
    		if ($tiers->qualite == 'Recoltant') {
    			return AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
    		}
    	}
    	if ($tiers->type == 'MetteurEnMarche') {
    		if ($tiers->hasCvi()) {
    			if ($achat = _TiersClient::getInstance()->find('ACHAT-'.$tiers->cvi) {
    				return $this->getType($achat);
    			}
    			if ($tiers->exist('qualite')) {
		    		if ($tiers->qualite == $tiersQualites[AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY]) {
		    			return AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY;
		    		}
		    		if ($tiers->qualite == $tiersQualites[AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY]) {
		    			return AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY;
		    		}
		    		if ($tiers->qualite == 'Recoltant') {
		    			return AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY;
		    		}
    			}
    		}
    	}
    	return null;
    }

}
