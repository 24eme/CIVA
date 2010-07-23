<?php

class DRRecolteAppellationCepageDetailForm extends BaseDRRecolteAppellationCepageDetailForm {
    public function configure() {
        //print_r($this->getObject()->getData());
        $this->embedForm('0', new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->getObject()->get('acheteurs/0')));
    }
}
