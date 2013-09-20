<?php

class RecapitulatifContainerForm extends acCouchdbForm {

    protected $lieu = null;

    public function __construct(DRRecolteLieu $lieu, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->lieu = $lieu;
        parent::__construct($lieu->getDocument(), $defaults, $options, $CSRFSecret);

    }

    public function configure() {
        if(!$this->lieu->getConfig()->existRendementCouleur()) {
            $this->embedForm($this->lieu->getKey(), new RecapitulatifForm($this->lieu));
        } else {
            foreach($this->lieu->getCouleurs() as $couleur) {
                $this->embedForm($couleur->getKey(), new RecapitulatifForm($couleur));
            }
        }

        $this->widgetSchema->setNameFormat('recapitulatif[%s]');
    }

    public function save() {
        foreach($this->getEmbeddedForms() as $key => $form) {
            $form->doUpdateObject($this->values[$key]);
        }

        $this->doc->update();
        $this->doc->save();
    }

    public function getObject() {

        return $this->lieu;
    }

    public function isLiesSaisisables() {
        foreach($this->getEmbeddedForms() as $form) {
            if($form->isLiesSaisisables()) {
                return true;
            }
        }

        return false;
    }
}