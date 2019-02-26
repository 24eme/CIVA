<?php
define ('PDF_MARGIN_HEADER', 0);
define ('PDF_MARGIN_TOP', 15);

class PageablePDFMercuriales extends PageablePDF {

    protected function init() {
        parent::init();
        $this->pdf->SetPrintHeader(false);
        $this->pdf->setFooterData(array(0,0,0), array(255,255,255));
    }
    
    public function addPage($html, $orientation = 'P') {
        $this->pdf->AddPage($orientation);
        $this->pdf->writeHTML($html);
    }

}

