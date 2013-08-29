<?php

abstract class ExportDocument {

	public function __construct($document, $partial_function, $type, $file_dir = null, $no_cache = false, $filename = null) {

	}

	public function isCached() {
        return (!$this->no_cache && $this->document->isCached());
    }

    public function removeCache() {
        return $this->document->removeCache();
    }

    abstract public function generatePDF();

    public function addHeaders($response) {
        $this->document->addHeaders($response);
    }

    public function output() {
        return $this->document->output();
    }

}