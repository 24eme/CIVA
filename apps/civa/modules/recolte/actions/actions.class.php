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

    public function preExecute() {
        $this->configuration = ConfigurationClient::getConfiguration();
        $this->declaration = $this->getUser()->getDeclaration();
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        $this->setCurrentEtape('recolte');

        preg_match('/(?P<appellation>\w+)-(?P<lieu>\w*)/',$this->getRequestParameter('appellation_lieu', null), $appellation_lieu);
        $appellation = null;
        if (isset($appellation_lieu['appellation'])) {
            $appellation = $appellation_lieu['appellation'];
        }
        $lieu = null;
        if (isset($appellation_lieu['lieu'])) {
            $lieu = $appellation_lieu['lieu'];
        }
        $cepage = $this->getRequestParameter('cepage', null);

        $this->onglets = new RecolteOnglets($this->configuration, $this->declaration);
        if (!$appellation && !$lieu && !$cepage) {
           $this->redirect($this->onglets->getUrl());
        }
        $this->forward404Unless($this->onglets->init($appellation, $lieu, $cepage));

        $this->details = $this->declaration->get($this->onglets->getItemsCepage()->getHash())
                                     ->add($this->onglets->getCurrentKeyCepage())
                                     ->add('detail');

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }
}