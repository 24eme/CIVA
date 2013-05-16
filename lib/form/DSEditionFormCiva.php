<?php

class DSEditionFormCiva extends acCouchdbForm {

    protected $ds = null;
    protected $appellation_lieu = null;

    public function __construct(acCouchdbJson $ds, $appellation_lieu, $defaults = array(), $options = array(), $CSRFSecret = null) {

       $this->ds = $ds;
       $this->appellation_lieu = $appellation_lieu;
       foreach ($this->getProduitsDetails() as $hash => $detail) {     
            $form_key = $detail->getHashForKey();
            
            if(!$detail->getCepage()->no_vtsgn){
                $defaults[DSCivaClient::VOLUME_VT.$form_key] = $detail->volume_vt;
                $defaults[DSCivaClient::VOLUME_SGN.$form_key] = $detail->volume_sgn;
            }  
            $defaults[DSCivaClient::VOLUME_NORMAL.$form_key] = $detail->volume_normal;     
        }
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
        foreach ($this->getProduitsDetails() as $hash => $detail) {
          $key = $detail->getHashForKey();         
          if(!$detail->getCepage()->no_vtsgn){
            $this->setWidget(DSCivaClient::VOLUME_VT . $key, new sfWidgetFormInputFloat(array(), array('size' => '6')));
            $this->setValidator(DSCivaClient::VOLUME_VT . $key, new sfValidatorNumber(array('required' => false)));
            $this->widgetSchema->setLabel(DSCivaClient::VOLUME_VT . $key, DSCivaClient::VOLUME_VT);

            $this->setWidget(DSCivaClient::VOLUME_SGN . $key, new sfWidgetFormInput(array(), array('size' => '6')));
            $this->setValidator(DSCivaClient::VOLUME_SGN . $key, new sfValidatorNumber(array('required' => false)));
            $this->widgetSchema->setLabel(DSCivaClient::VOLUME_SGN . $key, DSCivaClient::VOLUME_SGN);
          }
          $this->setWidget(DSCivaClient::VOLUME_NORMAL . $key, new sfWidgetFormInput(array(), array('size' => '6')));
	  $this->setValidator(DSCivaClient::VOLUME_NORMAL . $key, new sfValidatorNumber(array('required' => false)));
	  $this->widgetSchema->setLabel(DSCivaClient::VOLUME_NORMAL . $key, DSCivaClient::VOLUME_NORMAL);
        }
        $this->widgetSchema->setNameFormat('ds[%s]');
    }

    public function doUpdateObject() {  
        $values = $this->values;
        foreach ($values as $prodKey => $volumeRev) {
	      if (substr($prodKey, 0, strlen(DSCivaClient::VOLUME_NORMAL)) === DSCivaClient::VOLUME_NORMAL){ 
		$this->updateVol(DSCivaClient::VOLUME_NORMAL,$this->keyTohash(substr($prodKey,strlen(DSCivaClient::VOLUME_NORMAL))), $volumeRev);
              }
	      if (substr($prodKey, 0, strlen(DSCivaClient::VOLUME_VT)) === DSCivaClient::VOLUME_VT){ 
		$this->updateVol(DSCivaClient::VOLUME_VT,$this->keyTohash(substr($prodKey,strlen(DSCivaClient::VOLUME_VT))), $volumeRev);
              }
	      if (substr($prodKey, 0, strlen('sgn')) === DSCivaClient::VOLUME_SGN){
		$this->updateVol(DSCivaClient::VOLUME_SGN,$this->keyTohash(substr($prodKey,strlen(DSCivaClient::VOLUME_SGN))), $volumeRev);
              }
            }
    }
    
    public function getProduitsDetails() {
        $matches = array();
        if(preg_match('/^([A-Z]+)-([A-Za-z0-9]+)$/', $this->appellation_lieu,$matches)){
            return $this->ds->declaration->getAppellations()->get('appellation_'.$matches[1])->mention->get('lieu'.$matches[2])->getProduitsDetails();
        }
        return $this->ds->declaration->getAppellations()->get('appellation_'.$this->appellation_lieu)->getProduitsDetails();
    }
    
    private function keyTohash($key) {
        return str_replace('-','/',$key);
        
    }
    

    public function updateVol($kind, $prodKey, $volume) {
        if ($this->getDocument()->get($prodKey)) {
            $this->getDocument()->get($prodKey)->updateVolume($kind,$volume);
        }
    }
     
}
