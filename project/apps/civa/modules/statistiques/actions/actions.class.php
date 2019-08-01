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
        $this->processStatsCompte();
        $this->processStatsDR();
        $this->ds_types = array(DSCivaClient::TYPE_DS_PROPRIETE => "Propriété", DSCivaClient::TYPE_DS_NEGOCE => "Négoce");
        $this->etapeDsValidee = array();
        $this->etapeDsNonValidee = array();
        $this->etapeDsNonValideeEtapes = array();
        $this->utilisateurs_edition_ds = array();
        $this->processStatsDS(DSCivaClient::TYPE_DS_PROPRIETE);
        $this->processStatsDS(DSCivaClient::TYPE_DS_NEGOCE);
    }


    protected function processStatsCompte() {
        $this->nbInscrit = 0;
        $this->nbInscritGamma = 0;

        // $metteur = acCouchdbManager::getClient("MetteurEnMarche")->getAll(acCouchdbClient::HYDRATE_JSON);

        // foreach ($metteur as $item) {
        //     if (isset($item->gamma) && $item->gamma->statut == 'INSCRIT') {
        //         $this->nbInscritGamma++;
        //     }
        // }

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





        if(isset($compte_inscrit->rows) && count($compte_inscrit->rows) > 0) {
            $this->nbInscrit = $compte_inscrit->rows[0]->value;
        }

        if(isset($compte_mot_de_passe_oublie->rows) && count($compte_mot_de_passe_oublie->rows) > 0) {
            $this->nbInscrit += $compte_mot_de_passe_oublie->rows[0]->value;
            $this->nbOublie = $compte_mot_de_passe_oublie->rows[0]->value;
        }
    }

    protected function processStatsDR() {
        $campagne = $this->getUser()->getCampagne();
        $this->etapeDrValidee=0;
        $this->etapeValidation = 0;
        $this->etapeRecolte = 0;
        $this->etapeExploitation = 0;
        $this->etapeDrNonValidee = 0;
        $this->etapeNoDr = 0;
        $this->drTeledeclare = 0;
        $this->drPapier = 0;
        $this->drAuto = 0;

        $dr_validees = acCouchdbManager::getClient()->group(true)
                                              ->group_level(3)
                                              ->startkey(array($campagne, true, true))
                                              ->endkey(array($campagne, true, true, array()))
                                              ->getView("STATS", "DR");

        $dr_non_validees = acCouchdbManager::getClient()->group(true)
                                              ->group_level(3)
                                              ->startkey(array($campagne, false, false))
                                              ->endkey(array($campagne, false, false, array()))
                                              ->getView("STATS", "DR");

        $dr_non_validees_etapes_exploitation = acCouchdbManager::getClient()->group(true)
                                              ->group_level(5)
                                              ->startkey(array($campagne, false, false, null, 'exploitation'))
                                              ->endkey(array($campagne, false, false, null, 'exploitation', array()))
                                              ->getView("STATS", "DR");

        $dr_non_validees_etapes_repartition = acCouchdbManager::getClient()->group(true)
                                              ->group_level(5)
                                              ->startkey(array($campagne, false, false, null, 'repartition'))
                                              ->endkey(array($campagne, false, false, null, 'repartition', array()))
                                              ->getView("STATS", "DR");


        $dr_non_validees_etapes_recolte = acCouchdbManager::getClient()->group(true)
                                              ->group_level(5)
                                              ->startkey(array($campagne, false, false, null, 'recolte'))
                                              ->endkey(array($campagne, false, false, null, 'recolte', array()))
                                              ->getView("STATS", "DR");

        $dr_non_validees_etapes_validation = acCouchdbManager::getClient()->group(true)
                                              ->group_level(5)
                                              ->startkey(array($campagne, false, false, null, 'validation'))
                                              ->endkey(array($campagne, false, false, null, 'validation', array()))
                                              ->getView("STATS", "DR");

        $this->nb_csv_acheteurs = acCouchdbManager::getClient('CSV')->countCSVsAcheteurs();

        $this->etapeDrValidee = $dr_validees->rows[0]->value;
        $this->etapeDrNonValidee = $dr_non_validees->rows[0]->value;

        if (isset($dr_non_validees_etapes_exploitation->rows) && count($dr_non_validees_etapes_exploitation->rows) > 0) {
            $this->etapeExploitation = $dr_non_validees_etapes_exploitation->rows[0]->value;
        }
        if (isset($dr_non_validees_etapes_repartition->rows) && count($dr_non_validees_etapes_repartition->rows) > 0) {
            $this->etapeRepartition = $dr_non_validees_etapes_repartition->rows[0]->value;
        }
        if (isset($dr_non_validees_etapes_recolte->rows) && count($dr_non_validees_etapes_recolte->rows) > 0) {
            $this->etapeRecolte = $dr_non_validees_etapes_recolte->rows[0]->value;
        }
        if (isset($dr_non_validees_etapes_validation->rows) && count($dr_non_validees_etapes_validation->rows) > 0) {
            $this->etapeValidation = $dr_non_validees_etapes_validation->rows[0]->value;
        }

        $drs = acCouchdbManager::getClient()->reduce(false)
                                              ->startkey(array($campagne, true, true))
                                              ->endkey(array($campagne, true, true, array()))
                                              ->getView("STATS", "DR");
        foreach($drs->rows as $dr) {
          if (isset($dr->key[5])) {
            $this->drPapier += 1;
          } else {
            $this->drTeledeclare += 1;
          }
        }

        $utilisateurs_edition = acCouchdbManager::getClient()->group(true)
                                              ->group_level(2)
                                              ->startkey(array($campagne))
                                              ->endkey(array($campagne, array()))
                                              ->getView("STATS", "edition_dr");

        $this->utilisateurs_edition_dr = array();
        foreach ($utilisateurs_edition->rows as $u) {
                      if(preg_match('/civa/', $u->key[1])) {
                          $this->utilisateurs_edition_dr[$u->key[1]] = $u->value;
                      }
        }

        arsort($this->utilisateurs_edition_dr);
        $utilisateurs_validation = acCouchdbManager::getClient()->group(true)
                                              ->group_level(2)
                                              ->startkey(array($campagne))
                                              ->endkey(array($campagne, array()))
                                              ->getView("STATS", "validation_dr");

        foreach ($utilisateurs_validation->rows as $u) {
          if(preg_match('/^COMPTE-auto$/', $u->key[1])) {
              $this->drAuto = $u->value;
          }
        }

        $this->drTeledeclare = $this->drTeledeclare - $this->drAuto;

    }


    protected function processStatsDS($type_ds) {
        $periode = CurrentClient::getCurrent()->getPeriodeDS();


        $this->etapeDsValidee[$type_ds] = 0;
        $this->etapeDsNonValidee[$type_ds] = 0;
        $this->etapeDsNonValideeEtapes[$type_ds] = array();

        $ds_validees = acCouchdbManager::getClient()->group(true)
                                              ->group_level(4)
                                              ->startkey(array($type_ds, $periode, true, true))
                                              ->endkey(array($type_ds, $periode, true, true, array()))
                                              ->getView("STATS", "DS");

        $ds_non_validees = acCouchdbManager::getClient()->group(true)
                                              ->group_level(4)
                                              ->startkey(array($type_ds, $periode, false, false))
                                              ->endkey(array($type_ds, $periode, false, false, array()))
                                              ->getView("STATS", "DS");

        foreach(DSCivaClient::$etapes as $num => $libelle) {
            $ds_non_validees_etape = acCouchdbManager::getClient()->group(true)
                                              ->group_level(6)
                                              ->startkey(array($type_ds, $periode, false, false, null, $num))
                                              ->endkey(array($type_ds, $periode, false, false, null, $num, array()))
                                              ->getView("STATS", "DS");

            $this->etapeDsNonValideeEtapes[$type_ds][$libelle] = 0;
            if (isset($ds_non_validees_etape->rows) && count($ds_non_validees_etape->rows) > 0) {
                $this->etapeDsNonValideeEtapes[$type_ds][$libelle] = $ds_non_validees_etape->rows[0]->value;
            }
        }

        if (isset($ds_validees->rows) && count($ds_validees->rows) > 0) {
          $this->etapeDsValidee[$type_ds] = $ds_validees->rows[0]->value;
        }

        if (isset($ds_non_validees->rows) && count($ds_non_validees->rows) > 0) {
          $this->etapeDsNonValidee[$type_ds] = $ds_non_validees->rows[0]->value;
        }

        $utilisateurs_edition = acCouchdbManager::getClient()->group(true)
                                              ->group_level(3)
                                              ->startkey(array($type_ds, $periode))
                                              ->endkey(array($type_ds, $periode, array()))
                                              ->getView("STATS", "edition_ds");

        $this->utilisateurs_edition_ds[$type_ds] = array();
        $cpt = 0;
        foreach ($utilisateurs_edition->rows as $u) {
                      if(!preg_match('/^COMPTE-C?[0-9]{10}/', $u->key[2])) {
                          $this->utilisateurs_edition_ds[$type_ds][$u->key[2]] = $u->value;
                      }
        }
        arsort($this->utilisateurs_edition_ds[$type_ds]);
    }
}
