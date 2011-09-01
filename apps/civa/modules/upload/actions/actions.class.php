<?php

/**
 * upload actions.
 *
 * @package    civa
 * @subpackage upload
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class uploadActions extends sfActions
{

  const CSV_ACHETEUR_CVI = 0;
  const CSV_ACHETEUR_LIBELLE = 1;
  const CSV_RECOLTANT_CVI = 2;
  const CSV_RECOLTANT_LIBELLE = 3;
  const CSV_APPELLATION = 4;
  const CSV_LIEU = 5;
  //  const CSV_COULEUR = 6;
  const CSV_CEPAGE = 6;
  const CSV_VTSGN = 7;
  const CSV_DENOMINATION = 8;
  const CSV_VOLUME = 9;
  const CSV_SUPERFICIE = 10;
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeCsvUpload(sfWebRequest $request)
  {
    $this->csvform = new UploadCSVForm();
    if (!$request->isMethod('post'))
      return;
    $this->csvform->bind($request->getParameter('csv'),$request->getFiles('csv'));
    if (!$this->csvform->isValid())
      return ;
    return $this->redirect('upload/csvView?md5='.$this->csvform->getValue('file')->getMd5());
  }

  public function executeCsvView(sfWebRequest $request) 
  {
    $md5 = $request->getParameter('md5');

    $this->csv = new CsvFile(sfConfig::get('sf_data_dir').'/upload/'.$md5);
    $cpt = -1;
    $this->errors = array();
    $this->warnings = array();
    foreach ($this->csv->getCsv() as $line) {
      $cpt++;
      $this->errors[$cpt] = array();
      $this->warnings[$cpt] = array();
      if (!$this->hasCVI($line)) {
	if (!$cpt)
	  continue;
	$this->errors[$cpt][] = 'no cvi';
      }
      if (!$this->canIdentifyProduct($line))
	$this->errors[$cpt][] = 'Il nous  est impossible de repérer le produit, merci de vérifier les libellés.';
      if (!$this->hasVolume($line))
	$this->errors[$cpt][] = 'Le volume ne devrait pas être absent ou null.';
      if (!$this->mayHaveSuperficie($line))
	$this->errors[$cpt][] = 'La superficie erronnée.';
      if (!$this->isVTSGNOk($line))
	$this->errors[$cpt][] = 'Le champ VT/SGN est non valide.';
      if (!$this->hasGoodUnit($line)) {
	$this->warnings[$cpt][] = 'Les unités ne semblent pas en ares et hectolitres.';
      }
    }
  }
  
  protected function isVTSGNOk($line) {
    if (!$line[8])
      return true;
    if ($line[8] == 'VT')
      return true;
    if ($line[8] == 'SGN')
      return true;
    return false;
  }

  protected function hasCVI($line) {
    if (preg_match('/^6[78]\d{8}$/', $line[self::CSV_ACHETEUR_CVI]) && preg_match('/^6[78]\d{8}$/', $line[self::CSV_RECOLTANT_CVI]))
      return true;
    return false;
  }
  protected function canIdentifyProduct($line) {
    if (lc($line[self::CSV_APPELLATION]) == 'jeunes vignes')
      return true;
    if (ConfigurationClient::getConfiguration()->identifyProduct($line[self::CSV_APPELLATION], $line[self::CSV_LIEU], $line[self::CSV_CEPAGE]))
      return true;
    return false;
  }
  private function isPositive($val) {
    $val = preg_replace('/,/', '.', $val);
    if (!is_numeric($val))
      return false;
    if ($val <= 0)
      return false;
    return true;
  }

  protected function hasGoodUnit($line) {
    if (
	(preg_match('/^[0-9,\.]+$/', $line[self::CSV_SUPERFICIE]) || !$line[self::CSV_SUPERFICIE])
	&&
	(!$line[self::CSV_VOLUME] || preg_match('/^[0-9,\.]+$/', $line[self::CSV_VOLUME]))
	)
      return true;
    return false;
  }

  protected function hasVolume($line) {
    return $this->isPositive($line[self::CSV_VOLUME]);
  }
  protected function mayHaveSuperficie($line) {
    if (!isset($line[self::CSV_SUPERFICIE]) || !$line[self::CSV_SUPERFICIE])
      return true;
    return $this->isPositive($line[self::CSV_SUPERFICIE]);
  }
}
