<?php

class _DRActions extends sfActions
{
    protected $_etapes_config = null;

    public function __construct($context, $moduleName, $actionName) {
        parent::__construct($context, $moduleName, $actionName);
        $this->_etapes_config = new EtapesConfig();
    }

    protected function redirectByBoutonsEtapes($boutons_suppl = null, $dr) {
        if ($this->askRedirectToNextEtapes()) {

            return $this->redirectToNextEtapes($dr);
        } elseif ($this->askRedirectToPreviousEtapes()) {

            return $this->redirectToPreviousEtapes($dr);
        } elseif ($this->askRedirectToPrevisualiser()) {

            return $this->redirectToPrevisualiser();
        } elseif (!is_null($boutons_suppl) && is_array($boutons_suppl)) {
            foreach($boutons_suppl as $bouton_suppl => $action) {
                if (in_array($bouton_suppl, $this->getBoutons())) {
                    if ($action == 'next') {

                        return $this->redirectToNextEtapes($dr);
                    } elseif ($action == 'previous') {

                        return $this->redirectToPreviousEtapes($dr);
                    } elseif($action == 'previsualiser') {

                        return $this->redirectToPrevisualiser($dr);
                    }
                }
            }
        } else {

            return $this->redirect($this->getRoute()->getParameters());
        }
    }

    protected function getBoutons() {
        $boutons = array();
        if (is_array($this->getRequest()->getParameter('boutons'))) {
            $boutons = array_keys($this->getRequest()->getParameter('boutons'));
        }
        return $boutons;
    }

    protected function redirectToEtape($etape, $dr) {

        return $this->redirect($this->_etapes_config->getUrl($etape), $dr);
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

    protected function redirectToNextEtapes($dr) {
        if ($new_etape = $this->_etapes_config->needToChangeEtape()) {
            $this->getUser()->addEtapeDeclaration($new_etape);
        }

        return $this->redirect($this->_etapes_config->nextUrl(), $dr);
    }

    protected function redirectToPreviousEtapes($dr) {

        return $this->redirect($this->_etapes_config->previousUrl(), $dr);
    }

    protected function redirectToPrevisualiser($dr) {

        return $this->redirect('print', $dr);
    }

    protected function setCurrentEtape($current_etape) {
        $this->_etapes_config->setCurrentEtape($current_etape);
    }

    public function secureDR($droit) {
        if($this->getRoute() instanceof DRRoute) {
            if(!DRSecurity::getInstance($this->getRoute()->getDR())->isAuthorized($droit)) {

                return $this->forwardSecure();
            }
        }

        if($this->getRoute() instanceof EtablissementRoute) {
            if(!EtablissementSecurity::getInstance($this->getRoute()->getEtablissement())->isAuthorized(Roles::TELEDECLARATION_DR)) {
                return $this->forwardSecure();
            }
        }

        if(!$this->getRoute() instanceof EtablissementRoute && !$this->getRoute() instanceof DRRoute) {
            exit;
        }
    }

    protected function forwardSecure()
    {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }
}
