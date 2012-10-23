<?php

/**
 * Description of ExportDRAcheteursCsv
 *
 * @author vince
 */
class ExportUserDRValideeCsv extends ExportCsv {

    protected $_headers = array(
        "cvi_acheteur" => "CVI acheteur",
        "nom_acheteur" => "nom acheteur",
        "cvi_recoltant" => "CVI récoltant",
        "nom_recoltant" => "nom récoltant",
        "appellation" => "appellation",
        "lieu" => "lieu",
        "cepage" => "cépage",
        "vtsgn" => "vtsgn",
        "denomination" => "dénomination",
        "superficie_livree" => "superficie livrée",
        "volume_livre" => "volume livré",
        "dont_dplc" => "dont dplc",
        "superficie_totale" => "superficie totale",
        "volume_total" => "volume total",
        "dplc_total" => "dplc total",
        "creation_date" => "date de création",
        "validation_date" => "date de validation",
        "validation_user" => "validateur",
    );
    protected $_validation_detail_acheteur = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => false),
        "appellation" => array("type" => "string", "required" => true),
        "lieu" => array("type" => "string", "required" => false),
        "cepage" => array("type" => "string", "required" => true),
        "vtsgn" => array("type" => "string", "required" => false),
        "denomination" => array("type" => "string", "required" => false),
        "superficie_livree" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "volume_livre" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "dont_dplc" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "superficie_totale" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "creation_date" => array("type" => "string", "required" => false),
        "volume_total" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "dplc_total" => array("type" => "float", "format" => "%01.02f", "required" => false),
    );
    protected $_validation_detail_total = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => false),
        "appellation" => array("type" => "string", "required" => true),
        "lieu" => array("type" => "string", "required" => false),
        "cepage" => array("type" => "string", "required" => true),
        "vtsgn" => array("type" => "string", "required" => false),
        "denomination" => array("type" => "string", "required" => false),
        "superficie_livree" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "volume_livre" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "dont_dplc" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "superficie_totale" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "creation_date" => array("type" => "string", "required" => false),
        "volume_total" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "dplc_total" => array("type" => "float", "format" => "%01.02f", "required" => false),
    );
    protected $_validation_lieu_acheteur = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => false),
        "appellation" => array("type" => "string", "required" => true),
        "lieu" => array("type" => "string", "required" => false),
        "cepage" => array("type" => "string", "required" => true),
        "vtsgn" => array("type" => "string", "required" => false),
        "denomination" => array("type" => "string", "required" => false),
        "superficie_livree" => array("type" => "float", "format" => "%01.02f", "required" => true, "default" => true),
        "volume_livre" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "dont_dplc" => array("type" => "float", "format" => "%01.02f", "required" => true, "default" => true),
        "superficie_totale" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "creation_date" => array("type" => "string", "required" => false),
        "volume_total" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "dplc_total" => array("type" => "float", "format" => "%01.02f", "required" => true),
    );
    protected $_validation_lieu_total = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => false),
        "appellation" => array("type" => "string", "required" => true),
        "lieu" => array("type" => "string", "required" => false),
        "cepage" => array("type" => "string", "required" => true),
        "vtsgn" => array("type" => "string", "required" => false),
        "denomination" => array("type" => "string", "required" => false),
        "superficie_livree" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "volume_livre" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "dont_dplc" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "superficie_totale" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "creation_date" => array("type" => "string", "required" => false),
        "volume_total" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "dplc_total" => array("type" => "float", "format" => "%01.02f", "required" => true),
    );
    protected $_validation_jeunes_vignes = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => false),
        "appellation" => array("type" => "string", "required" => true),
        "lieu" => array("type" => "string", "required" => false),
        "cepage" => array("type" => "string", "required" => false),
        "vtsgn" => array("type" => "string", "required" => false),
        "denomination" => array("type" => "string", "required" => false),
        "superficie_livree" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "volume_livre" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "dont_dplc" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "superficie_totale" => array("type" => "float", "format" => "%01.02f", "required" => true),
        "creation_date" => array("type" => "string", "required" => false),
        "volume_total" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "dplc_total" => array("type" => "float", "format" => "%01.02f", "required" => false),
    );

    protected $_acheteur = null;
    protected $_debug = false;
    protected $_ids_dr = null;
    protected $_campagne = null;

    /**
     *
     * @param string $campagne 
     */
    public function __construct($campagne, $acheteur_or_cvi, $debug = false) {
        parent::__construct($this->_headers);
        $this->_debug = $debug;
        $this->_campagne = $campagne;
        if ($acheteur_or_cvi instanceof Acheteur) {
            $this->_acheteur = $acheteur_or_cvi;
        } else {
            $this->_acheteur = sfCouchdbManager::getClient("Acheteur")->retrieveByCvi($acheteur_or_cvi);
        }
        if (!$this->_acheteur) {
            throw new sfException("Acheteur not find");
        }
        $drs = sfCouchdbManager::getClient("DR")->findAllByCampagneAndCviAcheteur($this->_campagne, $this->_acheteur->cvi, sfCouchdbClient::HYDRATE_JSON);

        $this->_ids_dr = $drs->getIds();
    }
    
    public function add($data, $validation = array()) {
        $line = parent::add($data, $validation);
        if ($this->_debug) {
            echo $line;
        }
        return $line;
    }

    public function export() {
        foreach ($this->_ids_dr as $id_dr) {
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($id_dr);
            if(!is_null($dr->validee) )
            {
                $this->dr = $dr;
                if (substr($dr->cvi, 0, 1) == "6") {
                    if ($this->_debug) {
                        echo "\n\n ------------ \n" . $dr->get('_id') . "\n ----------- \n";
                    }
                    foreach ($dr->recolte->getNoeudAppellations()->appellations as $appellation) {
                        foreach ($appellation->getLieux() as $lieu) {
                            foreach($lieu->getCouleurs() as $couleur) {
                                foreach ($couleur->getCepages() as $cepage) {
                                    foreach ($cepage->getDetail() as $detail) {
                                        $added = false;
                                        foreach ($detail->filter('negoces|cooperatives') as $acheteurs) {
                                            foreach ($acheteurs as $acheteur) {
                                                if ($acheteur->cvi == $this->_acheteur->cvi) {
                                                    $this->addDetailAcheteur($acheteur);
                                                    $added = true;
                                                }
                                            }
                                        }

                                        if (!$added) {
                                            $this->addDetailTotal($detail);
                                        }
                                    }
                                }$this->addLieuTotal($lieu);
                            }
                        }$this->addJeunesVignes($dr);
                    }
                }unset($dr);
            }
        }
        if ($this->_debug) {
            echo "------------ \n" . count($this->_ids_dr) . " DRs \n ------------\n";
        }
    }



    protected function addLieuTotal(DRRecolteLieu $lieu) {
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $lieu->getCouchdbDocument()->cvi,
            "nom_recoltant" => $lieu->getCouchdbDocument()->declarant->nom,
            "appellation" => $lieu->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $lieu->getConfig()->getLibelle(),
            "cepage" => "TOTAL",
            "vtsgn" => null,
            "denomination" => null,
            "superficie_livree" => null,
            "volume_livre" => null,
            "dont_dplc" => null,
            "superficie_totale" => $lieu->getTotalSuperficie(),
            "volume_total" => $lieu->getTotalVolume(),
            "dplc_total" => $lieu->getDplc(),
            "creation_date" => $this->dr->getPremiereModificationDr(),
            "validation_date" => $lieu->getCouchdbDocument()->validee,
            "validation_user" => $this->getValidationUser($lieu->getCouchdbDocument()),
        ), $this->_validation_lieu_total);
    }

    protected function addJeunesVignes(DR $dr) {
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $dr->cvi,
            "nom_recoltant" => $dr->declarant->nom,
            "appellation" => "Jeunes Vignes",
            "lieu" => null,
            "cepage" => null,
            "vtsgn" => null,
            "denomination" => null,
            "superficie_livree" => null,
            "volume_livre" => null,
            "dont_dplc" => null,
            "superficie_totale" => $dr->jeunes_vignes,
            "volume_total" => null,
            "dplc_total" => null,
            "creation_date" => $this->dr->getPremiereModificationDr(),
            "validation_date" => $dr->validee,
            "validation_user" => $this->getValidationUser($dr),
        ), $this->_validation_jeunes_vignes);
    }


    protected function addDetailAcheteur($acheteur) {
        $detail = $acheteur->getParent()->getParent();
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getConfig()->hasLieuEditable() ? $detail->lieu : $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->getConfig()->hasDenomination() ? $detail->denomination : null,
            "superficie_livree" => (($detail->volume == $acheteur->quantite_vendue) ? $detail->superficie : null),
            "volume_livre" => $acheteur->quantite_vendue,
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "dplc_total" => null,
            "creation_date" => $this->dr->getPremiereModificationDr(),
            "validation_date" => $detail->getCouchdbDocument()->validee,
            "validation_user" => $this->getValidationUser($detail->getCouchdbDocument()),
        ), $this->_validation_detail_acheteur);
    }

    protected function addDetailTotal($detail) {
        $lieu = "";
        $denomination = "";

        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getConfig()->hasLieuEditable() ? $detail->lieu : $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->getConfig()->hasDenomination() ? $detail->denomination : null,
            "superficie_livree" => null,
            "volume_livre" => null,
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "dplc_total" => null,
            "creation_date" =>  $this->dr->getPremiereModificationDr(),
            "validation_date" => $detail->getCouchdbDocument()->validee,
            "validation_user" => $this->getValidationUser($detail->getCouchdbDocument()),
                ), $this->_validation_detail_total);
    }

    protected function calculMd5($drs) {
        $revisions = "";
        foreach ($drs as $id => $dr) {
            if (substr($id, 0, 4) == "DR-6") {
                $revisions .= $dr->_rev;
            }
            unset($dr);
        }
        if ($revisions) {
            return md5($revisions);
        } else {
            return null;
        }
    }
    
    private function getValidationUser($dr) {
        $user = null;
        if ($dr->exist('utilisateurs')) {
            foreach($dr->utilisateurs->validation as $compte => $date_fr) {
                if (preg_match('/^COMPTE-[0-9]+$/', $compte)) {
                    $user = "Récoltant";
                } elseif(preg_match('/^COMPTE-.*civa.*$/', $compte)) {
                    $user = "CIVA";
                } elseif(!preg_match('/^COMPTE-/', $compte)) {
                    $user = $compte;
                }
            }
        }
        if (!$user && strtotime($dr->validee) > strtotime($this->_campagne.'-12-10')) {
            $user = 'Automatique';
        } elseif(!$user) {
            $user = 'Récoltant';
        }

        return $user;
    }



}