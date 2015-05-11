<?php

class ValidatorLieuxStockageDS extends sfValidatorSchema {

    protected $lieux_stockage;
    protected $ds = null;

    public function __construct($lieux_stockage = null, $ds = null, $options = array(), $messages = array()) {
        $this->lieux_stockage = $lieux_stockage;
        $this->ds = $ds;
        parent::__construct($options, $messages);
    }

    protected function configure($options = array(), $messages = array()) {
        $this->addMessage('required_appellation', "Aucune appellation n'a été séléctionné, et la DS n'est pas à néant");
        $this->addMessage('invalid_lieux_stockage', "La DS principale doit avoir au moins une appelation.");
        if ($this->ds->isDsPrincipale() && $this->ds->isDateDepotMairie()) {
            $this->addMessage('invalid_date_depot_mairie', "Il n'est pas possible de sauvegarder une DS dont la date de dépot en mairie a dépassée le 10 septembre.");
        }
    }

    protected function doClean($values) {
        $errorSchema = new sfValidatorErrorSchema($this);
        $values_by_lieux = array();

        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = preg_replace('/^C?[0-9]{10}/', '', $lieu_s);
            $id_lieu_field = 'lieuxStockage_' . $id_lieu;
            $values_by_lieux[$id_lieu] = $values[$id_lieu_field];
        }

        $empty = true;
        foreach ($values_by_lieux as $key_lieu => $fields) {
            if (!is_null($fields)) {
                $empty = false;
                break;
            }
        }

        if ($empty && !$values['neant']) {
            $errorSchema->addError(new sfValidatorError($this, 'required_appellation'));
        }

        $num_principale = $values['ds_principale'][0];
        
        if (is_null($values_by_lieux[$num_principale])) {
            foreach ($values_by_lieux as $key_lieu => $fields) {
                if ($key_lieu == $num_principale)
                    continue;
                if (!is_null($fields)) {
                    $errorSchema->addError(new sfValidatorError($this, 'invalid_lieux_stockage'));
                }
            }
        }
        if ($this->ds->isDsPrincipale() && $this->ds->isDateDepotMairie()) {
            $matches = array();
            $annee = substr(CurrentClient::getCurrent()->getPeriodeDS(),0,4);
            $pattern = '/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/';
            if (!preg_match($pattern, $values['date_depot_mairie'], $matches)) {
                $errorSchema->addError(new sfValidatorError($this, 'invalid_date_depot_mairie'));
            }

            if(Date::getIsoDateFromFrenchDate($values['date_depot_mairie']) > $annee."-09-10"){
                //$errorSchema->addError(new sfValidatorError($this, 'invalid_date_depot_mairie'));
            }
        }
        // throws the error for the main form
        if (count($errorSchema)) {
            throw new sfValidatorErrorSchema($this, $errorSchema);
        }

        return $values;
    }

}