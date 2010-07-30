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
       $this->setCurrentEtape('exploitation_acheteurs');

       $configuration = ConfigurationClient::getConfiguration();
       $this->config_appellations = $configuration->getArrayAppellations();
       $this->nb_config_appellations = count($this->config_appellations);

        $this->acheteurs_negociant = include(sfConfig::get('sf_data_dir').'/acheteurs-negociant.php');
        $this->acheteurs_cave = include(sfConfig::get('sf_data_dir').'/acheteurs-cave.php');
        $acheteurs_negociant_without_key = array();
        $acheteurs_cave_without_key = array();
        foreach($this->acheteurs_negociant as $item) {
            $acheteurs_negociant_without_key[] = $item['nom'].'|@'.$item['cvi'].'|@'.$item['commune'];
        }
        foreach($this->acheteurs_cave as $item) {
            $acheteurs_cave_without_key[] = $item['nom'].'|@'.$item['cvi'].'|@'.$item['commune'];
        }
        $this->acheteurs_negociant_json = $acheteurs_negociant_without_key;
        $this->acheteurs_cave_json = $acheteurs_cave_without_key;

        //print_r($this->getUser()->getDeclaration()->getAcheteurs());
        $this->form = new ExploitationAcheteursForm($this->getUser()->getDeclaration()->getAcheteurs());
       
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirectByBoutonsEtapes();
            }
            
        }
  }

  public function executeExploitationAcheteursTableRowItemAjax(sfWebRequest $request) {
      if ($request->isXmlHttpRequest()) {
          $configuration = ConfigurationClient::getConfiguration();
          $config_appellations = $configuration->getArrayAppellations();
          $qualite_name = $request->getParameter('qualite_name');
          $donnees = $request->getParameter('donnees');
          $nom = $donnees[0];
          $cvi = $donnees[1];
          $commune = $donnees[2];
          $mout = ($request->getParameter('acheteur_mouts', null) == '1');
          $config_appellations_form = $config_appellations;
          if ($mout) {
            $config_appellations_form = $configuration->getArrayAppellationsMout();
          }
          $values = array();
          $i=3;
          foreach($config_appellations_form as $appellation_key => $config_appellation) {
              $values[$appellation_key] = (isset($donnees[$i]) && $donnees[$i]=='1');
              $i++;
          }

          $form = ExploitationAcheteursForm::getNewQualiteAjax($qualite_name, $cvi, $values, $config_appellations_form);

          return $this->renderPartial('exploitationAcheteursTableRowItem', array('nom' => $nom,
                                                                          'cvi' => $cvi,
                                                                          'commune' => $commune,
                                                                          'appellations' => $config_appellations,
                                                                          'form_item' => $form[$qualite_name.'_new'][$cvi],
                                                                          'mout' => $mout));
      } else {
          $this->forward404();
      }
  }
}
