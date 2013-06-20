<?php

class DSLieuxDeStockageForm extends acCouchdbForm {

    protected $lieux_stockage = null;
    protected $tiers = null;
    protected $ds = null;
    
    public function __construct(acCouchdbJson $ds, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->ds =$ds;
        $this->tiers = $this->ds->getEtablissement();
        $this->lieux_stockage = $this->tiers->lieux_stockage;
        $this->appelations = $this->getAppelations();
        $this->dss = DSCivaClient::getInstance()->findDssByDS($this->ds);
        $defaults = array();
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = str_replace($this->tiers->cvi,'', $lieu_s);
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
        
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = str_replace($this->tiers->cvi,'', $lieu_s);               
              $this->setWidget('lieuxStockage_'.$id_lieu, new sfWidgetFormChoice(array('choices' => $this->getAppelations(),'expanded' => true, 'multiple' => true)));
              $this->setValidator('lieuxStockage_'.$id_lieu, new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getAppelations()), 'multiple' => true)));
        }
        $this->setWidget('neant', new sfWidgetFormChoice(array('choices' => $this->getNeant(),'expanded' => true, 'multiple' => true)));
        $this->setValidator('neant', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getNeant()), 'multiple' => true)));
        $this->getValidatorSchema()->setPostValidator(new ValidatorLieuxStockageDS($this->lieux_stockage));

        
        
        $this->widgetSchema->setLabel('neant', 'DS à néant');
        $this->widgetSchema->setNameFormat('ds_lieu[%s]');
    }

    public function doUpdateDss($dss) {
        $values = $this->getValues();
        foreach ($this->lieux_stockage as $lieu_id => $lieu) {
            $lieu_num = preg_replace('/^([0-9]{10})([0-9]{3})$/', "$2", $lieu_id);
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
        $is_neant = $values['neant'] && $values['neant'][0] == 1;
        foreach ($dss as $ds) {
            if($is_neant){
                if(!$ds->hasNoAppellation()){
                    throw new sfException("La DS $ds->_id possède des appellations, il n'est pas possible de rendre cette DS à néant.");
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
        }
        return $dss;
    }
    

    public function getAppelations(){
        $result = array();
        foreach (ConfigurationClient::getConfiguration()->getArrayAppellations() as $conf){
            $result[preg_replace('/^\/recolte/','declaration',$conf->getHash())] = $conf->getLibelle();
        }
        return $result;
    }
    
    public function getNeant() {
        return array(1 => 1);
    }
    
    public function postValidator(){
        
    }

}
