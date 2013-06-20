<?php

class ExportDSPdf {
    protected $type;
    protected $document;
    protected $nb_pages;
    protected $partial_name;
    protected $file_dir;
    protected $no_cache;
    protected $cvi;
    protected $autres = array();
    protected $agrega_total;
    protected $ds_principale;
    protected $dss;

    public function __construct($ds_principale, $partial_function, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {
        if(!$ds_principale->isDSPrincipale()) {
            throw new sfException("Ce n'est pas la DS principale");
        }

        $this->type = $type;
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;

        $this->ds_principale = $ds_principale;
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds_principale);

        $this->init($filename);
    }

    public function isCached() {
        return (!$this->no_cache && $this->document->isCached());
    }

    public function removeCache() {
        return $this->document->removeCache();
    }

    public function generatePDF() {
        if(!$this->isCached()) {
            $this->create();
        }
        return $this->document->generatePDF($this->no_cache);
    }

    public function addHeaders($response) {
        $this->document->addHeaders($response);
    }

    public function output() {
        return $this->document->output();
    }

    protected function init($filename = null) {
        $validee = 'Non Validée';
        $validee = 'Déclaration validée le 31/07/2013';
        $validee .= ' et modifiée le 03/08/2013';
        sfContext::getInstance()->getConfiguration()->loadHelpers('ds');
        $title = 'Déclaration de Stocks au 31 Juillet 2013';
        $header = sprintf("%s\nCommune de déclaration : %s\n%s", 'GAEC '.$this->ds_principale->declarant->nom, $this->ds_principale->declarant->commune, $validee);
        if (!$filename) {
            $rev = null;
            foreach($this->dss as $ds) {
                $rev .= $ds->_rev;
            }
            $rev = md5($rev);
            $filename = $this->ds_principale->periode.'_DS_'.$this->ds_principale->declarant->cvi.'_'.$rev.'.pdf';
        }

        $config = array('PDF_MARGIN_TOP' => 21,
                        'PDF_FONT_SIZE_MAIN' => 8,
                        'PDF_HEADER_LOGO_WIDTH' => 20);

        if ($this->type == 'html') {
          $this->document = new PageableHTML($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }
    }

    protected function create() {
        foreach($this->dss as $ds) {
            if(!$ds->isValidee()) {
                $ds->update();
                $ds->declaration->cleanAllNodes();
            }
            $this->createMainByDS($ds);
            $this->createAnnexeByDS($ds);
        }

        $this->createRecap();
    }

    protected function createMainByDS($ds) {
        $this->buildOrder($ds);
        $alsace_blanc = array("ALSACEBLANC", "COMMUNALE", "LIEUDIT", "PINOTNOIR", "PINOTNOIRROUGE");

        $recap = array("AOC Alsace" => array("colonnes" => array("cepage" => "Cépages"),
                                                   "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                   "produits" => array(),
                                                   "nb_produits" => 0,
                                                   "limit" => -1,
                                                   "nb_ligne" => -1),
                       "AOC Alsace Grands Crus" => array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"), 
                                                        "produits" => array(),
                                                        "nb_produits" => 0,
                                                        "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                        "limit" => 13,
                                                        "nb_ligne" => 13),
                       "AOC Crémant d'Alsace" => array("colonnes" => array("couleur" => "Couleurs"), 
                                                       "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                       "produits" => array(),
                                                       "nb_produits" => 0,
                                                       "no_header" => true,
                                                       "limit" => -1,
                                                       "nb_ligne" => -1));

        foreach($alsace_blanc as $appellation_key) {
            $this->preBuildRecap($ds, $appellation_key, $recap["AOC Alsace"]);
        }

        $this->preBuildRecap($ds, "CREMANT", $recap["AOC Crémant d'Alsace"]);
        $page = $recap;

        foreach($alsace_blanc as $appellation_key) {
            $this->getRecap($ds, $appellation_key, $recap["AOC Alsace"]);
        }
       
        $this->getRecap($ds, "GRDCRU", $recap["AOC Alsace Grands Crus"], true);
        $this->getRecap($ds, "CREMANT", $recap["AOC Crémant d'Alsace"]);

        $paginate = $this->paginate($recap, 29, $page);

        $this->rowspanPaginate($paginate);
        $this->autoFill($paginate, $page);

        foreach($paginate["pages"] as $num_page => $page) {
            $is_last = ($num_page == count($paginate["pages"]) - 1);
            $this->document->addPage($this->getPartial('ds_export/principal', array('ds' => $ds, 
                                                                                 'recap' => $page,
                                                                                 'autres' => $this->getAutres($ds),
                                                                                 'is_last_page' => $is_last)));
        }
    }

    protected function createAnnexeByDS($ds) {
        $this->buildOrder($ds);
        $appellations = array("ALSACEBLANC", "LIEUDIT", "COMMUNALE", "PINOTNOIR", "PINOTNOIRROUGE");
        $recap = array();
        foreach($appellations as $appellation_key) {
            if(!$ds->declaration->getAppellations()->exist("appellation_".$appellation_key)) {

                continue;
            }

            $appellation = $ds->declaration->getAppellations()->get("appellation_".$appellation_key);

            $colonnes = array("cepage" => "Cépages");
            $lieu = false;
            if($appellation->getConfig()->hasManyLieu() || $appellation->getConfig()->hasLieuEditable()) {
                $colonnes = array("lieu" => "Lieu-dit", "cepage" => "Cépages");
                $lieu = true;
            }

            $recap[$appellation->getLibelle()] = array("colonnes" => $colonnes, 
                                                       "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                       "produits" => array(),
                                                       "limit" => -1,
                                                       "no_header" => !$appellation->getConfig()->hasVtsgn(),
                                                       "nb_ligne" => -1);

            $this->getRecap($ds, $appellation_key, $recap[$appellation->getLibelle()], $lieu);
        }

        $paginate = $this->paginate($recap, 36);
        $this->rowspanPaginate($paginate);

        foreach($paginate["pages"] as $num_page => $page) {
            $is_last = ($num_page == count($paginate["pages"]) - 1);
            $this->document->addPage($this->getPartial('ds_export/annexe', array('ds' => $ds, 
                                                                                 'recap' => $page)));
        }
    }

    protected function createRecap() {
        
        $this->document->addPage($this->getPartial('ds_export/recap', array('ds' => $this->ds_principale, 
                                                                            'recap_total' => $this->getRecapTotal(),
                                                                            'recap_autres' => $this->getRecapAutres(),
                                                                            'recap_vins_sans_ig' => $this->getRecapVinsSansIG())));
    }

    protected function getAutres($ds) {
        if(!array_key_exists($ds->_id, $this->autres)) {
            $this->autres[$ds->_id] = array("Moûts concentrés rectifiés" => $ds->isDSPrincipale() ? $ds->mouts : null, 
                      "Vins sans IG (Vins de table)" => $ds->getTotalVinSansIg(), 
                      "Vins sans IG mousseux" => $ds->getTotalMousseuxSansIg(), 
                      "Rebêches" => $ds->isDSPrincipale() ? $ds->rebeches : null, 
                      "Dépassements de rendements" => $ds->isDSPrincipale() ? $ds->dplc : null, 
                      "Lies en stocks" => $ds->isDSPrincipale() ? $ds->lies : null);
        }

        return $this->autres[$ds->_id];
    }

    protected function getRecapTotal() {
        if(is_null($this->agrega_total)) {
            $this->agrega_total = DSCivaClient::getInstance()->getTotauxByAppellationsRecap($this->ds_principale);
        }

        return $this->agrega_total;
    }

    protected function getRecapAutres() {

        return array("Rebêches" => $this->ds_principale->rebeches, 
                     "Usages industiels" => $this->ds_principale->getUsagesIndustriels());
    }

    protected function getRecapVinsSansIG() {

        return array("Vins Sans IG" => DSCivaClient::getInstance()->getTotalSansIG($this->ds_principale), 
                     "Mousseux" => DSCivaClient::getInstance()->getTotalSansIGMousseux($this->ds_principale));
    }

    protected function getRecap($ds, $appellation_key, &$recap, $lieu = false, $couleur = false) {
        if(!$ds->declaration->getAppellations()->exist('appellation_'.$appellation_key)) {

            return; 
        }

        if(is_null($recap["total"]["normal"])) {
            $recap["total"]["normal"] = 0;
        }

        if(is_null($recap["total"]["vt"])) {
            $recap["total"]["vt"] = 0;
        }

        if(is_null($recap["total"]["sgn"])) {
            $recap["total"]["sgn"] = 0;
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
            $libelle = $produit_config->getLieu()->getLibelleLong();
            if($produit_config->getAppellation()->hasLieuEditable()) {
                $key = sprintf("%s%s/%s", $this->getOrder($key_cepage), $key_cepage, $lieu);
                $libelle = $lieu;
            }
            $colonnes = array("lieu" => array("rowspan" => 1, "libelle" => $libelle), 
                              "cepage" => array("rowspan" => 1, "libelle" => $produit_config->getLibelleLong()));
        } elseif ($couleur) {
            $key = sprintf("%s%s", $this->getOrder($key_couleur), $key_couleur);
            $colonnes = array("couleur" => array("rowspan" => 1, "libelle" => $key_couleur));
        }
        else {
            $key = sprintf("%s%s", $this->getOrder($key_cepage), $key_cepage);
            $colonnes = array("cepage" => array("rowspan" => 1, "libelle" => $produit_config->getLibelleLong()));
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

        ksort($recap['produits']);
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

            if(in_array($cepage->getKey(), array('cepage_PN', 'cepage_PR'))) {
            
                continue;
            }

            $this->order["cepage:".$cepage->getKey()] = $i;
            $i++; 
        }

        $this->order["cepage:cepage_PN"] = $i;
        $this->order["cepage:cepage_PR"] = $i + 1;

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
                    $paginate["pages"][$num_page][$libelle]["nb_produits"] = 0;
                    $paginate["pages"][$num_page][$libelle]["produits"] = array();
                }

                $paginate["pages"][$num_page][$libelle]["nb_produits"] += 1;
                $paginate["pages"][$num_page][$libelle]["produits"][$hash] = $produit;

                if($num_page == floor($i / $limit)) {
                    $i++;
                }
                $j++;
            }

            if(isset($paginate["pages"][$num_page][$libelle])) {
                $paginate["pages"][$num_page][$libelle]["total"] = $tableau["total"];
            }
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
