<?php

class DRDeclarant extends BaseDRDeclarant {
    public function getIntitule() {

        return CompteGenerique::extractIntitule($this->nom)[0];
    }

    public function getNomWithoutIntitule() {

        return CompteGenerique::extractIntitule($this->nom)[1];
    }
}
