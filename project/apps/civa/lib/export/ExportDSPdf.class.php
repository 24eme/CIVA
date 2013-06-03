<?php

class ExportDSPdf {
    protected $type;
    protected $document;
    protected $nb_pages;
    protected $partial_name;
    protected $file_dir;
    protected $no_cache;

    public function __construct($ds, $partial_function, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {
        $this->type = $type;
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;

        $this->init($ds, $filename);
        $this->create($ds);
    }

    public function isCached() {
        return (!$this->no_cache && $this->document->isCached());
    }

    public function removeCache() {
        return $this->document->removeCache();
    }

    public function generatePDF() {
        return $this->document->generatePDF($this->no_cache);
    }

    public function addHeaders($response) {
        $this->document->addHeaders($response);
    }

    public function output() {
        return $this->document->output();
    }

    protected function init($ds, $filename = null) {
        $title = 'Déclaration de stock '.$ds->campagne;
        $header = $ds->declarant->nom."\nCommune de déclaration : ".$ds->declarant->commune."\n"."Lieu de stockage : Principal";
        if (!$filename) {
            $filename = $ds->campagne.'_DS_'.$ds->declarant->cvi.'_'.$ds->_rev.'.pdf';
        }

        if ($this->type == 'html') {
          $this->document = new PageableHTML($title, $header, $filename, $this->file_dir, ' de ', 'P', 8);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir, ' de ', 'P', 8);
        }
    }

    protected function create($ds) {
        $this->buildOrder($ds);
        $alsace_blanc = array("ALSACEBLANC", "LIEUDIT", "COMMUNALE", "PINOTNOIR", "PINOTNOIRROUGE");
        $recap = array("AOC Alsace Blanc" => array("colonnes" => array("cepage" => "Cepages"), "produits" => array()),
                       "AOC Alsace Grand Cru" => array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cepages"), "produits" => array()),
                       "Crémant d'Alsace" => array("colonnes" => array("couleur" => "Couleurs"), "produits" => array()));

        foreach($alsace_blanc as $appellation_key) {
            $this->preBuildRecap($ds, $appellation_key, $recap["AOC Alsace Blanc"]);
            $this->getRecap($ds, $appellation_key, $recap["AOC Alsace Blanc"]);
        }

        $this->getRecap($ds, "GRDCRU", $recap["AOC Alsace Grand Cru"], true);

        $this->preBuildRecap($ds, "CREMANT", $recap["Crémant d'Alsace"]);
        $this->getRecap($ds, "CREMANT", $recap["Crémant d'Alsace"]);

        $this->document->addPage($this->getPartial('ds_export/douane', array('ds' => $ds, 'recap' => $recap)));
    }

    protected function getRecap($ds, $appellation_key, &$recap, $lieu = false, $couleur = false) {

        if(!isset($recap['total'])) {
            $recap['total'] = array("normal" => 0, "vt" => 0, "sgn" => 0);
        }

        if(!$ds->declaration->getAppellations()->exist('appellation_'.$appellation_key)) {

            return; 
        }

        $appellation = $ds->declaration->getAppellations()->get('appellation_'.$appellation_key);

        $details = $appellation->getProduitsDetails();

        foreach($details as $detail) {
            $key = $this->addProduit($recap, $detail->getCepage()->getConfig(), $lieu, $couleur);

            if (!is_null($detail->volume_normal)) {
                $recap["produits"][$key]["normal"] += $detail->volume_normal;
                $recap["total"]["normal"] += $detail->volume_normal;
            }

            if (!is_null($detail->volume_vt)) {
                $recap["produits"][$key]["vt"] += $detail->volume_vt;
                $recap["total"]["vt"] += $detail->volume_vt;
            }

            if(!is_null($detail->volume_sgn)) {
                $recap["produits"][$key]["sgn"] += $detail->volume_sgn;
                $recap["total"]["sgn"] += $detail->volume_sgn;
            }
        }

        ksort($recap['produits']);

        $prev_hash = null;
        $prev_cepage = null;
        foreach($recap['produits'] as $hash => $produit) {
            if (!preg_match("/cepage:([a-zA-Z0-9_]+)\//", $hash, $matches)) {
                continue;
            }

            $cepage = $matches[1];
           
            if($cepage == $prev_cepage) {
                $recap['produits'][$prev_hash]['colonnes']['cepage']['rowspan'] += 1;
                $recap['produits'][$hash]['colonnes']['cepage']['rowspan'] = 0;
                continue;
            }

            $prev_hash = $hash;
            $prev_cepage = $cepage;
        }
    }

    protected function addProduit(&$recap, $produit_config, $lieu = false, $couleur = false) {
        $key_lieu = "lieu:".$produit_config->getLieu()->getKey();
        $key_cepage = "cepage:".$produit_config->getKey();
        $key_couleur = "couleur:".$this->getCouleurKey($produit_config->getKey());

        if($lieu) {
            $key = sprintf("%s%s/%s%s", $this->getOrder($key_cepage), $key_cepage, $this->getOrder($key_lieu), $key_lieu);
            $colonnes = array("lieu" => array("rowspan" => 1, "libelle" => $produit_config->getLieu()->getLibelle()), 
                              "cepage" => array("rowspan" => 1, "libelle" => $produit_config->getLibelle()));
        } elseif ($couleur) {
            $key = sprintf("%s%s", $this->getOrder($key_couleur), $key_couleur);
            $colonnes = array("couleur" => array("rowspan" => 1, "libelle" => $key_couleur));
        }
        else {
            $key = sprintf("%s%s", $this->getOrder($key_cepage), $key_cepage);
            $colonnes = array("cepage" => array("rowspan" => 1, "libelle" => $produit_config->getLibelle()));
        }

        if(isset($recap["produits"][$key])) {
            
            return $key;
        }

        $recap["produits"][$key] = array("colonnes" => $colonnes, "normal" => null, "vt" => null, "sgn" => null);

        return $key;
    }

    protected function preBuildRecap($ds, $appellation_key, &$recap, $lieu = false, $couleur = false) {
        $produits = $ds->declaration->getConfig()->getNoeudAppellations()->get('appellation_'.$appellation_key)->getProduitsFilter(ConfigurationAbstract::TYPE_DECLARATION_DS);
        foreach($produits as $produit) {
            $key = $this->addProduit($recap, $produit, $lieu, $couleur);
        }
    }

    protected function buildOrder($ds) {
        $this->order = array();

        $i = 0;
        foreach($ds->declaration->getConfig()->getNoeudAppellations() as $appellation) {
            if(!$appellation->hasManyLieu()) {

                continue;
            }
            foreach($appellation->getLieux() as $lieu) {
                if(isset($this->order["lieu:".$lieu->getKey()])) {

                    continue;
                }
                $this->order["lieu:".$lieu->getKey()] = $i;
                $i++;
            }
        }

        $i = 0;
        foreach($ds->declaration->getConfig()->getProduitsFilter(ConfigurationAbstract::TYPE_DECLARATION_DS) as $cepage) {
            if(isset($this->order["cepage:".$cepage->getKey()])) {

                continue;
            }
            $this->order["cepage:".$cepage->getKey()] = $i;
            $i++; 
        }

        $this->order['couleur:blanc'] = 1;
        $this->order['couleur:rose'] = 2;
        $this->order['couleur:rouge'] = 3;
    }

    protected function getCouleurKey($cepage) {
        if($cepage == "cepage_PR") {
            
            return "rouge";
        } elseif($cepage == "cepage_PN") {
            
            return "rose";
        }

        return "blanc";
    }

    protected function getOrder($key) {

        return sprintf("%03d", $this->order[$key]);
    }


    protected function getPartial($templateName, $vars = null) {
          return call_user_func_array($this->partial_function, array($templateName, $vars));
    }

}
