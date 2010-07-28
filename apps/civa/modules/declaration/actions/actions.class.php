<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationActions extends sfActions {

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->campagnes = $this->getUser()->getRecoltant()->getDeclarationArchivesCampagne($this->getUser()->getCampagne());
        $this->declaration = $this->getUser()->getDeclaration();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->processChooseDeclaration($request);
        }
    }

    protected function processChooseDeclaration(sfWebRequest $request) {
        $recoltant = $this->getUser()->getRecoltant();
        $dr_data = $this->getRequestParameter('dr', null);
        if ($dr_data) {
            if ($dr_data['type_declaration'] == 'reprendre_brouillon') {
                $this->redirect('@exploitation_administratif');
            } elseif ($dr_data['type_declaration'] == 'supprimer_brouillon') {
                $this->getUser()->getDeclaration()->delete();
                $this->redirect('@mon_espace_civa');
            } elseif ($dr_data['type_declaration'] == 'nouvelle') {
                $doc = new DR();
                $doc->set('_id', 'DR-' . $recoltant->cvi . '-' . $this->getUser()->getCampagne());
                $doc->cvi = $recoltant->cvi;
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->save();
                $this->redirect('@exploitation_administratif');
            } elseif ($dr_data['type_declaration'] == 'reprendre_ancienne') {
                $this->forward404Unless($old_doc = $recoltant->getDeclaration($dr_data['liste_precedentes_declarations']));
                $doc = clone $old_doc;
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->save();
                $this->redirect('@exploitation_administratif');
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
