<?php

class ValidatorLieuxStockageDS extends sfValidatorSchema {

    protected $lieux_stockage;
    
    public function __construct($lieux_stockage = null, $options = array(), $messages = array())
    {          
        $this->lieux_stockage = $lieux_stockage;
        parent::__construct($options, $messages);
        
    }
    protected function configure($options = array(), $messages = array()) {
        $this->addMessage('required_appellations', "Aucune appellation n'a été séléctionné, et la DS n'est pas à néant");
        $this->addMessage('invalid_lieux_stockage', "Il n'est pas possible de sauvegarder un lieu de stockage secondaire avec des appellations alors que le principal n'en possède pas.");
    }

    protected function doClean($values) {
        $errorSchema = new sfValidatorErrorSchema($this);
        $values_by_lieux = array();
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = preg_replace('/^[0-9]{10}/','', $lieu_s);   
            $id_lieu_field = 'lieuxStockage_'.$id_lieu;
            $values_by_lieux[$id_lieu] = $values[$id_lieu_field];
        }

        $empty = true;
        foreach ($values_by_lieux as $key_lieu => $fields) {
            if(!is_null($fields)){
                $empty = false;
                break;
            }
        }

        if($empty && !$values['neant']) {
            $errorSchema->addError(new sfValidatorError($this, 'required_appellations'));  
        }

        if(is_null($values_by_lieux['001'])){
            foreach ($values_by_lieux as $key_lieu => $fields) {
                if($key_lieu == '001') continue;
                if(!is_null($fields)){
                    $errorSchema->addError(new sfValidatorError($this, 'invalid_lieux_stockage'));
                }
            }
        }
        // throws the error for the main form
        if (count($errorSchema)) {
            throw new sfValidatorErrorSchema($this, $errorSchema);
        }
        
        return $values;
    }

}