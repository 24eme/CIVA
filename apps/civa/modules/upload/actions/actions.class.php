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
	$this->errors[$cpt][] = 'cannot identify the product';
      if (!$this->hasVolume($line))
	$this->errors[$cpt][] = 'no volume';
      if (!$this->mayHaveSuperficie($line))
	$this->errors[$cpt][] = 'wrong superficie';
      if (!$this->isVTSGNOk($line))
	$this->errors[$cpt][] = 'incorrect VT/SGN';
      if (!$this->hasGoodUnit($line)) {
	$this->warnings[$cpt][] = 'Les unités ne semblent pas en ares et hectolitres';
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
    if (preg_match('/^6[78]\d{8}$/', $line[0]) && preg_match('/^6[78]\d{8}$/', $line[2]))
      return true;
    return false;
  }
  protected function canIdentifyProduct($line) {
    if (ConfigurationClient::getConfiguration()->identifyProduct($line[4], $line[5], $line[7]))
      return true;
    return false;
  }
  private function isPositive($val) {
    $val = preg_replace('/,/', '.', $val);
    if (!is_numeric($val))
      return false;
    if ($val < 0)
      return false;
    return true;
  }

  protected function hasGoodUnit($line) {
    if ((!$line[10] || preg_match('/^[0-9,\.]+$/', $line[10])) && (preg_match('/^[0-9,\.]+$/', $line[11]) || !$line[11]))
      return true;
    return false;
  }

  protected function hasVolume($line) {
    return $this->isPositive($line[10]);
  }
  protected function mayHaveSuperficie($line) {
    if (!isset($line[11]) || !$line[11])
      return true;
    return $this->isPositive($line[10]);
  }
}
