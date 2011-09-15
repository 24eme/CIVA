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
    protected $_validation_detail = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => true),
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
    protected $_validation_total = array(
        "cvi_acheteur" => array("type" => "double", "required" => true),
        "nom_acheteur" => array("type" => "string", "required" => true),
        "cvi_recoltant" => array("type" => "double", "required" => true),
        "nom_recoltant" => array("type" => "string", "required" => true),
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
    protected $_debug = false;
    
    protected $_md5 = null;

    /**
     *
     * @param string $campagne 
     */
    public function __construct($campagne, $cvi_acheteur, $debug = false) {
        parent::__construct($this->_headers);
        $this->_debug = $debug;
        $drs = sfCouchdbManager::getClient("DR")->findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, sfCouchdbClient::HYDRATE_ON_DEMAND_WITH_DATA);
        $this->load($drs, $campagne, $cvi_acheteur);
    }
    
    protected function load($drs, $campagne, $cvi_acheteur = null) {
        $revisions = "";
        $this->_md5 = null;
        foreach ($drs as $dr) {
            if (substr($dr->cvi, 0, 1) == "6") {
                if ($this->_debug) {
                    echo "\n\n ------------ \n" . $dr->get('_id') . "\n ----------- \n";
                }
                foreach ($dr->recolte->appellations as $appellation) {
                    foreach ($appellation->getLieux() as $lieu) {
                        foreach ($lieu->getCepages() as $cepage) {
                            foreach ($cepage->getDetail() as $detail) {
                                foreach ($detail->filter('negoces|cooperatives|mouts') as $acheteurs) {
                                    foreach ($acheteurs as $acheteur) {
                                        if (is_null($cvi_acheteur) || $acheteur->cvi == $cvi_acheteur) {
                                            $this->addDetail($acheteur->cvi, $acheteur);
                                        }
                                    }
                                }
                            }
                        }
                        foreach ($lieu->acheteurs as $acheteurs) {
                            foreach ($acheteurs as $cvi_a => $acheteur) {
                                if(is_null($cvi_acheteur) || $cvi_a == $cvi_acheteur) {
                                    $this->addTotal($cvi_a, $acheteur);
                                }
                            }
                        }
                    }
                }
                $revisions .= $dr->get('_rev');
            }
            unset($dr);
        }
        if ($this->_debug) {
            echo "------------ \n" . count($drs)." DRs \n ------------\n";
        }
        if ($revisions) {
            $this->_md5 = md5($revisions);
        }
    }

    protected function addDetail($cvi, $acheteur) {
        $detail = $acheteur->getParent()->getParent();
        $type = $acheteur->getParent()->getKey();
        $this->add(array(
            "cvi_acheteur" => $cvi,
            "nom_acheteur" => $detail->getCepage()->getLieu()->acheteurs->$type->$cvi->getNom(),
            "cvi_recoltant" => $detail->getCouchdbDocument()->cvi,
            "nom_recoltant" => $detail->getCouchdbDocument()->declarant->nom,
            "appellation" => $detail->getCepage()->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $detail->getCepage()->getLieu()->getConfig()->getLibelle(),
            "cepage" => $detail->getCepage()->getConfig()->getLibelle(),
            "vtsgn" => $detail->vtsgn,
            "denomination" => $detail->denomination,
            "superficie_livree" => null,
            "volume_livre" => $acheteur->quantite_vendue,
            "dont_dplc" => null,
            "superficie_totale" => $detail->superficie,
            "volume_total" => $detail->volume,
            "dplc_total" => null,
                ), $this->_validation_detail);
        
    }

    protected function addTotal($cvi, DRRecolteLieuAcheteur $acheteur) {
        $lieu = $acheteur->getLieu();
        $this->add(array(
            "cvi_acheteur" => $cvi,
            "nom_acheteur" => $acheteur->getNom(),
            "cvi_recoltant" => $acheteur->getCouchdbDocument()->cvi,
            "nom_recoltant" => $acheteur->getCouchdbDocument()->declarant->nom,
            "appellation" => $acheteur->getLieu()->getAppellation()->getConfig()->getLibelle(),
            "lieu" => $acheteur->getLieu()->getConfig()->getLibelle(),
            "cepage" => "TOTAL",
            "vtsgn" => null,
            "denomination" => null,
            "superficie_livree" => $acheteur->superficie,
            "volume_livre" => $acheteur->getVolume(),
            "dont_dplc" => $acheteur->dontdplc,
            "superficie_totale" => $acheteur->getLieu()->getTotalSuperficie(),
            "volume_total" => $acheteur->getLieu()->getTotalVolume(),
            "dplc_total" => $acheteur->getLieu()->getDplc(),
                ), $this->_validation_total);
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