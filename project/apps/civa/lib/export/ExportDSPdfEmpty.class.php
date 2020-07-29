<?php

class ExportDSPdfEmpty extends ExportDSPdf {
  
    protected $tiers;
    
    public function __construct($tiers, $type_ds, $partial_function, $annexe = true, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {

        
        $this->tiers = $tiers;
        $this->type = $type;
        $this->annexe = true;
        $this->dss = DSCivaClient::getInstance()->createDssByTiers($this->tiers, $type_ds, date('Y-m-d'));   
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
        $title = 'BROUILLON - Déclaration de Stocks au 31/07/'.date('Y');
        
        $header = getHeaderBrouillon($this->ds_principale,'');
        if (!$filename) {
            $filename = $this->getFileName(true, true);
        }
            
        $config = array('PDF_FONT_SIZE_MAIN' => 9,
                        'LOGO_FILE' => 'civa_brouillon.jpg');

        if ($this->type == 'html') {
           $this->document = new PageableHTML($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }
    }

    public function getFileName($with_name = true, $with_rev = false) {

      return "BROUILLON_".parent::getFileName($with_name, $with_rev);
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
        
        
        $appellations = $ds->declaration->getAppellationsSorted();
        
        $recap["AOC Alsace Blanc"] = array("colonnes" => array("cepage" => "Cépages"), 
                                                "produits" => array(),
                                                "limit" => -1,
                                                "nb_ligne" => -1);
        
        $recap["AOC Alsace Lieu-dit"] = array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"), 
                                                "produits" => array(),
                                                "limit" => -1,
                                                "nb_ligne" => -1);
        
        $recap["AOC Alsace Communale"] = array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"), 
                                                "produits" => array(),
                                                "limit" => -1,
                                                "nb_ligne" => -1);
        
        $recap["AOC Alsace Grand Cru"] = array("colonnes" => array("lieu" => "Lieu-dit", "cepage" => "Cépages"),
                                                 "produits" => array(),
                                                 "limit" => -1,
                                                 "nb_ligne" => -1);

        $recap["AOC Alsace Pinot noir"] = array("colonnes" => array("cepage" => "Cépages"),
                                                "produits" => array(),
                                                "no_header" => true,
                                                "limit" => -1,
                                                "nb_ligne" => -1,
                                                "fixed" => true);

        $recap["AOC Alsace PN rouge"] = array("colonnes" => array("cepage" => "Cépages"),
                                              "produits" => array(),
                                              "no_header" => true,
                                              "limit" => -1,
                                              "nb_ligne" => -1,
                                              "fixed" => true);

        $recap["AOC Crémant d'Alsace"] = array("colonnes" => array("couleur" => "Couleurs"),
                                               "produits" => array(),
                                               "no_header" => true,
                                               "limit" => -1,
                                               "nb_ligne" => -1,
                                               "fixed" => true);

        $this->preBuildRecap($ds, "CREMANT", $recap["AOC Crémant d'Alsace"]);
        
        $this->getRecap($ds, "ALSACEBLANC", $recap["AOC Alsace Blanc"],false,false,true);
        $this->getRecap($ds, "LIEUDIT", $recap["AOC Alsace Lieu-dit"], true,false,true);
        $this->getRecap($ds, "COMMUNALE", $recap["AOC Alsace Communale"], true,false,true);
        $this->getRecap($ds, "GRDCRU", $recap["AOC Alsace Grand Cru"], true,false,true);
        $this->getRecap($ds, "PINOTNOIR", $recap["AOC Alsace Pinot noir"]);
        $this->getRecap($ds, "PINOTNOIRROUGE", $recap["AOC Alsace PN rouge"]);
        $this->getRecap($ds, "CREMANT", $recap["AOC Crémant d'Alsace"]);

        $recap["Autres Produits"] = array("colonnes" => array("type"),
                                          "produits" => array(),
                                          "limit" => -1,
                                          "no_header" => true,
                                          "nb_ligne" => -1,
                                          "fixed" => true);
        foreach($this->getAutres($ds, false) as $libelle => $volume)  {
            $recap["Autres Produits"]["produits"][$libelle]["colonnes"] = array("type" => array("rowspan" => 1, "libelle" => $libelle));
            $recap["Autres Produits"]["produits"][$libelle]["normal"] = !is_null($volume) ? null : false;
        }

        $paginate = $this->paginate($recap, ExportDSPdf::NB_LIGNES_PAR_PAGES, null, true);

        $this->rowspanPaginate($paginate);
        $this->autoFill($paginate, $recap);

        foreach($paginate["pages"] as $num_page => $page) {
            $this->document->addPage($this->getPartial('ds_export/principalEmpty', array('ds' => $ds,
                                                                                         'recap' => $page)));
        }
    }

}
