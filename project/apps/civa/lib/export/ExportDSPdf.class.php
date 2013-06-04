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
        $this->createAnnexe($ds);
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

        $recap = array("AOC Alsace Blanc" => array("colonnes" => array("cepage" => "Cepages"),
                                                   "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                   "produits" => array(), 
                                                   "limit" => -1,
                                                   "nb_ligne" => -1),
                       "AOC Alsace Grand Cru" => array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cepages"), 
                                                       "produits" => array(), 
                                                       "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                       "limit" => 13,
                                                       "nb_ligne" => 13),
                       "Crémant d'Alsace" => array("colonnes" => array("couleur" => "Couleurs"), 
                                                   "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                   "produits" => array(), 
                                                   "limit" => -1,
                                                   "nb_ligne" => -1));

        foreach($alsace_blanc as $appellation_key) {
            $this->preBuildRecap($ds, $appellation_key, $recap["AOC Alsace Blanc"]);
        }
        $this->preBuildRecap($ds, "CREMANT", $recap["Crémant d'Alsace"]);
        $page = $recap;

        foreach($alsace_blanc as $appellation_key) {
            $this->preBuildRecap($ds, $appellation_key, $recap["AOC Alsace Blanc"]);
            $this->getRecap($ds, $appellation_key, $recap["AOC Alsace Blanc"]);
        }
       
        $this->getRecap($ds, "GRDCRU", $recap["AOC Alsace Grand Cru"], true);
        $this->getRecap($ds, "CREMANT", $recap["Crémant d'Alsace"]);

        $paginate = $this->paginate($recap, 29, $page);
        $this->rowspanPaginate($paginate);
        $this->autoFill($paginate, $page);

        foreach($paginate["pages"] as $num_page => $page) {
            $is_last = ($num_page == count($paginate["pages"]) - 1);
            $this->document->addPage($this->getPartial('ds_export/douane', array('ds' => $ds, 
                                                                                 'recap' => $page, 
                                                                                 'total' => $is_last)));
        }
    }

    protected function createAnnexe($ds) {
        $this->buildOrder($ds);
        $appellations = array("ALSACEBLANC", "LIEUDIT", "COMMUNALE", "PINOTNOIR", "PINOTNOIRROUGE");
        $recap = array();
        foreach($appellations as $appellation_key) {
            if(!$ds->declaration->getAppellations()->exist("appellation_".$appellation_key)) {

                continue;
            }

            $appellation = $ds->declaration->getAppellations()->get("appellation_".$appellation_key);

            $colonnes = array("cepage" => "Cepages");
            $lieu = false;
            if($appellation->getConfig()->hasManyLieu() || $appellation->getConfig()->hasLieuEditable()) {
                $colonnes = array("lieu" => "Lieu-dit", "cepage" => "Cepages");
                $lieu = true;
            }

            $recap[$appellation->getLibelle()] = array("colonnes" => $colonnes, 
                                                   "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                   "produits" => array(), 
                                                   "limit" => -1,
                                                   "nb_ligne" => -1);

            $this->getRecap($ds, $appellation_key, $recap[$appellation->getLibelle()], $lieu);
        }

        $paginate = $this->paginate($recap, 33);
        $this->rowspanPaginate($paginate);

        foreach($paginate["pages"] as $num_page => $page) {
            $is_last = ($num_page == count($paginate["pages"]) - 1);
            $this->document->addPage($this->getPartial('ds_export/annexe', array('ds' => $ds, 
                                                                                 'recap' => $page)));
        }
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
            $key = $this->addProduit($recap, $detail->getCepage()->getConfig(), ($lieu && $detail->lieu) ? $detail->lieu : $lieu, $couleur);

            if (!is_null($detail->volume_normal)) {
                $recap["produits"][$key]["normal"] += $detail->volume_normal;
                
            }

            if (!is_null($detail->volume_vt)) {
                $recap["produits"][$key]["vt"] += $detail->volume_vt;
                
            }

            if(!is_null($detail->volume_sgn)) {
                $recap["produits"][$key]["sgn"] += $detail->volume_sgn;
            }

            $recap["total"]["normal"] += $detail->volume_normal;
            $recap["total"]["vt"] += $detail->volume_vt;
            $recap["total"]["sgn"] += $detail->volume_sgn;
        }

        ksort($recap['produits']);
    }

    protected function addProduit(&$recap, $produit_config, $lieu = false, $couleur = false) {
        $key_lieu = "lieu:".$produit_config->getLieu()->getKey();
        $key_cepage = "cepage:".$produit_config->getKey();
        $key_couleur = "couleur:".$this->getCouleurKey($produit_config->getKey());

        if($lieu) {
            $key = sprintf("%s%s/%s%s/%s", $this->getOrder($key_cepage), $key_cepage, $this->getOrder($key_lieu), $key_lieu, $lieu);
            $libelle = $produit_config->getLieu()->getLibelle();
            if($produit_config->getAppellation()->hasLieuEditable()) {
                $key = sprintf("%s%s/%s", $this->getOrder($key_cepage), $key_cepage, $lieu);
                $libelle = $lieu;
            }
            $colonnes = array("lieu" => array("rowspan" => 1, "libelle" => $libelle), 
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

    protected function addProduitLigneVide(&$recap, $colonnes) {

        $recap["produits"]['999vide'.uniqid()] = array("colonnes" => $colonnes, "normal" => null, "vt" => null, "sgn" => null);
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


    protected function paginate($recap, $limit, $page = null) {
        $paginate = array("pages" => array(), "total" => array());

        $i = 0;
        $num_page = 0;
        foreach($recap as $libelle => $tableau) {
            $num_page = floor($i / $limit);
            $j = 0;
            foreach($tableau["produits"] as $hash => $produit) {
                $num_page = floor($i / $limit);
                if($tableau["limit"] > 0) {
                    $num_page = $num_page + floor($j / $tableau["limit"]);
                }
                if(!isset($paginate["pages"][$num_page])) {
                    if($page) {
                        $paginate["pages"][$num_page] = $page;
                    } else {
                        $paginate["pages"][$num_page] = array();
                    }
                }

                if(!isset($paginate["pages"][$num_page][$libelle])) {
                    $paginate["pages"][$num_page][$libelle] = $tableau;
                    $paginate["pages"][$num_page][$libelle]["produits"] = array();
                }

                $paginate["pages"][$num_page][$libelle]["produits"][$hash] = $produit;

                if($num_page == floor($i / $limit)) {
                    $i++;
                }
                $j++;
            }

            $paginate["pages"][$num_page][$libelle]["total"] = $tableau["total"];
        }

        return $paginate;
    }

    protected function autoFill(&$paginate, $config) {
        foreach($paginate["pages"] as $num_page => $page) {
            foreach($page as $libelle => $tableau) {
                $config_nb_ligne = $config[$libelle]['nb_ligne'];
                $nb_ligne = count($tableau['produits']);
                if(!($config_nb_ligne > 0 && $nb_ligne < $config_nb_ligne)) {

                    continue;
                }

                for($i = $nb_ligne; $i <= $config_nb_ligne; $i++) {
                    $colonnes = array();
                    foreach($config[$libelle]['colonnes'] as $key => $colonne) {
                        $colonnes[$key] = array("libelle" => null, "rowspan" => 1);
                    }
                    $this->addProduitLigneVide($paginate["pages"][$num_page][$libelle], $colonnes);
                }
            }
        }
    }

    protected function rowspanPaginate(&$paginate) {
        foreach($paginate["pages"] as $num_page => $page) {
            foreach($page as $libelle => $tableau) {
                $this->rowspan($paginate["pages"][$num_page][$libelle]);
            }
        }
    }

    protected function rowspan(&$recap) {
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

    protected function getCouleurKey($cepage) {
        if($cepage == "cepage_PR") {
            
            return "rouge";
        } elseif($cepage == "cepage_PN") {
            
            return "rose";
        }

        return "blanc";
    }

    protected function getOrder($key) {
        if(!isset($this->order[$key])) {

            return "999";
        }
        return sprintf("%03d", $this->order[$key]);
    }


    protected function getPartial($templateName, $vars = null) {
          return call_user_func_array($this->partial_function, array($templateName, $vars));
    }

}
