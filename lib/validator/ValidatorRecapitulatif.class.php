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
    sfContext::getInstance()->getLogger()->alert(json_encode($values));
    foreach($values as $cvi => $value) {
        if (array_key_exists('superficie', $value)) {

            $sum_superficie += $value['superficie'];
        }
        if (array_key_exists('dontdplc', $value)) {
            $sum_dontdplc += $value['dontdplc'];
        }
    }

    sfContext::getInstance()->getLogger()->alert($sum_superficie);
    sfContext::getInstance()->getLogger()->alert($sum_dontdplc);

    if ($sum_superficie > $lieu->getTotalSuperficie()) {
         $errorSchema->addError(new sfValidatorError($this, 'invalid_superficie'));
    }

    if ($sum_dontdplc > $lieu->getDPLCFinal()) {
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