<?php

class ValidatorRecolte extends sfValidatorSchema {

    protected $_count_total = 0;

    protected function configure($options = array(), $messages = array()) {
        $this->addRequiredOption('object');
        $this->addRequiredOption('has_acheteurs_mout');
        $this->addMessage('invalid_acheteur', "L'acheteur n'existe pas");
        $this->addMessage('invalid_unique', "Le lieu, la dénomination et/ou la mention VT/SGN doivent être uniques.");
        
    }

    protected function doClean($values) {
        $this->_count_total = 0;

        $errorSchema = new sfValidatorErrorSchema($this);

        $cepage = $this->getObject()->getCepage();
        $appellation = $this->getObject()->getCepage()->getCouleur()->getLieu()->getAppellation();

        $unique_key = 'lieu:'.$values['lieu'].',denomination:'.$values['denomination'].',vtsgn:'.$values['vtsgn'];
        foreach ($cepage->getArrayUniqueKey(array($this->getObject()->getKey())) as $item_unique_key) {
            if ($unique_key == $item_unique_key) {
                $errorSchema->addError(new sfValidatorError($this, 'invalid_unique'));
            }
        }
        
        $negoces = $this->getObject()->getDocument()->acheteurs->getTheoriticalNegoces();
        
        $errorSchema->addError($this->doCleanAcheteurs($values, RecolteForm::FORM_NAME_NEGOCES, $negoces));

        $errorSchema->addError($this->doCleanAcheteurs($values, RecolteForm::FORM_NAME_NEGOCES . RecolteForm::FORM_SUFFIX_NEW, $negoces));

        $errorSchema->addError($this->doCleanAcheteurs($values, RecolteForm::FORM_NAME_COOPERATIVES, ListAcheteursConfig::getCooperatives()));

        $errorSchema->addError($this->doCleanAcheteurs($values, RecolteForm::FORM_NAME_COOPERATIVES . RecolteForm::FORM_SUFFIX_NEW, ListAcheteursConfig::getCooperatives()));

        if ($this->hasAcheteursMouts()) {
            $errorSchema->addError($this->doCleanAcheteurs($values, RecolteForm::FORM_NAME_MOUTS, ListAcheteursConfig::getMouts()));

            $errorSchema->addError($this->doCleanAcheteurs($values, RecolteForm::FORM_NAME_MOUTS . RecolteForm::FORM_SUFFIX_NEW, ListAcheteursConfig::getMouts()));
        }

        // throws the error for the main form
        if (count($errorSchema)) {
            throw new sfValidatorErrorSchema($this, $errorSchema);
        }

        return $values;
    }

    protected function doCleanAcheteurs($values, $name, $acheteurs) {
        $errorSchemaLocal = new sfValidatorErrorSchema($this);
        if (isset($values[$name])) {
            foreach ($values[$name] as $cvi => $value) {
                if (!isset($acheteurs[$cvi]) && array_key_exists('quantite_vendue', $value)) {
                    $errorSchemaLocal->addError(new sfValidatorError($this, 'invalid_acheteur'));
                    unset($values[$name][$cvi]);
                }
                if (!($value['quantite_vendue'] > 0)) {
                    unset($values[$name][$cvi]);
                }
            }
        }
        return $errorSchemaLocal;
    }

    protected function getObject() {
        return $this->getOption('object');
    }

    protected function hasAcheteursMouts() {
        return $this->getOption('has_acheteurs_mout');
    }

}