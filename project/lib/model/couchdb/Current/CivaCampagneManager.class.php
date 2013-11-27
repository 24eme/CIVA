<?php

class CivaCampagneManager extends CampagneManager {

    protected function formatCampagneOutput($campagne_output) {

        return preg_replace('/^([0-9]+)-[0-9]+$/', '\1', $campagne_output);
    }

    protected function formatCampagneInput($campagne_input) {

        return sprintf("%s-%s", $campagne_input, $campagne_input + 1);
    }
}