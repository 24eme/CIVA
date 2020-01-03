<?php

class ExportVracMercurialePdf
{
    protected $mercuriale;
    protected $no_cache;

    public function __construct($mercuriale, $no_cache = false) 
    {
        $this->mercuriale = $mercuriale;
        $this->no_cache = $no_cache;
        $this->init();
    }
    
    public function generatePDF() 
    {
        $this->create();
        return $this->document->generatePDF($this->no_cache);
    }

    public static function getConfig() 
    {
        return array('PDF_FONT_SIZE_MAIN' => 9);
    }

    protected function init() 
    {
        $title = "";
        $header = "";
        $this->document = new PageablePDFMercuriales($title, $header, $this->mercuriale->getPdfFilname(), $this->mercuriale->getPublicPdfPath(), ' de ', 'P', self::getConfig());
    }

    protected function create() 
    {
        $this->document->addPage(self::getPartial('pdfStats', array('mercuriale' => $this->mercuriale)));
        $this->document->addPage(self::getPartial('pdfCumul', array('mercuriale' => $this->mercuriale)), 'L');
        if (count($this->mercuriale->getAllLotsBio()) > 0) {
            $this->document->addPage(self::getPartial('pdfCumul', array('mercuriale' => $this->mercuriale, 'bio' => 1)), 'L');
        }
        $this->document->addPage(self::getPartial('pdfPlot1', array('mercuriale' => $this->mercuriale)), 'L');
        $this->document->addPage(self::getPartial('pdfPlot2', array('mercuriale' => $this->mercuriale)), 'L');
    }

    protected static function getPartial($template, $vars = null)
    {
        return sfContext::getInstance()->getController()->getAction('mercuriales', 'main')->getPartial('mercuriales/'.$template, $vars);
    }

}