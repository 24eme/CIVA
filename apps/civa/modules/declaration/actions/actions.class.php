<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationActions extends sfActions
{
 
  /**
   *
   * @param sfWebRequest $request
   */
  public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->campagne = '2010';
        $this->cvi = '6700800820';
        $docs = new sfCouchdbDocumentCollection(sfCouchdbManager::getClient()->startkey('DR-6700800820-0000')->endkey('DR-6700800820-9999')->getAllDocs());
        $this->campagnes = array();
        $this->has_brouillons = false;
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            if ($matches['campagne'] == $this->campagne) {
                $this->has_brouillons = true;
            } else {
                $this->campagnes[$doc_id] = $matches['campagne'];
            }
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            if ($dr_data = $this->getRequestParameter('dr', null)) {
                if ($dr_data['type_declaration'] == 'reprendre_brouillon') {
                    $this->redirect('@exploitation_administratif');
                } elseif($dr_data['type_declaration'] == 'supprimer_brouillon') {
                    sfCouchdbManager::getClient()->retrieveDocById('DR-'.$this->cvi.'-'.$this->campagne)->delete();
                    $this->redirect('@mon_espace_civa');
                } elseif($dr_data['type_declaration'] == 'nouvelle') {
                    $doc = new DR();
                    $doc->set('_id', 'DR-'.$this->cvi.'-'.$this->campagne);
                    $doc->cvi = $this->cvi;
                    $doc->campagne = $this->campagne;
                    $doc->save();
                    $this->redirect('@exploitation_administratif');
                } elseif($dr_data['type_declaration'] == 'reprendre_ancienne') {
                    
                }
            }
        }
  }

  /**
   *
   * @param sfWebRequest $request 
   */
  public function executeExploitationAutres(sfWebRequest $request) {
      
  }
  
  /**
   *
   * @param sfWebRequest $request
   */
  public function executeRecolte(sfWebRequest $request) {

  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeValidation(sfWebRequest $request) {

  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeConfirmation(sfWebRequest $request) {

  }

  
}
