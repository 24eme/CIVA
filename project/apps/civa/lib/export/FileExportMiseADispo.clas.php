<?php

abstract class FileExportMiseADispo
{
    protected $_file_dir = null;
    protected $_filename = null;
    protected $_document = null;
    protected $_function_get_partial = null;
    protected $_export = null;

    public function __construct($_id, $function_get_partial) {
        $this->_function_get_partial = $function_get_partial;
        $this->_export_class = $export_class;

        $this->_document = $this->findDocument($_id);

        if (!$this->_document) {

            throw new sfException("document not found : " . $_id);
        }

        $this->_file_dir = $this->getFileDir();

        if (!is_dir($this->_file_dir)) {
            mkdir($this->_file_dir);
        }

        if (!is_dir($this->_file_dir)) {
            throw new sfException("path does not exist");
        }

        $this->_filename = $this->getFileName();
    }

    public function getDocument() {
        
        return $this->_document;
    }

    abstract public function getAnnee();

    public function isExported() {

        return is_file($this->getPath());
    }

    public function export() {
        $export = $this->getExport(); 
        $export->generatePDF();

        return true;
    }

    public function getPath() {
        return $this->_file_dir . $this->_filename;
    }

    abstract protected function getFileDir();

    abstract protected function getFileName();

    abstract protected function findDocument($id);

    abstract protected function getExport();

}