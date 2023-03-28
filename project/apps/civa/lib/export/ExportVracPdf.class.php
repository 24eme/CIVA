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
        $title = "CONTRAT";
        $leftLength = 81;
        if($this->vrac->isPluriannuelCadre()) {
            $title .= " PLURIANNUEL";
            $leftLength = 67;
        }
    	if ($this->vrac->type_contrat == VracClient::TYPE_BOUTEILLE) {
        	$title .= " DE VENTE EN BOUTEILLES                                                Visa du CIVA N° ".$this->vrac->numero_visa;
        	$header = "DE VINS AOC PRODUITS EN ALSACE                                                               du ".strftime('%d/%m/%Y', strtotime($this->vrac->valide->date_validation));
    	} else {
        	$title .= " DE VENTE";
            if ($this->vrac->type_contrat == VracClient::TYPE_RAISIN) {
                $title .= " DE RAISIN";
            } elseif($this->vrac->type_contrat == VracClient::TYPE_MOUT) {
                $title .= " DE MOÛT";
            } else {
                $title .= " EN VRAC";
            }
            $title = str_pad($title, $leftLength, " ", STR_PAD_RIGHT);
            $title .= "Visa du CIVA N° ".$this->vrac->numero_visa;
        	$header = "DE VINS AOC PRODUITS EN ALSACE                                                            du ".strftime('%d/%m/%Y', strtotime($this->vrac->valide->date_validation));
    	}

        $headerBlankToBottom = "\n\n";

        if($this->vrac->isPluriannuelCadre()) {
            $campagnes = VracSoussignesForm::getCampagnesChoices();
            $header .= "\nCAMPAGNES D'APPLICATION ".$campagnes[$this->vrac->campagne];;
            $headerBlankToBottom = "\n";
        }

        if ($this->vrac->isAnnule()) {
            $header .= $headerBlankToBottom."ANNULÉ";
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

    protected function getPartial($templateName, $vars = null) {
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

        $path_verso = Document::getByDatedFilename(sfConfig::get('sf_web_dir').'/helpPdf/', 'contrat_de_vente_annuel_'.strtolower($this->vrac->type_contrat).'_verso.pdf', $this->vrac->valide->date_validation);

        $ouputPdf = sfConfig::get('sf_root_dir').'/cache/pdf/'.uniqid().'.pdf';
        shell_exec("pdftk ". $tmpPdfPath ." ".$path_verso." cat output ".$ouputPdf);
        unlink($tmpPdfPath);

        return file_get_contents($ouputPdf);
      }
      return $this->document->output();
    }


}
