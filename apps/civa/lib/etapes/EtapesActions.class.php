<?php

class EtapesActions extends sfActions
{
    protected $_etapes_config = null;
    
    public function  __construct($context, $moduleName, $actionName) {
        parent::__construct($context, $moduleName, $actionName);
        $this->_etapes_config = new EtapesConfig();
    }

    protected function redirectByBoutonsEtapes($boutons_suppl = null) {
        $boutons = array();
        if (is_array($this->getRequest()->getParameter('boutons'))) {
            $boutons = array_keys($this->getRequest()->getParameter('boutons'));
        }
        if (in_array('next', $boutons)) {
             $this->redirect($this->_etapes_config->nextUrl());
        } elseif(in_array('previous', $boutons)) {
            $this->redirect($this->_etapes_config->previousUrl());
        } elseif(!is_null($boutons_suppl) && is_array($boutons_suppl)) {
            foreach($boutons_suppl as $bouton_suppl => $action) {
                if (in_array($bouton_suppl, $boutons)) {
                    if ($action == 'next') {
                        $this->redirect($this->_etapes_config->nextUrl());
                    } elseif ($action == 'previous') {
                        $this->redirect($this->_etapes_config->previousUrl());
                    }
                }
            }
        }
    }

    protected function setCurrentEtape($current_etape) {
        $this->_etapes_config->setCurrentEtape($current_etape);
   }
}
