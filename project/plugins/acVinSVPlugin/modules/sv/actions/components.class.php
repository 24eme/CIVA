<?php

class svComponents extends sfComponents {
    public function executeModalExtraction() {
        $this->form = new SVExtractionForm($this->sv);
    }
}
