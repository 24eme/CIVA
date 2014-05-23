<?php 

class TiersActionsStandalone {

	protected $form_gest = null;
	protected $form_gest_err = 0;
	protected $form_expl = null;
	protected $form_expl_err = 0;
	protected $request = null;
	protected $tiers = null;
	protected $callback = array();

	public function __construct($request, $tiers, $callback = array()) {
		$this->request = $request;
		$this->tiers = $tiers;
		$this->callback = $callback;
	}

	public function init() {
		$this->form_gest = null;
        $this->form_gest_err = 0;
        if($this->tiers->exist('exploitant')) {
            $this->form_gest = new TiersExploitantForm($this->tiers->getExploitant());
            $this->form_gest_err = 0;
        }

        $this->form_expl = new TiersExploitationForm($this->tiers);
        $this->form_expl_err = 0;
	}

	public function post() {
		if (!$this->request->isMethod(sfWebRequest::POST)) {
			return;
		}

        if ($this->request->getParameter('gestionnaire')) {
            $this->form_gest->bind($this->request->getParameter($this->form_gest->getName()));
            if ($this->form_gest->isValid()) {
                $this->form_gest->save();

            } else {
                $this->form_gest_err = 1;
            }
        }

        if ($this->request->getParameter('exploitation')) {
            $this->form_expl->bind($this->request->getParameter($this->form_expl->getName()));
            if ($this->form_expl->isValid()) {
                $this->form_expl->save();
            } else {
                $this->form_expl_err = 1;
            }
        }

        if (!$this->form_gest_err && !$this->form_expl_err) {
            
            $this->redirect('ds_exploitation', $this->ds);
    	}
	}

	protected function callback($nom) {
		if($this->callback('form_gest_save')) {
			
		}
	}
}