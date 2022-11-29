<?php

class ExportSVPdf extends ExportDocument
{
    const PRODUITS_PAR_PAGES = 22;

    protected $declaration = null;
    protected $etablissement = null;
    protected $document = null;
    protected $partial_function = null;

    public function __construct(SV $declaration, $type = 'pdf', $file_dir = null, $filename = null) {
        $this->declaration = $declaration;
        $this->etablissement = $declaration->getEtablissementObject();

        if (! $filename) {
            $filename = $this->getFileName(true);
        }

        $title = $this->getTitle();
        $header = $this->getHeader();

        if ($type == 'html') {
            $this->document = new PageableHTML($title, $header, $filename, $file_dir);
        }else {
            $this->document = new PageablePDF($title, $header, $filename, $file_dir);
        }
    }

    public function create()
    {
        $produits = $this->declaration->getRecapProduits();

        for ($i = 0; $i < ceil(count($produits) / self::PRODUITS_PAR_PAGES); $i++) {
            $p = array_slice($produits, self::PRODUITS_PAR_PAGES * $i, self::PRODUITS_PAR_PAGES, true);
            @$this->document->addPage($this->getPartial('sv/pdf', array('document' => $this->declaration, 'etablissement' => $this->etablissement, 'produits' => $p)));
        }
    }

    public function output() {
        if($this->document instanceof PageableHTML) {
            return parent::output();
        }

        return file_get_contents($this->getFile());
    }

    public function getFile() {

        if($this->document instanceof PageableHTML) {
            return parent::getFile();
        }

        return sfConfig::get('sf_cache_dir').'/pdf/'.$this->getFileName(true);
    }

    public function getFileName($with_rev = false) {

        return self::buildFileName($this->declaration, true);
    }

    public static function buildFileName($declaration, $with_rev = false) {
        $filename = sprintf("%s", $declaration->_id);

        if ($with_rev) {
            $filename .= '_' . $declaration->_rev;
        }

        return $filename . '.pdf';
    }

    protected function getTitle()
    {
        $date = new DateTime($this->declaration->valide->date_saisie);
        $titre = sprintf("Déclaration de Production du %s", $date->format('d/m/Y'));
        return $titre;
    }

    protected function getHeader()
    {
    }

    public function generatePDF()
    {
        $this->create();
        return $this->document->generatePDF(true);
    }
}
