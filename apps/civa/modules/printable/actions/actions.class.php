<?php

/**
 * printable actions.
 *
 * @package    civa
 * @subpackage printable
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */

class printableActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeDR(sfWebRequest $request)
  {
    $recoltant = $this->getUser()->getRecoltant();
    $annee = $this->getRequestParameter('annee', null);
    $key = 'DR-'.$recoltant->cvi.'-'.$annee;
    $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
    $this->forward404Unless($dr);

    $this->setLayout(false);
    //    $this->getResponse()->setContent('application/x-pdf');

    if ($this->getRequestParameter('output', 'pdf') == 'html') {
      $document = new PageableHTML('Déclaration de récolte '.$annee, $recoltant->nom, $annee.'_DR_'.$recoltant->cvi.'.pdf');
    }else {
      $document = new PageablePDF('Déclaration de récolte '.$annee, $recoltant->nom, $annee.'_DR_'.$recoltant->cvi.'.pdf');
    }

    foreach ($dr->getRecolte()->filter('^appellation_') as $appellation) {
      foreach ($appellation->filter('^lieu') as $lieu) {
	$document->addPage($this->getPartial('pageDR', array('recoltant'=>$recoltant, 'appellation_lieu' => $lieu)));
      }
    }

    return $document->output();
    
  }
}
