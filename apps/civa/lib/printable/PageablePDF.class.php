<?php

require_once(sfConfig::get('sf_lib_dir').'/vendor/tcpdf/tcpdf.php');

class PageablePDF extends PageableOutput {

  protected $pdf;

  protected function init() {
    // create new PDF document
    $this->pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // set document information
    $this->pdf->SetCreator('CIVA');
    $this->pdf->SetAuthor('CIVA');
    $this->pdf->SetTitle($this->title.$this->link.$this->subtitle);
    $this->pdf->SetSubject('PDF CIVA');
    $this->pdf->SetKeywords('Declaration, DR, récolte, vins d\'alsace');

    // set default header data
    $this->pdf->SetHeaderData("civa.jpg", PDF_HEADER_LOGO_WIDTH, $this->title, $this->subtitle);

    // set header and footer fonts
    $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
    //set margins
    $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    //set image scale factor
    $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    //set some language-dependent strings
    $this->pdf->setLanguageArray('fra');

    // ---------------------------------------------------------

    // set font
    $this->pdf->SetFont('dejavusans', '', 10);
  }

  public function addPage($html) {
    $this->pdf->AddPage();
    $this->pdf->writeHTML($html);
  }

  public function output() {
    $this->pdf->lastPage();
    $this->pdf->Output($this->filename, 'I');
  }
}

