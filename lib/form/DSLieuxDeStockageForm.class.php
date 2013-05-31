<?php

class DSLieuxDeStockageForm extends acCouchdbForm {

    protected $lieux_stockage = null;
    protected $tiers = null;

    public function __construct(acCouchdbJson $ds, $defaults = array(), $options = array(), $CSRFSecret = null) {
        $this->tiers = $ds->getEtablissement();
        $this->lieux_stockage = $this->tiers->lieux_stockage;
        $this->appelations = $this->getAppelations();
        $defaults = array();
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = str_replace($this->tiers->cvi,'', $lieu_s);
            foreach ($this->getAppelations() as $key => $value) {
              if($ds->exist($key)){
                  $defaults['lieuxStockage_'.$id_lieu][] = $key;
              }
            }
        }
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
        foreach ($this->lieux_stockage as $lieu_s => $value) {
            $id_lieu = str_replace($this->tiers->cvi,'', $lieu_s);               
              $this->setWidget('lieuxStockage_'.$id_lieu, new sfWidgetFormChoice(array('choices' => $this->getAppelations(),'expanded' => true, 'multiple' => true)));
              $this->setValidator('lieuxStockage_'.$id_lieu, new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getAppelations()), 'multiple' => true)));
        }
        $this->widgetSchema->setNameFormat('ds_lieu[%s]');
    }

    public function doUpdateDss($dss) {
        $values = $this->getValues();
        foreach ($dss as $ds) {
            $lieu_stockage = $ds->getLieuStockage();
            foreach ($values as $key => $value) {
                $l = preg_replace('/^lieuxStockage_/', '', $key); 
                if($l==$lieu_stockage){
                    foreach ($this->appelations as $key => $appelation) {
                        if(in_array($key, $value) && !$ds->exist($key))
                            $ds->addAppellation($key);
                        if(!in_array($key, $value) && $ds->exist($key))
                            $ds->remove($key);
                    }
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

}
