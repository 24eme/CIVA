<?php

class DRRecolteAppellationCepageDetailForm extends BaseDRRecolteAppellationCepageDetailForm {
    public function configure() {
        foreach($this->getObject()->getAcheteurs() as $key => $acheteur) {
            echo $key;
            print_r($acheteur);
            $this->embedForm($key, new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $acheteur));
        }
    }

    public function addAcheteur() {
        $this->embedForm(count($this->embeddedForms), new DRRecolteAppellationCepageDetailAcheteurForm($this->doc, $this->object->getAcheteurs()->add()));
    }
}
