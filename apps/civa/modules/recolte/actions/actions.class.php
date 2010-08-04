<?php

/**
 * recolte actions.
 *
 * @package    civa
 * @subpackage recolte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class recolteActions extends EtapesActions {
    const SESSION_RECOLTE_APPELLATION = 'recolte_appellation';
    const SESSION_RECOLTE_CEPAGE = 'recolte_cepage';

    public function preExecute() {
        $this->declaration = $this->getUser()->getDeclaration();
        $this->configuration = sfCouchdbManager::getClient('Configuration')->getConfiguration();
        $this->setAppellation();
        $this->setCepage();
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        $this->setCurrentEtape('recolte');

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    protected function setAppellation($appellation = null) {
        if (is_null($appellation)) {
            $appellation = $this->getAppellation();
        }
        $this->getUser()->setFlash(self::SESSION_RECOLTE_APPELLATION, $appellation);
        $this->appellation_current_key = $appellation;
    }
    
    protected function setCepage($cepage = null) {
        if (is_null($cepage)) {
            $cepage = $this->getCepage();
        }
        $this->getUser()->setFlash(self::SESSION_RECOLTE_CEPAGE, $cepage);
        $this->cepage_current_key = $cepage;
    }

    protected function getAppellation() {
        return $this->getUser()->getFlash(self::SESSION_RECOLTE_APPELLATION, $this->declaration->get('recolte')->getFirstCollectionKey());
    }

    protected function getCepage() {
        return $this->getUser()->getFlash(self::SESSION_RECOLTE_CEPAGE, $this->configuration->recolte->get($this->getAppellation())->lieu->getFirstCollectionKey());
    }



}
