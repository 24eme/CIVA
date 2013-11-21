<?php
class CompteDroitsForm extends acCouchdbForm {
    
    protected $comptes = null;

    public function __construct(acCouchdbDocument $doc, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->comptes = $doc->getComptesPersonnes();
        parent::__construct($doc, $defaults, $options, $CSRFSecret);
    }

    public function constructDefaults() {
        $defaults = array();

        foreach($this->comptes as $compte) {
            $defaults[$compte->_id] = array();
            $defaults[$compte->_id]['droits'] = $compte->droits->toArray();
        }

        return $defaults;
    }

    public function getComptes() {

        return $this->comptes;
    }

    public function configure() {
        foreach($this->comptes as $compte) {
            $this->embedForm($compte->_id, new CompteDroitForm($compte));
        }

        $this->widgetSchema->setNameFormat('comptes_droits[%s]');
    }

    public function save() {
        foreach($this->comptes as $compte) {
            $compte->remove('droits');
            $compte->add('droits', $this->values[$compte->_id]["droits"]);
            $compte->save();
        }
    }
}