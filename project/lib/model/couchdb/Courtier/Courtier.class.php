<?php
/**
 * Model for Courtier
 *
 */

class Courtier extends BaseCourtier {

	public function __toString() {
        return $this->getNom() . " - "."Courtier";
    }

    public function isDeclarantContratForSignature() {

        return true;
    }

    public function isDeclarantContratForResponsable() {

        return true;
    }

    public function getIdentifiant() {

        return $this->siren;
    }
}