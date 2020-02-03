<?php

define('K_PATH_IMAGES', sfConfig::get('sf_web_dir')."/images/pdf/");
define ('PDF_HEADER_LOGO_WIDTH', 30);
define('K_CELL_HEIGHT_RATIO', 1.315);
define('K_TCPDF_CALLS_IN_HTML', true);

require_once(sfConfig::get('sf_lib_dir').'/vendor/tcpdf/tcpdf.php');

class PageablePDF extends PageableOutput {

    protected $pdf;
    protected $pdf_file;

    protected function init() {


        $header_logo_width = (isset($this->config['PDF_HEADER_LOGO_WIDTH'])) ? $this->config['PDF_HEADER_LOGO_WIDTH'] : PDF_HEADER_LOGO_WIDTH;
        $pdf_font_size_main = (isset($this->config['PDF_FONT_SIZE_MAIN'])) ? $this->config['PDF_FONT_SIZE_MAIN'] : PDF_FONT_SIZE_MAIN;
        $margin_top = (isset($this->config['PDF_MARGIN_TOP'])) ? $this->config['PDF_MARGIN_TOP'] : PDF_MARGIN_TOP;

        // create new PDF document
        $this->pdf = new TCPDF($this->orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $this->pdf->SetCreator('CIVA');
        $this->pdf->SetAuthor('CIVA');
        $this->pdf->SetTitle($this->title.$this->link.preg_replace('/\n/', ', ', $this->subtitle));
        $this->pdf->SetSubject('PDF CIVA');
        $this->pdf->SetKeywords('Declaration, DR, Recolte, Vins Alsace');

        // set default header dat
        $logo_file = (isset($this->config['LOGO_FILE'])) ? $this->config['LOGO_FILE'] : 'civa.jpg';
        $this->pdf->SetHeaderData($logo_file, $header_logo_width, $this->title, $this->subtitle);

        // set header and footer fonts
        $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '',  $pdf_font_size_main));
        $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins

        $this->pdf->SetMargins(PDF_MARGIN_LEFT, $margin_top, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_FOOTER - 5);

        //set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        $this->pdf->setLanguageArray('fra');

        $this->pdf->setFontSubsetting(true);

         /* Defaulf file_dir */
        if (!$this->file_dir) {
            umask(0002);
            $this->file_dir = sfConfig::get('sf_cache_dir').'/pdf/';
            if (!file_exists($this->file_dir)) {
                mkdir($this->file_dir);
            }
        }
        /******************/

        $this->pdf_file = $this->file_dir.$this->filename;

        // set font
        $this->pdf->SetFont('helvetica', '', $pdf_font_size_main);
    }

    public function isCached() {
        return file_exists($this->pdf_file);
    }

    public function removeCache() {
        if (file_exists($this->pdf_file))
            return unlink($this->pdf_file);
        return true;
    }

    public function getPdfFilePath(){
      return $this->pdf_file;
    }

    public function addHeaders($response) {
        $response->setHttpHeader('Content-Type', 'application/pdf');
        $response->setHttpHeader('Content-disposition', 'attachment; filename="' . basename($this->filename) . '"');
        $response->setHttpHeader('Content-Transfer-Encoding', 'binary');
    //    $response->setHttpHeader('Content-Length', filesize($this->pdf_file));
        $response->setHttpHeader('Pragma', '');
        $response->setHttpHeader('Cache-Control', 'public');
        $response->setHttpHeader('Expires', '0');
    }

    public function addPage($html) {
        $this->pdf->AddPage();
        $this->pdf->writeHTML($html);
    }

    public function generatePDF($no_cache = false) {
        if (!$no_cache && $this->isCached()) {
            return true;
        } else {
            $this->removeCache();
        }
        $this->pdf->lastPage();
        return $this->pdf->Output($this->pdf_file, 'F');
    }

    public function output() {
        if (!$this->isCached()) {
            $this->generatePDF();
        }
        return file_get_contents($this->pdf_file);
    }

}
