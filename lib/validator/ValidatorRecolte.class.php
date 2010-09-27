<?php

class ValidatorRecolte extends sfValidatorSchema
{
  protected $_count_total = 0;

  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('object');
    $this->addRequiredOption('has_acheteurs_mout');
    $this->addMessage('invalid_acheteur', "L'acheteur n'existe pas");
    $this->addMessage('invalid_demonination_vtsgn', "La dénomination et la mention VT/SGN doivent être uniques.");
    $this->addMessage('invalid_demonination', "La dénomination doit être unique.");
  }

  protected function doClean($values)
  {
    $this->_count_total = 0;

    $errorSchema = new sfValidatorErrorSchema($this);

    $cepage = $this->getObject()->getCepage();

    if ($cepage->getConfig()->hasDenomination() && $cepage->getConfig()->hasVtsgn()) {
        $liste_couples = $cepage->getArrayVtSgnDenomination(array($this->getObject()->getKey()));
        foreach($liste_couples as $item) {
            if ($values['denomination'] == $item['denomination'] && $values['vtsgn'] == $item['vtsgn']) {
                $errorSchema->addError(new sfValidatorError($this, 'invalid_demonination_vtsgn'));
                break;
            }
        }
    } elseif ($cepage->getConfig()->hasDenomination() && !$cepage->getConfig()->hasVtsgn()) {
        $liste_couples = $cepage->getArrayVtSgnDenomination(array($this->getObject()->getKey()));
        foreach($liste_couples as $item) {
            if ($values['denomination'] == $item['denomination']) {
                $errorSchema->addError(new sfValidatorError($this, 'invalid_demonination'));
                break;
            }
        }
    }
    

    $errorSchema->addError($this->doCleanAcheteurs($values,
                                                      ExploitationAcheteursForm::FORM_NAME_NEGOCES,
                                                      ListAcheteursConfig::getNegoces()));

    $errorSchema->addError($this->doCleanAcheteurs($values,
                                                   ExploitationAcheteursForm::FORM_NAME_NEGOCES . ExploitationAcheteursForm::FORM_SUFFIX_NEW,
                                                   ListAcheteursConfig::getNegoces()));

    $errorSchema->addError($this->doCleanAcheteurs($values,
                                                   ExploitationAcheteursForm::FORM_NAME_COOPERATIVES,
                                                   ListAcheteursConfig::getCooperatives()));

    $errorSchema->addError($this->doCleanAcheteurs($values,
                                                   ExploitationAcheteursForm::FORM_NAME_COOPERATIVES . ExploitationAcheteursForm::FORM_SUFFIX_NEW,
                                                   ListAcheteursConfig::getCooperatives()));

    if ($this->hasAcheteursMouts()) {
        $errorSchema->addError($this->doCleanAcheteurs($values,
                                                       ExploitationAcheteursForm::FORM_NAME_MOUTS,
                                                       ListAcheteursConfig::getMouts()));

        $errorSchema->addError($this->doCleanAcheteurs($values,
                                                       ExploitationAcheteursForm::FORM_NAME_MOUTS . ExploitationAcheteursForm::FORM_SUFFIX_NEW,
                                                       ListAcheteursConfig::getMouts()));
    }


    // throws the error for the main form
    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

    return $values;
  }


  public function doCleanAcheteurs($values, $name, $acheteurs) {
      $errorSchemaLocal = new sfValidatorErrorSchema($this);
      if (isset($values[$name])) {
          foreach($values[$name] as $cvi => $value) {
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