<?php
class Recoltant extends BaseRecoltant {
    public function __toString() {
        return $this->getNom() . " - Récoltant";
    }
}