<?php

class ValidatorImportCsv extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    /*
    $this->addRequiredOption('object');
    $this->addRequiredOption('has_acheteurs_mout');
    $this->addMessage('invalid_acheteur', "L'acheteur n'existe pas");
    $this->addMessage('invalid_demonination_vtsgn', "La dénomination et la mention VT/SGN doivent être uniques.");
    */
    $this->addMessage('invalid_file', "Le fichier fourni ne peut être lu");
    $this->addMessage('invalid_csv_file', "Le fichier fourni n'est pas un CSV");
    /*    $options['mime_types'] = array('text/plain');
    $options['required'] = true;
    */

  }

  protected function doClean($values)
  {
    $errorSchema = new sfValidatorErrorSchema($this);

    if (($handle = fopen($values['file']->getTempName(), "r")) === FALSE) {
	$errorSchema->addError(new sfValidatorError($this, 'invalid_file'));
	throw new sfValidatorErrorSchema($this, $errorSchema);
    }
    $buffer = fread($handle, 500);

    $buffer = preg_replace('/$[^\n]*\n/', '', $buffer);


    if (!$buffer) {
      $errorSchema->addError(new sfValidatorError($this, 'invalid_file'));
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
    if (!preg_match('/("?)[0-9]{10}("?)([,;])/', $buffer, $match)) {
      $errorSchema->addError(new sfValidatorError($this, 'invalid_csv_file'));
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
    rewind($handle);
    $values['csv'] = array();
    while (($data = fgetcsv($handle, $match[3])) !== FALSE) {
      $values['csv'][] = $data;
    }
    fclose($handle);
    return $values;
  }

}