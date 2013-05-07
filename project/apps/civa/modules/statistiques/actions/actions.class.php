<?php

/**
 * statistiques actions.
 *
 * @package    civa
 * @subpackage statistiques
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class statistiquesActions extends sfActions {
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        ini_set('memory_limit', '512M');

        $this->nbInscrit = 0;
        $this->etapeDrValidee=0;
        $this->etapeValidation = 0;
        $this->etapeRecolte = 0;
        $this->etapeExploitation = 0;
        $this->etapeDrNonValidee = 0;
        $this->etapeNoDr = 0;
        $this->nbInscritGamma = 0;

        $metteur = acCouchdbManager::getClient("MetteurEnMarche")->getAll(acCouchdbClient::HYDRATE_JSON);

        foreach ($metteur as $item) {
            if (isset($item->gamma) && $item->gamma->statut == 'INSCRIT') {
                $this->nbInscritGamma++;
            }
        }
        
        $dr_validees = acCouchdbManager::getClient()->group(true)
                                              ->group_level(2)
                                              ->startkey(array(true, true))
                                              ->endkey(array(true, true, array()))
                                              ->getView("STATS", "DR");
        
        $dr_non_validees = acCouchdbManager::getClient()->group(true)
                                              ->group_level(2)
                                              ->startkey(array(false, false))
                                              ->endkey(array(false, false, array()))
                                              ->getView("STATS", "DR");
        
       $dr_non_validees_etapes_exploitation = acCouchdbManager::getClient()->group(true)
                                              ->group_level(4)
                                              ->startkey(array(false, false, null, 'exploitation'))
                                              ->endkey(array(false, false, null, 'exploitation'))
                                              ->getView("STATS", "DR");
         
         
        $dr_non_validees_etapes_recolte = acCouchdbManager::getClient()->group(true)
                                              ->group_level(4)
                                              ->startkey(array(false, false, null, 'recolte'))
                                              ->endkey(array(false, false, null, 'recolte'))
                                              ->getView("STATS", "DR");
        
        $dr_non_validees_etapes_validation = acCouchdbManager::getClient()->group(true)
                                              ->group_level(4)
                                              ->startkey(array(false, false, null, 'validation'))
                                              ->endkey(array(false, false, null, 'validation'))
                                              ->getView("STATS", "DR");
        
        $compte_inscrit = acCouchdbManager::getClient()->group(true)
                                              ->group_level(1)
                                              ->startkey(array('INSCRIT'))
                                              ->endkey(array('INSCRIT'))
                                              ->getView("STATS", "COMPTE");
        
        $compte_mot_de_passe_oublie = acCouchdbManager::getClient()->group(true)
                                              ->group_level(1)
                                              ->startkey(array('MOT_DE_PASSE_OUBLIE'))
                                              ->endkey(array('MOT_DE_PASSE_OUBLIE'))
                                              ->getView("STATS", "COMPTE");
        
        $utilisateurs_edition = acCouchdbManager::getClient()->group(true)
                                              ->group_level(1)
                                              ->getView("STATS", "edition");


	$this->nb_csv_acheteurs = acCouchdbManager::getClient('CSV')->countCSVsAcheteurs();

        $this->etapeDrValidee = $dr_validees->rows[0]->value;
        $this->etapeDrNonValidee = $dr_non_validees->rows[0]->value;
        
        if (isset($dr_non_validees_etapes_exploitation->rows) && count($dr_non_validees_etapes_exploitation->rows) > 0) {
            $this->etapeExploitation = $dr_non_validees_etapes_exploitation->rows[0]->value;
        }
        if (isset($dr_non_validees_etapes_recolte->rows) && count($dr_non_validees_etapes_recolte->rows) > 0) {
            $this->etapeRecolte = $dr_non_validees_etapes_recolte->rows[0]->value;
        }
        if (isset($dr_non_validees_etapes_validation->rows) && count($dr_non_validees_etapes_validation->rows) > 0) {
            $this->etapeValidation = $dr_non_validees_etapes_validation->rows[0]->value;
        }
        
        if(isset($compte_inscrit->rows) && count($compte_inscrit->rows) > 0) {
            $this->nbInscrit = $compte_inscrit->rows[0]->value;
        }
        
        if(isset($compte_mot_de_passe_oublie->rows) && count($compte_mot_de_passe_oublie->rows) > 0) {
            $this->nbInscrit += $compte_mot_de_passe_oublie->rows[0]->value;
            $this->nbOublie = $compte_mot_de_passe_oublie->rows[0]->value;
        }
	$this->utilisateurs_edition = array(); $cpt = 0;
	foreach ($utilisateurs_edition->rows as $u) {
                if(!preg_match('/^COMPTE-[0-9]{10}$/', $u->key[0])) {
                    $this->utilisateurs_edition[$u->key[0]] = $u->value;
                }
	}
	arsort($this->utilisateurs_edition);
    }
}
