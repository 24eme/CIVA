<?php
class CompteDroitsForm extends acCouchdbForm {

    protected $comptes = null;

    public function __construct(acCouchdbDocument $doc, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->comptes = array();
        foreach($doc->getContactsObj() as $compte) {
            if(!$compte->isActif() || !$compte->mot_de_passe) {
                continue;
            }

            $this->comptes[$compte->_id] = $compte;
        }
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

            $compte->add('droits');
            $this->embedForm($compte->_id, new CompteDroitForm($compte));
        }

        //$this->validatorSchema->setPostValidator(new ValidatorCompteDroits(null, array('doc' => $this->getDocument())));

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
