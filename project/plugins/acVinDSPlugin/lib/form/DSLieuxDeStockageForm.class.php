<?php

class DSLieuxDeStockageForm extends acCouchdbForm {

    protected $lieux_stockage = null;
    protected $tiers = null;
    protected $ds = null;
    protected $identifiant = null;


    public function __construct(acCouchdbJson $ds, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->ds =$ds;
        $this->tiers = $this->ds->getEtablissement();
        $this->lieux_stockage = $this->tiers->getLieuxStockage(true, $this->ds->getIdentifiant());
        $this->appelations = $this->getAppelations();
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds);
        $this->identifiant = $this->ds->getIdentifiant();

        $defaults = array();
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = str_replace($this->identifiant,'', $lieu_s);
            $ds_id = preg_replace("/[0-9]{3}$/", $id_lieu, $this->ds->_id);
            if(array_key_exists($ds_id, $this->dss)){
                $current_ds = $this->dss[$ds_id];
                foreach ($this->getAppelations() as $key => $value) {
                    if($current_ds->exist($key)){
                        $defaults['lieuxStockage_'.$id_lieu][] = $key;
                    }
                }
            }
        }
        if($ds->isDsPrincipale() && $this->ds->isDateDepotMairie()){
            $defaults['date_depot_mairie'] = $this->ds->getDateDepotMairieFr();
        }

        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = str_replace($this->identifiant,'', $lieu_s);
              $this->setWidget('lieuxStockage_'.$id_lieu, new sfWidgetFormChoice(array('choices' => $this->getAppelations(),'expanded' => true, 'multiple' => true)));
              $this->setValidator('lieuxStockage_'.$id_lieu, new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getAppelations()), 'multiple' => true)));
        }
        if($this->ds->isDateDepotMairie()){
            $this->setWidget('date_depot_mairie', new sfWidgetFormInput());
            $this->widgetSchema->setLabel('date_depot_mairie', 'Date de dépot en mairie :');
            $this->setValidator('date_depot_mairie', new sfValidatorRegex(array('required' => true, 'pattern' => "/^([0-9]){2}\/([0-9]){2}\/([0-9]){4}$/"),array('invalid' => 'Le format de la date de dépot en mairie doit être jj/mm/aaaa')));
        }

        $this->setWidget('neant', new sfWidgetFormChoice(array('choices' => $this->getNeant(),'expanded' => true, 'multiple' => true)));
        $this->setValidator('neant', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getNeant()), 'multiple' => true)));

        $this->setWidget('ds_principale', new sfWidgetFormChoice(array('choices' => $this->getLieuxStockage(),'expanded' => false, 'multiple' => true)));
        $this->setValidator('ds_principale', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getLieuxStockage()), 'multiple' => true)));



        $this->getValidatorSchema()->setPostValidator(new ValidatorLieuxStockageDS($this->lieux_stockage,$this->ds));



        $this->widgetSchema->setLabel('neant', 'DS Néant');
        $this->widgetSchema->setNameFormat('ds_lieu[%s]');
    }





    public function doUpdateDss($dss) {

        $values = $this->getValues();
        if(count($values['ds_principale']) != 1){
            throw new sfException("La ds principale doit être basée sur un des lieux de stockage proposés");
        }
        $num = $values['ds_principale'][0];

        foreach ($this->lieux_stockage as $lieu_id => $lieu) {
            $lieu_num = preg_replace('/^(C?[0-9]{10})([0-9]{3})$/', "$2", $lieu_id);
            $ds_id = preg_replace("/[0-9]{3}$/", $lieu_num, $this->ds->_id);
            $current_ds = array_key_exists($ds_id,$dss)? $dss[$ds_id] : null;
            if(!$current_ds){
                $new_ds = DSCivaClient::getInstance()->createDsByDsPrincipaleAndLieu($this->ds,$lieu_num);
                $dss[$new_ds->_id] = $new_ds;
                $current_ds = $dss[$new_ds->_id];
            }
            foreach ($values as $key => $value) {
                $l = preg_replace('/^lieuxStockage_/', '', $key);
                if($l==$lieu_num){
                    foreach ($this->appelations as $key => $appelation) {
                        if($value && in_array($key, $value) && !$current_ds->exist($key)){
                            $current_ds->addAppellation($key);
                        }
                        if( ($value && !in_array($key, $value)) && ($current_ds->exist($key))){
                            if($current_ds->get($key)->hasVolume()){
                                throw new sfException("L'appellation $key de la ds $current_ds->_id ne peut être supprimer car du volume a été saisie");
                            }
                            $current_ds->remove($key);
                        }
                        if(!$value && $current_ds->exist($key)){
                            if($current_ds->get($key)->hasVolume()){
                                throw new sfException("L'appellation $key de la ds $current_ds->_id ne peut être supprimer car du volume a été saisie");
                            }
                            $current_ds->remove($key);
                        }
                    }
                }
        }
        }

        $dss = DSCivaClient::getInstance()->changeDSPrincipale($dss,$this->ds,$num);

        $is_neant = $values['neant'] && $values['neant'][0] == 1;
        foreach ($dss as $ds) {
            if($is_neant){
                if(!$ds->hasNoAppellation()){
                    throw new sfException("La DS $ds->_id possède des appellations, il n'est pas possible de rendre cette DS Néant.");
                }
                if($ds->isDsPrincipale()){
                   $ds->add('ds_neant',1);
                }
            }
            else{
                if($ds->isDsPrincipale()){
                    $ds->add('ds_neant',0);
                }
            }
            if($ds->isDsPrincipale() && $this->ds->isDateDepotMairie()){
                $ds->add('date_depot_mairie',Date::getIsoDateFromFrenchDate($values['date_depot_mairie']));
            }

        }
        return $dss;
    }

    public function getAppelations(){
        $result = array();
        foreach (DSCivaClient::getInstance()->getConfigAppellations($this->ds->getConfig()) as $hash => $configAppellation){
            $result[$hash] = $configAppellation->getLibelle();
        }

        return $result;
    }

    public function getLieuxStockage() {
        $lieux_stockages = array();
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $stockage_num = str_replace($this->identifiant,'', $lieu_s);
            $lieux_stockages[$stockage_num] = $stockage_num;
         }
         return $lieux_stockages;
    }

    public function getNeant() {
        return array(1 => 1);
    }

    public function postValidator(){

    }

}
