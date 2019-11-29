<?php

class ValidatorAdminStatistiquesMercuriales extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->addMessage('incoherent', "Vous devez selectionner la première quinzaine, la dernière quinzaine ou le mois complet");
    }

    protected function doClean($values) {
        if (!$values['start_date'] || !$values['end_date']) {
            return $values;
        }
        $start = new DateTime($values['start_date']);
        $end = new DateTime($values['end_date']);
        $nextEnd = new DateTime($values['end_date']);
        $nextEnd->modify('+1 day');
        if ($start >= $end) {
            throw new sfValidatorErrorSchema($this, array('end_date' => new sfValidatorError($this, 'incoherent')));
        }
        if (!($start->format('j') == 1 || $start->format('j') == 15)) {
            throw new sfValidatorErrorSchema($this, array('start_date' => new sfValidatorError($this, 'incoherent')));
        }
        if ($end->format('j') != 15) {
            if ($end->format('n') == $nextEnd->format('n')) {
                throw new sfValidatorErrorSchema($this, array('end_date' => new sfValidatorError($this, 'incoherent')));
            }
        }
        return $values;
    }

}
