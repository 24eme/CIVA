<?php

class ExportDSPdfEmpty extends ExportDSPdf {
  
    protected $tiers;
    
    public function __construct($tiers, $partial_function, $annexe = true, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {

        
        $this->tiers = $tiers;
        $this->type = $type;
        $this->annexe = true;
        $this->dss = DSCivaClient::getInstance()->createDssByTiers($this->tiers,date('Y-m-d'));       
        foreach ($this->dss as $ds) {
            if($ds->isDsPrincipale()){
                $this->ds_principale = $ds;
            }
        }
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;

        $this->init($filename);
    }

    
    public function generatePDF() {
        if($this->no_cache || !$this->isCached()) {
            $this->create();
        }
        return $this->document->generatePDF($this->no_cache);
    }

    protected function init($filename = null) {
        
        sfContext::getInstance()->getConfiguration()->loadHelpers('ds');
        $title = 'Déclaration de Stocks au 31/07/'.date('Y');
        
        $header = getHeader($this->ds_principale,'');
        if (!$filename) {
            $filename = $this->getFileName(true, true);
        }
            
        $config = array('PDF_FONT_SIZE_MAIN' => 9);

        if ($this->type == 'html') {
           $this->document = new PageableHTML($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }
    }

    public function getFileName($with_name = true, $with_rev = false) {

      return self::buildFileName($this->ds_principale, $with_name, $with_rev);
    }

    public static function buildFileName($ds_principale, $with_name = true, $with_rev = false) {
         return parent::buildFileName($ds_principale, $with_name, $with_rev);
    }

    protected function create() {
        foreach($this->dss as $ds) {
            $ds->storeInfos();
            $ds->update();
            $this->createMainByDS($ds);
        }
    }
    
    protected function createMainByDS($ds) {
        
        $this->buildOrder($ds);        
        $recap = array();
             
        $recap["AOC Alsace Blanc"] = array("colonnes" => array("cepage" => "Cépages"), 
                                                "produits" => array(),
                                                "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                "limit" => -1,
                                                "nb_ligne" => -1);
        
        $recap["AOC Alsace Lieu-dit"] = array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"), 
                                                "produits" => array(),
                                                "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                "limit" => 7,
                                                "nb_ligne" => -1);
        
        $recap["AOC Alsace Communale"] = array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"), 
                                                "produits" => array(),
                                                "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                "limit" => 7,
                                                "nb_ligne" => -1);
        
        $recap["AOC Alsace Grands Crus"] = array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"), 
                                                 "produits" => array(),
                                                 "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                 "limit" => 7,
                                                 "nb_ligne" => -1);
        
        $recap["AOC Alsace Pinot noir"] = array("colonnes" => array("cepage" => "Cépages"), 
                                                "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                                "produits" => array(),
                                                "no_header" => true,
                                                "limit" => -1,
                                                "nb_ligne" => -1,
                                                "fixed" => true);
        
        $recap["AOC Alsace PN rouge"] = array("colonnes" => array("cepage" => "Cépages"), 
                                              "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                              "produits" => array(),
                                              "no_header" => true,
                                              "limit" => -1,
                                              "nb_ligne" => -1,
                                              "fixed" => true);        
        
        $recap["AOC Crémant d'Alsace"] = array("colonnes" => array("couleur" => "Couleurs"), 
                                               "total" => array("normal" => null, "vt" => null, "sgn" => null),
                                               "produits" => array(),
                                               "no_header" => true,
                                               "limit" => -1,
                                               "nb_ligne" => -1,
                                               "fixed" => true);

        $this->preBuildRecap($ds, "CREMANT", $recap["AOC Crémant d'Alsace"]);
        
        $this->getRecap($ds, "ALSACEBLANC", $recap["AOC Alsace Blanc"],false,false,true);
        $this->getRecap($ds, "LIEUDIT", $recap["AOC Alsace Lieu-dit"], true,false,true);
        $this->getRecap($ds, "COMMUNALE", $recap["AOC Alsace Communale"], true,false,true);
        $this->getRecap($ds, "GRDCRU", $recap["AOC Alsace Grands Crus"], true,false,true);        
        $this->getRecap($ds, "PINOTNOIR", $recap["AOC Alsace Pinot noir"]);
        $this->getRecap($ds, "PINOTNOIRROUGE", $recap["AOC Alsace PN rouge"]);
        $this->getRecap($ds, "CREMANT", $recap["AOC Crémant d'Alsace"]);

        $paginate = $this->paginate($recap, self::NB_LIGNES_PAR_PAGES);

        $this->rowspanPaginate($paginate);
        $this->autoFill($paginate, $recap);        
        
        foreach($paginate["pages"] as $num_page => $page) {
            $is_last = ($num_page == count($paginate["pages"]) - 1);
            $this->document->addPage($this->getPartial('ds_export/principalEmpty', array('ds' => $ds,
                                                                                         'recap' => $page,
                                                                                         'autres' => $this->getAutres($ds),
                'is_last_page' => $is_last)));
        }
    }
}
