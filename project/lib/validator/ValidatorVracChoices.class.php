<?php
class ValidatorVracChoices extends sfValidatorChoice {
    public function doClean($value) {
        $choices = $this->getChoices();
        if ($this->getOption('multiple')) {
          $value = $this->cleanMultiple($value, $choices);
        } else {
          if (!self::inChoices($value, $choices)) {
              if (self::inChoices($value, array_keys(VracSoussignesForm::getDureeContratNextMillesime()))) {
                  $choices = array_keys(VracSoussignesForm::getDureeContratNextMillesime());
              } else {
                throw new sfValidatorError($this, 'invalid', array('value' => $value));
              }
          }
        }
    }
}
