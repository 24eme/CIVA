<?php

class svComponents extends sfComponents {
    public function executeModalExtraction() {
        $this->form = new SVExtractionForm($this->sv);
        if(!isset($this->url)) {
            $this->url = null;
        }
    }
}
