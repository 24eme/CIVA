<?php

class ExportSVPdf extends ExportDocument
{
    const PRODUITS_PAR_PAGES = 20;
    const KEY_AUTRES_PRODUITS = 'Autres produits';

    protected $declaration = null;
    protected $etablissement = null;
    protected $document = null;
    protected $partial_function = null;

    public function __construct(SV $declaration, $type = 'pdf', $file_dir = null, $filename = null) {
        $this->declaration = $declaration;
        $this->etablissement = $declaration->getEtablissementObject();

        if (! $filename) {
            $filename = $this->getFileName(true, true);
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
        $produits[self::KEY_AUTRES_PRODUITS] = ['Lies et bourbes' => $this->declaration->lies, 'Rebêches' => $this->declaration->rebeches];

        for ($i = 0; $i < ceil(count($produits) / self::PRODUITS_PAR_PAGES); $i++) {
            $p = array_slice($produits, self::PRODUITS_PAR_PAGES * $i, self::PRODUITS_PAR_PAGES, true);
            @$this->document->addPage($this->getPartial('sv/pdf_par_produit', array('document' => $this->declaration, 'etablissement' => $this->etablissement, 'produits' => $p)));
        }

        $apporteurs = $this->declaration->getApporteurs()->toArray();

        $a = [];
        $total_produit_page = 0;
        foreach ($apporteurs as $apporteur) {
            $total_produit_page += count($apporteur->getProduits());

            if ($total_produit_page < 16 && count($a) < 4) {
                $a[] = $apporteur;
                continue;
            }

            @$this->document->addPage($this->getPartial('sv/pdf_par_apporteur', array('document' => $this->declaration, 'etablissement' => $this->etablissement, 'apporteurs' => $a)));
            $total_produit_page = count($apporteur->getProduits());
            $a = [];
            $a[] = $apporteur;
        }

        @$this->document->addPage($this->getPartial('sv/pdf_par_apporteur', array('document' => $this->declaration, 'etablissement' => $this->etablissement, 'apporteurs' => $a)));
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

    public function getFileName($with_name = true, $with_rev = false) {

        return self::buildFileName($this->declaration, $with_name, $with_rev);
    }

    public static function buildFileName($declaration, $with_name = true, $with_rev = false) {
        $filename = sprintf("%s", $declaration->_id);

        if($with_name) {
            $declarant_nom = strtoupper(KeyInflector::slugify($declaration->declarant->nom));
            $filename .= '_'.$declarant_nom;
        }

        if ($with_rev) {
            $filename .= '_' . $declaration->_rev;
        }

        return $filename . '.pdf';
    }

    protected function getTitle()
    {
        $titre = "Déclaration de Production";

        if ($this->declaration->valide->date_saisie) {

            $date = new DateTime($this->declaration->valide->date_saisie);
            $titre .= sprintf(" validée le %s", $date->format("d/m/Y"));
        }
        return $titre;
    }

    protected function getHeader()
    {
        if ($this->declaration->isValide() === false) {
            return 'BROUILLON';
        }
    }

    public function generatePDF()
    {
        $this->create();
        return $this->document->generatePDF(true);
    }
}
