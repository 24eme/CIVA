<?php

class ExportVracPdf extends ExportDocument {

    const NB_LIGNES_PAR_PAGES = 50;

    protected $type;
    protected $document;
    protected $nb_pages;
    protected $partial_function;
    protected $file_dir;
    protected $no_cache;

    protected $vrac;
    protected $odg;

    public function __construct($vrac, $odg, $partial_function, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {

        $this->type = $type;
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;

        $this->vrac = $vrac;
        $this->odg = $odg;

        $this->init($filename);
    }

    public function generatePDF() {
        if($this->no_cache || !$this->isCached()) {
            $this->create();
        }
        return $this->document->generatePDF($this->no_cache);
    }

    public static function getConfig() {

        return array('PDF_FONT_SIZE_MAIN' => 9);
    }

    protected function init($filename = null) {
        $nbCharTitle = 104;
        $nbCharHeader = 120;
        $title = "CONTRAT ".strtoupper($this->vrac->getTypeDureeLibelle())." DE VENTE DE ";
        if($this->vrac->isApplicationPluriannuel()) {
            $nbCharTitle -= 8;
            $nbCharHeader -= 8;
        }

    	if ($this->vrac->type_contrat == VracClient::TYPE_VRAC) {
            $title .= "VINS EN VRAC";
    	} elseif($this->vrac->type_contrat == VracClient::TYPE_BOUTEILLE) {
            $title .= "BOUTEILLES";
    	} elseif($this->vrac->type_contrat == VracClient::TYPE_RAISIN) {
            $title .= "RAISINS";
    	} elseif($this->vrac->type_contrat == VracClient::TYPE_MOUT) {
            $title .= "MOÛTS";
        }

        $title = str_pad($title, $nbCharTitle - strlen($title), " ", STR_PAD_RIGHT);
        $title .= "Visa du CIVA N° ".$this->vrac->numero_visa;

        $header = "AOC PRODUITS EN ALSACE";
        $header = str_pad($header, $nbCharHeader - strlen($header), " ", STR_PAD_RIGHT);
        $header .= "du ".strftime('%d/%m/%Y', strtotime($this->vrac->valide->date_validation));

        if($this->vrac->isPluriannuelCadre()) {
            $header .= "\n\nCAMPAGNES D'APPLICATION DE ".VracSoussignesForm::getCampagnesChoices()[$this->vrac->campagne];
        }
        if($this->vrac->isApplicationPluriannuel()) {
            $header .= "\n\nCONTRAT D'APPLICATION ".$this->vrac->campagne;
        }

        if ($this->vrac->isAnnule()) {
            $header .= "\nCONTRAT ANNULÉ";
        }

        if (!$filename) {
            $filename = $this->getFileName(true, true);
        }
        $config = self::getConfig();
        if ($this->type == 'html') {
          $this->document = new PageableHTML($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir, ' de ', 'P', $config);
        }
    }

    public function getFileName($with_name = true, $with_rev = false) {

      return self::buildFileName($this->vrac, $with_name, $with_rev);
    }

    public static function buildFileName($vrac, $with_name = true, $with_rev = false) {
        $filename = sprintf("%s_Contrat-%s_%s", $vrac->type_archive, str_replace('-', '', $vrac->valide->date_validation), $vrac->numero_visa);

        if($with_name) {
            $libelle = '';
		    if($vrac->vendeur->intitule) {
		    	$libelle .= $vrac->vendeur->intitule.'-';
		    }
		    $libelle .= $vrac->vendeur->raison_sociale.'_';
		    if($vrac->acheteur->intitule) {
		    	$libelle .= $vrac->acheteur->intitule.'-';
		    }
		    $libelle .= $vrac->acheteur->raison_sociale;
		    if ($vrac->hasCourtier()) {
		    	$libelle .= '_';
		    	if($vrac->mandataire->intitule) {
		    		$libelle .= $vrac->mandataire->intitule.'-';
		    	}
		    	$libelle .= $vrac->mandataire->raison_sociale;
		    }

            $libelle = strtoupper(KeyInflector::slugify($libelle));
            $filename .= '_'.$libelle;
        }

        if($with_rev) {
            $rev = VracClient::getInstance()->find($vrac->_id, acCouchdbClient::HYDRATE_JSON)->_rev;;
            $rev = md5($rev);
            $filename .= '_'.$rev;
        }

        return $filename.'.pdf';
    }

    protected function create() {
        $this->document->addPage($this->getPartial('vrac_export/principal', array('vrac' => $this->vrac, 'odg' => $this->odg)));

    }

    public function getPartial($templateName, $vars = null) {
        return call_user_func_array($this->partial_function, array($templateName, $vars));
    }

    public function getPdfFilePath(){
      return $this->document->getPdfFilePath();
    }

    public function output() {
      if($this->type == 'pdf'){

        $content = $this->document->output();
        $tmpPdfPath = sfConfig::get('sf_root_dir').'/cache/pdf/'.uniqid().'.pdf';
        file_put_contents($tmpPdfPath,$content);

        $path_verso = Document::getByDatedFilename(sfConfig::get('sf_web_dir').'/helpPdf/', 'contrat_de_vente_'.strtolower($this->vrac->getTypeDureeLibelle()).'_'.strtolower($this->vrac->type_contrat).'_verso.pdf', $this->vrac->valide->date_validation);

        $ouputPdf = sfConfig::get('sf_root_dir').'/cache/pdf/'.uniqid().'.pdf';
        shell_exec("pdftk ". $tmpPdfPath ." ".$path_verso." cat output ".$ouputPdf);
        unlink($tmpPdfPath);

        return file_get_contents($ouputPdf);
      }
      return $this->document->output();
    }


}
