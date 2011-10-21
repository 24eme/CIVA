<?php

/**
 * Description of ExportDRAcheteursCsv
 *
 * @author vince
 */
class ExportDRAcheteurCsv extends ExportCsv {

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
        "volume_total" => array("type" => "float", "format" => "%01.02f", "required" => false),
        "dplc_total" => array("type" => "float", "format" => "%01.02f", "required" => false),
    );
    protected $_acheteur = null;
    protected $_debug = false;
    protected $_md5 = null;
    protected $_has_dr = null;

    /**
     *
     * @param string $campagne 
     */
    public function __construct($campagne, $acheteur_or_cvi, $debug = false) {
        parent::__construct($this->_headers);
        $this->_debug = $debug;
        if ($acheteur_or_cvi instanceof Acheteur) {
            $this->_acheteur = $acheteur_or_cvi;
        } else {
            $this->_acheteur = sfCouchdbManager::getClient("Acheteur")->retrieveByCvi($acheteur_or_cvi);
        }
        if (!$this->_acheteur) {
            throw new sfException("Acheteur not find");
        }

        $dr_ids = sfCouchdbManager::getClient("DR")->findAllByCampagneAndCviAcheteur($campagne, $this->_acheteur->cvi, sfCouchdbClient::HYDRATE_JSON)->getIds();
        $this->load($dr_ids, $campagne);
        $this->_has_dr = (count($dr_ids) > 0);
    }

    protected function load($dr_ids, $campagne) {
        $revisions = "";
        $this->_md5 = null;
        foreach ($dr_ids as $dr_id) {
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($dr_id);
            if (substr($dr->cvi, 0, 1) == "6") {
                if ($this->_debug) {
                    echo "\n\n ------------ \n" . $dr->get('_id') . "\n ----------- \n";
                }
                foreach ($dr->recolte->appellations as $appellation) {
                    foreach ($appellation->getLieux() as $lieu) {
                        foreach($lieu->getCouleurs() as $couleur) {
                            foreach ($couleur->getCepages() as $cepage) {
                                foreach ($cepage->getDetail() as $detail) {
                                    foreach ($detail->filter('negoces|cooperatives|mouts') as $acheteurs) {
                                        foreach ($acheteurs as $acheteur) {
                                            if ($acheteur->cvi == $this->_acheteur->cvi) {
                                                $this->addDetailAcheteur($acheteur);
                                            }
                                        }
                                    }
                                    $this->addDetailTotal($detail);
                                }
                            }
                        }
                        foreach ($lieu->acheteurs as $acheteurs) {
                            foreach ($acheteurs as $cvi_a => $acheteur) {
                                if ($cvi_a == $this->_acheteur->cvi) {
                                    $this->addLieuAcheteur($acheteur);
                                }
                            }
                        }
                        $this->addLieuTotal($lieu);
                    }
                }
                $this->addJeunesVignes($dr);
                $revisions .= $dr->get('_rev');
            }
            unset($dr);
        }
        if ($this->_debug) {
            echo "------------ \n" . count($dr_ids) . " DRs \n ------------\n";
        }
        if ($revisions) {
            $this->_md5 = md5($revisions);
        }
    }

    protected function addDetailAcheteur($acheteur) {
        $detail = $acheteur->getParent()->getParent();
        
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->denomination,
            "superficie_livree" => (($detail->volume == $acheteur->quantite_vendue) ? $detail->superficie : null),
            "volume_livre" => $acheteur->quantite_vendue,
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "dplc_total" => null,
                ), $this->_validation_detail_acheteur);
    }

    protected function addDetailTotal($detail) {
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->denomination,
            "superficie_livree" => null,
            "volume_livre" => null,
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "dplc_total" => null,
                ), $this->_validation_detail_total);
    }

    protected function addLieuAcheteur(DRRecolteLieuAcheteur $acheteur) {
        $lieu = $acheteur->getLieu();
        $this->add(array(
            "cvi_acheteur" => $this->_acheteur->cvi,
            "nom_acheteur" => $this->_acheteur->nom,
            "cvi_recoltant" => $acheteur->getCouchdbDocument()->cvi,
            "nom_recoltant" => $acheteur->getCouchdbDocument()->declarant->nom,
            "appellation" => $lieu->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $lieu->getConfig()->getLibelle(),
            "cepage" => "TOTAL",
            "vtsgn" => null,
            "denomination" => null,
            "superficie_livree" => $acheteur->superficie,
            "volume_livre" => $acheteur->getVolume(),
            "dont_dplc" => $acheteur->dontdplc,
            "superficie_totale" => $lieu->getTotalSuperficie(),
            "volume_total" => $lieu->getTotalVolume(),
            "dplc_total" => $lieu->getDplc(),
                ), $this->_validation_lieu_acheteur);
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
                ), $this->_validation_jeunes_vignes);
    }

    public function add($data, $validation = array()) {
        $line = parent::add($data, $validation);
        if ($this->_debug) {
            echo $line;
        }
        return $line;
    }

    public function getMd5() {
        return $this->_md5;
    }
    
    public function hasDR() {
        return $this->_has_dr;
    }

    public static function calculMd5($campagne, $cvi_acheteur) {
        $revisions = "";
        $drs = sfCouchdbManager::getClient("DR")->findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, sfCouchdbClient::HYDRATE_JSON);
        foreach ($drs as $dr) {
            if (substr($dr->cvi, 0, 1) == "6") {
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

}