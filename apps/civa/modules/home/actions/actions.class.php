<?php

/**
 * home actions.
 *
 * @package    civa
 * @subpackage home
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class homeActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
     // print_r(sfCouchdbManager::getClient()->getDoc('DR-2009-670d0800090'));
//
      
      $test = new DR();
      $test->load(sfCouchdbManager::getClient()->getDoc('DR-2009-6700300110'));
      
      /*$detail = new DRRecolteAppellationCepage();
      $detail->setAppellation('2');
      $detail->setCepage('HU');
      $detail->setVolume(2.6);
      $test->addRecolte($detail);

      $detail = new DRRecolteAppellationCepage();
      $detail->setAppellation('2');
      $detail->setCepage('H2');
      $detail->setVolume(3);
      $test->addRecolte($detail);*/


            echo '<br /><br />';
      
      echo '<br /><br />';
     
      print_r($test->getData());
      //$test = new DRRecolte();
      //print_r($test);
      //print_r($test->getData());
      //print_r(sfCouchdbManager::getClient()->getDoc('DR-2009-6703102670'));
      //echo $test->getRecolte()->getTest();
      //print_r($test->getAcheteurs());
      //print_r(json_encode($test->getData()));
      //print_r(sfCouchdbManager::getClient()->getDoc('DR-2009-6700300070'));

//      print_r($test->getAcheteurs());
//      print_r ($test->getAcheteurs()->getAppellation1());
      
     

  }
}
