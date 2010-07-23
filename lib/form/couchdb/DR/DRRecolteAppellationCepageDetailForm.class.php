<?php

class DRRecolteAppellationCepageDetailForm extends BaseDRRecolteAppellationCepageDetailForm {
    public function configure() {
        $this->embedForm('0', new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->getObject()->get('acheteurs/0')));
    }
}
