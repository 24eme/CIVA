<?php

class EtapesActions extends sfActions {

    protected $_etapes_config = null;

    public function __construct($context, $moduleName, $actionName) {
        parent::__construct($context, $moduleName, $actionName);
        $this->_etapes_config = new EtapesConfig();
    }

    protected function redirectByBoutonsEtapes($boutons_suppl = null) {
        if ($this->askRedirectToNextEtapes()) {
            $this->redirectToNextEtapes();
        } elseif ($this->askRedirectToPreviousEtapes()) {
            $this->redirectToPreviousEtapes();
        } elseif ($this->askRedirectToPrevisualiser()) {
            $this->redirectToPrevisualiser();
        } elseif (!is_null($boutons_suppl) && is_array($boutons_suppl)) {
            foreach($boutons_suppl as $bouton_suppl => $action) {
                if (in_array($bouton_suppl, $this->getBoutons())) {
                    if ($action == 'next') {
                        $this->redirectToNextEtapes();
                    } elseif ($action == 'previous') {
                        $this->redirectToPreviousEtapes();
                    } elseif($action == 'previsualiser') {
                        $this->redirectToPrevisualiser();
                    }
                }
            }
        } else {
            $this->redirect($this->getRoute()->getParameters());
        }
    }

    protected function getBoutons() {
        $boutons = array();
        if (is_array($this->getRequest()->getParameter('boutons'))) {
            $boutons = array_keys($this->getRequest()->getParameter('boutons'));
        }
        return $boutons;
    }

    protected function askRedirectToNextEtapes() {
        return in_array('next', $this->getBoutons());
    }

    protected function askRedirectToPreviousEtapes() {
        return in_array('previous', $this->getBoutons());
    }

    protected function askRedirectToPrevisualiser() {
        return in_array('previsualiser', $this->getBoutons());
    }

    protected function redirectToNextEtapes() {
        if ($new_etape = $this->_etapes_config->needToChangeEtape()) {
            $this->getUser()->addEtape($new_etape);
        }
        $this->redirect($this->_etapes_config->nextUrl());
    }

    protected function redirectToPreviousEtapes() {
        $this->redirect($this->_etapes_config->previousUrl());
    }

    protected function redirectToPrevisualiser() {
        $this->redirect('@print?annee=' . $this->getUser()->getCampagne());
    }

    protected function setCurrentEtape($current_etape) {
        $this->_etapes_config->setCurrentEtape($current_etape);
    }

}
