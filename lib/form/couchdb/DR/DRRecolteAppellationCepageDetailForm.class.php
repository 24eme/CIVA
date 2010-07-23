<?php

class DRRecolteAppellationCepageDetailForm extends BaseDRRecolteAppellationCepageDetailForm {
    public function configure() {
        $this->embedForm(0, new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->getObject()->get('acheteurs/0')));
        $this->embedForm(1, new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->getObject()->get('acheteurs/1')));
        $this->embedForm(2, new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->getObject()->get('acheteurs/2')));
        $this->embedForm(3, new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->getObject()->get('acheteurs/3')));
    }

    public function addAcheteur() {
        $this->embedForm(count($this->embeddedForms), new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->object->getAcheteurs()->add()));
    }
}
