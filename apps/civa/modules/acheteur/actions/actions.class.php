<?php

/**
 * acheteurs actions.
 *
 * @package    civa
 * @subpackage acheteurs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class acheteurActions extends EtapesActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeExploitationAcheteurs(sfWebRequest $request)
  {
        $this->acheteurs_negociant = include(sfConfig::get('sf_data_dir').'/acheteurs-negociant.php');
        $this->acheteurs_cave = include(sfConfig::get('sf_data_dir').'/acheteurs-cave.php');
        $acheteurs_negociant_without_key = array();
        $acheteurs_cave_without_key = array();
        foreach($this->acheteurs_negociant as $item) {
            $acheteurs_negociant_without_key[] = $item;
        }
        foreach($this->acheteurs_cave as $item) {
            $acheteurs_cave_without_key[] = $item;
        }
        $this->acheteurs_negociant_json = json_encode($acheteurs_cave_without_key);
        $this->acheteurs_cave_json = json_encode($acheteurs_cave_without_key);

        $this->setCurrentEtape('exploitation_acheteurs');
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
  }
}
