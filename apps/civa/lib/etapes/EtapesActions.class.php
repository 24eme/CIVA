<?php

class EtapesActions extends sfActions
{
    protected $_etapes_config = null;
    
    public function  __construct($context, $moduleName, $actionName) {
        parent::__construct($context, $moduleName, $actionName);
        $this->_etapes_config = new EtapesConfig();
    }

    protected function redirectByBoutonsEtapes($boutons_suppl = null) {
        if ($this->askRedirectToNextEtapes()) {
             $this->redirect($this->_etapes_config->nextUrl());
        } elseif($this->askRedirectToPreviousEtapes()) {
            $this->redirect($this->_etapes_config->previousUrl());
        } elseif(!is_null($boutons_suppl) && is_array($boutons_suppl)) {
            foreach($boutons_suppl as $bouton_suppl => $action) {
                if (in_array($bouton_suppl,  $this->getBoutons())) {
                    if ($action == 'next') {
                        $this->redirect($this->_etapes_config->nextUrl());
                    } elseif ($action == 'previous') {
                        $this->redirect($this->_etapes_config->previousUrl());
                    }
                }
            }
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

    protected function redirectToNextEtapes() {
        $this->redirect($this->_etapes_config->nextUrl());
    }

    protected function redirectToPreviousEtapes() {
        $this->redirect($this->_etapes_config->previousUrl());
    }

    protected function setCurrentEtape($current_etape) {
        $this->_etapes_config->setCurrentEtape($current_etape);
   }
}
