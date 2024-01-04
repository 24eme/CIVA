<?php

class svComponents extends sfComponents {
    public function executeModalExtraction() {
        $this->form = new SVExtractionForm($this->sv);
        if(!isset($this->url)) {
            $this->url = null;
        }
    }
    public function executeMonEspaceColonne(sfWebRequest $request)
    {
        $this->svs = SVClient::getInstance()->getAllByEtablissement($this->etablissement);
        krsort($this->svs);
    }
}
