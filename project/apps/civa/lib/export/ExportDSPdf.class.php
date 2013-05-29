<?php

class ExportDSPdf {
    protected $type;
    protected $document;
    protected $nb_pages;
    protected $partial_name;
    protected $file_dir;
    protected $no_cache;

    public function __construct($ds, $partial_function, $type = 'pdf', $file_dir = null, $no_cache = false, $filename = null) {
        $this->type = $type;
        $this->partial_function = $partial_function;
        $this->file_dir = $file_dir;
        $this->no_cache = $no_cache;

        $this->init($ds, $filename);
        $this->create($ds);
    }

    public function isCached() {
        return (!$this->no_cache && $this->document->isCached());
    }

    public function removeCache() {
        return $this->document->removeCache();
    }

    public function generatePDF() {
        return $this->document->generatePDF($this->no_cache);
    }

    public function addHeaders($response) {
        $this->document->addHeaders($response);
    }

    public function output() {
        return $this->document->output();
    }

    protected function init($ds, $filename = null) {
        $title = 'Déclaration de stock '.$ds->campagne;
        $header = $ds->declarant->nom."\nCommune de déclaration : ".$ds->declarant->commune."\n"."Lieu de stockage : Principal";
        if (!$filename) {
            $filename = $ds->campagne.'_DS_'.$ds->declarant->cvi.'_'.$ds->_rev.'.pdf';
        }

        if ($this->type == 'html') {
          $this->document = new PageableHTML($title, $header, $filename, $this->file_dir, ' de ', 'P', 8);
        }else {
          $this->document = new PageablePDF($title, $header, $filename, $this->file_dir, ' de ', 'P', 8);
        }
    }

    protected function create($ds) {

       $this->document->addPage($this->getPartial('ds_export/douane'));
    }

    protected function getPartial($templateName, $vars = null) {
          return call_user_func_array($this->partial_function, array($templateName, $vars));
    }

}
