<?php
class Recoltant extends BaseRecoltant {
    public function __toString() {
        return $this->getNom() . " - Récoltant";
    }
    
    public function getRaisonSociale() {
        return $this->getNom();
    }
    
    public function getNoAccises() {
        return 1;
    }
    
    public function getRegion() {
        return '';
    }
}