<?php

class ValidatorRecapitulatif extends sfValidatorSchema
{
  protected $_count_total = 0;

  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('object');
    $this->addMessage('invalid_superficie', "La somme superficie des acheteurs ne peut être supérieure au total");
    $this->addMessage('invalid_dontdplc', "La somme dplc des acheteurs ne peut être supérieure au total");
  }

  protected function doClean($values)
  {
    $errorSchema = new sfValidatorErrorSchema($this);

    $lieu = $this->getObject();
    $sum_superficie = 0;
    $sum_dontdplc = 0;
    
    foreach($values as $cvi => $value) {
        if (is_array($value) && array_key_exists('superficie', $value)) {
            $sum_superficie += $value['superficie'];
        }
        if (is_array($value) && array_key_exists('dontdplc', $value)) {
            $sum_dontdplc += $value['dontdplc'];
        }
    }

    if (intval($sum_superficie*100) > intval($lieu->getTotalSuperficie()*100)) {
         $errorSchema->addError(new sfValidatorError($this, 'invalid_superficie'));
    }

    if (intval($sum_dontdplc*100) > intval($lieu->getDplc()*100)) {
         $errorSchema->addError(new sfValidatorError($this, 'invalid_dontdplc'));
    }

    // throws the error for the main form
    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

    return $values;
  }

  protected function getObject() {
        return $this->getOption('object');
  }
}