<?php

class SVExtraction extends BaseSVExtraction {

    public function getRecapProduit() {
        $recap = $this->getDocument()->getRecapProduits();

        return $recap[str_replace("/extraction/", "/declaration/", $this->getHash())];
    }

    public function getQuantiteRecolte() {

        return $this->getRecapProduit()->quantite_recolte;
    }

    public function getLibelleHtml() {

        return $this->getRecapProduit()->libelle_html;
    }

    public function setVolumeExtrait($volume) {
        $this->_set('volume_extrait', $volume);
        echo $this->getQuantiteRecolte() / $volume."\n";
        $this->taux_extraction = round($this->getQuantiteRecolte() / $volume, 4);
    }
}
