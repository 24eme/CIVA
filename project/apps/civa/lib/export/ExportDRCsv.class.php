<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class ExportDRCsv
 * @author mathurin
 */
class ExportDRCsv extends ExportCsv {

    public static $_headers = array(
        "cvi_acheteur" => "CVI acheteur",
        "nom_acheteur" => "nom acheteur",
        "cvi_recoltant" => "CVI récoltant",
        "nom_recoltant" => "nom récoltant",
        "appellation" => "appellation",
        "lieu" => "lieu",
        "cepage" => "cépage",
        "vtsgn" => "vtsgn",
        "denomination" => "dénomination",
        "superficie_livree" => "superficie",
        "volume_livre/sur place" => "volume",
        "dont_dplc" => "dont volume à détruire",
        "superficie_totale" => "superficie totale",
        "volume_total" => "volume total",
        "volume_a_detruire_total" => "volume à détruire total",
        "dont_vci" => "dont VCI",
        "vci_total" => "VCI total",
        "validation_date" => "date de validation / dépot",
        "modification_date" => "date de modification",
        "validation_user" => "validateur",
        "hash" => "hash_produit",
        "type" => "type_ligne",
    );
    protected $_validation_ligne = array(
        "cvi_acheteur" => array("type" => "string"),
        "nom_acheteur" => array("type" => "string"),
        "cvi_recoltant" => array("type" => "string"),
        "nom_recoltant" => array("type" => "string"),
        "appellation" => array("type" => "string"),
        "lieu" => array("type" => "string"),
        "cepage" => array("type" => "string"),
        "vtsgn" => array("type" => "string"),
        "denomination" => array("type" => "string"),
        "superficie_livree" => array("type" => "float", "format" => "%01.02f"),
        "volume_livre/sur place" => array("type" => "float", "format" => "%01.02f"),
        "dont_dplc" => array("type" => "float", "format" => "%01.02f"),
        "superficie_totale" => array("type" => "float", "format" => "%01.02f"),
        "volume_total" => array("type" => "float", "format" => "%01.02f"),
        "volume_a_detruire_total" => array("type" => "float", "format" => "%01.02f"),
        "dont_vci" => array("type" => "float", "format" => "%01.02f"),
        "vci_total" => array("type" => "float", "format" => "%01.02f"),
        "validation_date" => array("type" => "string"),
        "modification_date" => array("type" => "string"),
        "validation_user" => array("type" => "string"),
        "hash" => array("type" => "string"),
        "type" => array("type" => "string"),
    );

    protected $_acheteur = null;
    protected $_debug = false;
    protected $_md5 = null;
    protected $_campagne = null;
    protected $dr = null;

    /**
     *
     * @param string $campagne
     */
    public function __construct($campagne, $cvi, $with_header = true, $debug = false) {
        $this->_debug = $debug;
        $this->_campagne = $campagne;
        $this->dr = acCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($cvi,$campagne);
        if (!$this->dr) {
            throw new sfException("DR not find");
        }

        if($with_header) {
            parent::__construct(self::$_headers);
        }

        $this->_md5 = $this->calculMd5($this->dr);
    }

    public function add($data, $validation = array()) {
        if(!$this->dr->recolte->canHaveVci()) {
            unset($data['dont_vci']);
            unset($data['vci_total']);
        }

        $line = parent::add($data, $validation);
        if ($this->_debug) {
            echo $line;
        }
        return $line;
    }

    public function getMd5() {
        return $this->_md5;
    }

    public function export() {
        $dr = $this->dr;

        if ($this->_debug) {
           echo "\n\n ------------ \n" . $dr->get('_id') . "\n ----------- \n";
        }

        foreach ($dr->recolte->getAppellations() as $appellation) {
            foreach($appellation->getMentions() as $mention) {
                foreach ($mention->getLieux() as $lieu) {
                    foreach($lieu->getCouleurs() as $couleur) {
                        foreach ($couleur->getCepages() as $cepage) {
                            foreach ($cepage->getDetail() as $detail) {
                                if($detail->cave_particuliere > 0) {
                                    $this->addDetailTotal($detail);
                                }
                                foreach ($detail->filter('negoces|cooperatives|mouts') as $acheteurs) {
                                    foreach ($acheteurs as $acheteur) {
                                            $this->addDetailAcheteur($acheteur);
                                    }
                                }
                                if($detail->exist('motif_non_recolte') && $detail->motif_non_recolte) {
                                    $this->addDetailNonRecolte($detail);
                                }
                            }
                        }
                        if($couleur->getKey() != "couleur") {
                            $this->addNoeudTotal($couleur);
                        }
                    }

                    if($appellation->getConfig()->hasManyLieu()) {
                        $this->addNoeudTotal($lieu);
                    }

                    foreach ($lieu->acheteurs as $acheteurs) {
                        foreach ($acheteurs as $cvi_a => $acheteur) {
                                $this->addNoeudAcheteur($lieu, $acheteur);
                        }
                    }
                }
                $this->addNoeudTotal($mention);
            }
        }
        $this->addJeunesVignes($dr);
        $this->addJusRaisin($dr);

        if ($this->_debug) {
            echo "------------ \n" . count($this->_ids_dr) . " DRs \n ------------\n";
        }
    }

    protected function addDetailAcheteur($acheteur) {
        $detail = $acheteur->getParent()->getParent();

        $acheteurObject =  EtablissementClient::getInstance()->findByCvi($acheteur->cvi, acCouchdbClient::HYDRATE_JSON);

        $this->add(array(
            "cvi_acheteur" => $acheteur->cvi,
            "nom_acheteur" => $acheteurObject->nom,
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getConfig()->hasLieuEditable() ? $detail->lieu : $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->getConfig()->hasDenomination() ? $detail->denomination : null,
            "superficie_livree" => (($detail->volume == $acheteur->quantite_vendue) ? $detail->superficie : null),
            "volume_livre/sur place" => $acheteur->quantite_vendue,
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "volume_a_detruire_total" => $detail->usages_industriels,
            "dont_vci" => null,
            "vci_total" => ($detail->exist('vci') && $detail->vci > 0) ? $detail->vci : null,
            "validation_date" => $this->getValidationDate($detail->getCouchdbDocument()),
            "modification_date" => $this->getModificationDate($detail->getCouchdbDocument()),
            "validation_user" => $this->getValidationUser($detail->getCouchdbDocument()),
            "hash" => $detail->getHash(),
            "detail_vente_".$acheteur->getParent()->getKey(),
                ), $this->_validation_ligne);
    }

    protected function addDetailNonRecolte($detail) {
        $lieu = "";
        $denomination = "";

        $this->add(array(
            "cvi_acheteur" => "MOTIF ".$detail->motif_non_recolte,
            "nom_acheteur" => "NON RECOLTE",
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getConfig()->hasLieuEditable() ? $detail->lieu : $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->getConfig()->hasDenomination() ? $detail->denomination : null,
            "superficie_livree" => null,
            "volume_livre/sur place" => $detail->getTotalCaveParticuliere(),
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "volume_a_detruire_total" => $detail->usages_industriels,
            "dont_vci" => null,
            "vci_total" => ($detail->exist('vci') && $detail->vci > 0) ? $detail->vci : null,
            "validation_date" => $this->getValidationDate($detail->getCouchdbDocument()),
            "modification_date" => $this->getModificationDate($detail->getCouchdbDocument()),
            "validation_user" => $this->getValidationUser($detail->getCouchdbDocument()),
            "hash" => $detail->getHash(),
            "type" => "detail_motif"
                ), $this->_validation_ligne);
    }

    protected function addDetailTotal($detail) {
        $lieu = "";
        $denomination = "";

        $this->add(array(
            "cvi_acheteur" => $detail->getCouchdbDocument()->cvi,
            "nom_acheteur" => "SUR PLACE",
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getConfig()->hasLieuEditable() ? $detail->lieu : $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->getConfig()->hasDenomination() ? $detail->denomination : null,
            "superficie_livree" => null,
            "volume_livre/sur place" => $detail->getTotalCaveParticuliere(),
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "volume_a_detruire_total" => $detail->usages_industriels,
            "dont_vci" => null,
            "vci_total" => ($detail->exist('vci') && $detail->vci > 0) ? $detail->vci : null,
            "validation_date" => $this->getValidationDate($detail->getCouchdbDocument()),
            "modification_date" => $this->getModificationDate($detail->getCouchdbDocument()),
            "validation_user" => $this->getValidationUser($detail->getCouchdbDocument()),
            "hash" => $detail->getHash(),
            "type" => "detail_cave_particuliere"
                ), $this->_validation_ligne);
    }

    protected function addNoeudAcheteur($noeud, $acheteur) {
        $vtsgn = str_replace("mention", "", $noeud->getMention()->getKey());

        $this->add(array(
            "cvi_acheteur" => $acheteur->cvi,
            "nom_acheteur" => $acheteur->getNom(),
            "cvi_recoltant" => $acheteur->getCouchdbDocument()->cvi,
            "nom_recoltant" => $acheteur->getCouchdbDocument()->declarant->nom,
            "appellation" => $noeud->getAppellation()->getConfig()->getLibelle(),
            "lieu" => ($noeud instanceof DRRecolteLieu) ? $noeud->getConfig()->getLibelle() : $noeud->getLieu()->getConfig()->getLibelle(),
            "cepage" => "TOTAL",
            "vtsgn" => $vtsgn,
            "denomination" => null,
            "superficie_livree" => $acheteur->superficie,
            "volume_livre/sur place" => $acheteur->getVolume(),
            "dont_dplc" => $acheteur->dontdplc,
            "superficie_totale" => $noeud->getTotalSuperficie(),
            "volume_total" => $noeud->getTotalVolume(),
            "volume_a_detruire_total" => $noeud->getUsagesIndustriels(),
            "dont_vci" => $acheteur->exist('dontvci') && $acheteur->dontvci > 0 ? $acheteur->dontvci : null,
            "vci_total" => ($noeud->getTotalVci() > 0) ? $noeud->getTotalVci() : null,
            "validation_date" => $this->getValidationDate($acheteur->getCouchdbDocument()),
            "modification_date" => $this->getModificationDate($acheteur->getCouchdbDocument()),
            "validation_user" => $this->getValidationUser($acheteur->getCouchdbDocument()),
            "hash" => $noeud->getHash(),
            "type" => "total_vente_".$acheteur->getParent()->getKey(),
                ), $this->_validation_ligne);
    }

    protected function addNoeudTotal($noeud) {
        if($noeud instanceof DRRecolteCouleur && !$noeud->getAppellation()->getConfig()->hasManyLieu()) {
            $lieu = "TOTAL ".$noeud->getConfig()->getLibelle();
            $cepage = "";
        }

        if($noeud instanceof DRRecolteCouleur && $noeud->getAppellation()->getConfig()->hasManyLieu()) {
            $lieu = $noeud->getLieu()->getConfig()->getLibelle();
            $cepage = "TOTAL ".$noeud->getConfig()->getLibelle();
        }

        if($noeud instanceof DRRecolteLieu) {
            $lieu = $noeud->getConfig()->getLibelle();
            $cepage = "TOTAL";
        }

        if($noeud instanceof DRRecolteMention) {
            $lieu = "TOTAL";
            $cepage = "";
        }
        $vtsgn = str_replace("mention", "", $noeud->getMention()->getKey());

        $this->add(array(
            "cvi_acheteur" => $noeud->getCouchdbDocument()->cvi,
            "nom_acheteur" => "SUR PLACE",
            "cvi_recoltant" => $noeud->getCouchdbDocument()->cvi,
            "nom_recoltant" => $noeud->getCouchdbDocument()->declarant->nom,
            "appellation" => ($noeud instanceof DRRecolteAppellation) ? $noeud->getConfig()->getLibelle() : $noeud->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $lieu,
            "cepage" => $cepage,
            "vtsgn" => $vtsgn,
            "denomination" => null,
            "superficie_livree" => ($noeud->canCalculSuperficieSurPlace()) ? $noeud->getSuperficieCaveParticuliere() : null,
            "volume_livre/sur place" => $noeud->getTotalCaveParticuliere(),
            "dont_dplc" => ($noeud->canCalculVolumeRevendiqueSurPlace()) ? $noeud->getUsagesIndustrielsSurPlace() : null,
            "superficie_totale" => $noeud->getTotalSuperficie(),
            "volume_total" => $noeud->getTotalVolume(),
            "volume_a_detruire_total" => $noeud->getUsagesIndustriels(),
            "dont_vci" => ($noeud->getVciCaveParticuliere() > 0) ? $noeud->getVciCaveParticuliere() : null,
            "vci_total" => ($noeud->getTotalVci()) ? $noeud->getTotalVci() : null,
            "validation_date" => $this->getValidationDate($noeud->getCouchdbDocument()),
            "modification_date" => $this->getModificationDate($noeud->getCouchdbDocument()),
            "validation_user" => $this->getValidationUser($noeud->getCouchdbDocument()),
            "hash" => $noeud->getHash(),
            "type" => "total_cave_particuliere",
                ), $this->_validation_ligne);
    }

    protected function addJeunesVignes(DR $dr) {
        $this->add(array(
            "cvi_acheteur" => $dr->cvi,
            "nom_acheteur" => "SUR PLACE",
            "cvi_recoltant" => $dr->cvi,
            "nom_recoltant" => $dr->declarant->nom,
            "appellation" => "Jeunes Vignes",
            "lieu" => null,
            "cepage" => null,
            "vtsgn" => null,
            "denomination" => null,
            "superficie_livree" => $dr->jeunes_vignes,
            "volume_livre/sur place" => null,
            "dont_dplc" => null,
            "superficie_totale" => $dr->jeunes_vignes,
            "volume_total" => null,
            "volume_a_detruire_total" => null,
            "dont_vci" => null,
            "vci_total" => null,
            "validation_date" => $this->getValidationDate($dr),
            "modification_date" => $this->getModificationDate($dr),
            "validation_user" => $this->getValidationUser($dr),
            "hash" => "/jeunes_vignes",
            "type" => "annexe",
                ), $this->_validation_ligne);
    }

    protected function addJusRaisin(DR $dr) {
        if(!$dr->exist('jus_raisin_superficie') || !$dr->exist('jus_raisin_volume')) {
            return;
        }

        if($dr->jus_raisin_superficie === null && $dr->jus_raisin_volume === null) {
            return;
        }

        $this->add(array(
            "cvi_acheteur" => $dr->cvi,
            "nom_acheteur" => "SUR PLACE",
            "cvi_recoltant" => $dr->cvi,
            "nom_recoltant" => $dr->declarant->nom,
            "appellation" => "Jus de raisin",
            "lieu" => null,
            "cepage" => null,
            "vtsgn" => null,
            "denomination" => null,
            "superficie_livree" => $dr->jus_raisin_superficie,
            "volume_livre/sur place" => $dr->jus_raisin_volume,
            "dont_dplc" => null,
            "superficie_totale" => $dr->jus_raisin_superficie,
            "volume_total" => $dr->jus_raisin_volume,
            "volume_a_detruire_total" => null,
            "dont_vci" => null,
            "vci_total" => null,
            "validation_date" => $this->getValidationDate($dr),
            "modification_date" => $this->getModificationDate($dr),
            "validation_user" => $this->getValidationUser($dr),
            "hash" => "/jus_raisin",
            "type" => "annexe",
                ), $this->_validation_ligne);
    }


    protected function calculMd5($dr) {
       return $dr->_rev;
    }

    private function getValidationDate($dr) {

        if($dr->exist('date_depot_mairie') && $dr->date_depot_mairie) {

            return $dr->date_depot_mairie;
        }

        return $dr->validee;
    }

    private function getModificationDate($dr) {

        if($dr->validee == $dr->modifiee) {

            return $this->getValidationDate($dr);
        }

        return $dr->modifiee;
    }

    private function getValidationUser($dr) {
        $user = null;

        if($dr->exist('date_depot_mairie') && $dr->get('date_depot_mairie')) {

            return 'CIVA';
        }

        if($dr->exist('validee_par') && $dr->validee_par == DRClient::VALIDEE_PAR_AUTO) {

            return "Automatique";
        }

        if ($dr->exist('utilisateurs')) {
            foreach($dr->utilisateurs->validation as $compte => $date_fr) {
                if (preg_match('/^COMPTE-auto$/', $compte)) {

                   return "Automatique";
                }

                if (preg_match('/^COMPTE-C?[0-9]+$/', $compte)) {
                    $user = "Récoltant";
                    break;
                } elseif(preg_match('/^COMPTE-.*civa.*$/', $compte)) {
                    $user = "CIVA";
                    break;
                } elseif(!preg_match('/^COMPTE-/', $compte)) {
                    $user = $compte;
                    break;
                }
            }
        }

        if ((!$user || $user == "CIVA") && strtotime($dr->validee) >= strtotime($this->_campagne.'-12-10') && strtotime($dr->modifiee) >= strtotime($this->_campagne.'-12-10')) {
            $user = 'Automatique';
        }

        if(!$user) {
            $user = 'Inconnu';
        }

        return $user;
    }

}
