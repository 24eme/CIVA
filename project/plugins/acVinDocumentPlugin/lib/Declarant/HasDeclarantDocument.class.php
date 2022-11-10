<?php

trait HasDeclarantDocument
{
    protected $declarant_document = null;

    public function initDeclarantDocument()
    {
        $this->declarant_document = new DeclarantDocument($this);
    }

    public function storeDeclarant()
    {
        $this->declarant_document->storeDeclarant();
    }

    public function getEtablissementObject()
    {
        return $this->getEtablissement();
    }
}
