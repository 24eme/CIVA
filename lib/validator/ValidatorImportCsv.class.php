<?php

class ValidatorImportCsv extends sfValidatorFile
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
    $options['mime_types'] = array('text/plain');
    $options['required'] = true;

    return parent::configure($options, $messages);

  }

  protected function doClean($values)
  {
    $csvValidated = new CsvValidatedFile(parent::doClean($values));
    
    $errorSchema = new sfValidatorErrorSchema($this);

    //Conversion UTF8
    $fc = htmlentities(utf8_decode(file_get_contents($csvValidated->getTempName())),ENT_NOQUOTES);
    $handle=fopen("php://memory", "rw");
    fwrite($handle, $fc);
    fseek($handle, 0);

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
    $separateur = $match[3];

    rewind($handle);
    $csv = array();
    while (($data = fgetcsv($handle, 0, $separateur)) !== FALSE) {
      $csv[] = $data;
    }
    fclose($handle);
    $csvValidated->setCsv($csv);
    return $csvValidated;
  }

}