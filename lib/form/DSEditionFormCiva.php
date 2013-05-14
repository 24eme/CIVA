<?php

class DSEditionFormCiva extends acCouchdbForm {

    protected $ds = null;
    protected $appellation = null;

    protected $produitsByAppellation = null;

    public function __construct(acCouchdbJson $ds, $appellation, $defaults = array(), $options = array(), $CSRFSecret = null) {

       $this->ds = $ds;
       $this->appellation = $appellation;
       $this->produitsByAppellation = $this->ds->getProduitsByAppellation($appellation);
       foreach ($this->produitsByAppellation as $key => $cepage) {     
           
            $form_key = str_replace('/','_',$key);
            
            if(!$cepage->no_vtsgn){
                $defaults[DSCivaClient::VOLUME_VT.$form_key] = $cepage->getVT();
                $defaults[DSCivaClient::VOLUME_SGN.$form_key] = $cepage->getSGN();
            }  
            $defaults[DSCivaClient::VOLUME_NORMAL.$form_key] = $cepage->getVolume();     
        }
        parent::__construct($ds, $defaults, $options, $CSRFSecret);
    }

    public function configure() {
      //  parent::configure();
        foreach ($this->produitsByAppellation as $key => $cepage) {
          $key = str_replace('/','_',$key);          
          if(!$cepage->no_vtsgn){
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
    
    public function getProduitsByAppellation() {       
        return $this->produitsByAppellation;
    }


    private function hashTokey(){
        
    }
    
    private function keyTohash($key) {
        if($this->appellation != "LIEUDIT"){
            $appellation_hash =  str_replace($this->appellation,'appellation_'.$this->appellation, str_replace('_','/',$key));
            return str_replace('cepage/','cepage_',$appellation_hash);
        }
        else{
            $appellation_hash = str_replace($this->appellation,'appellation_'.$this->appellation, $key);
            $matches = array();
            preg_match('/^([A-Za-z\-_]+)_(cepage_[A-Z]{2})_([A-Za-z0-9\-]+)/',$appellation_hash, $matches);
            $appellation_hash = $matches[1].'_'.$matches[2];
            $appellation_hash = str_replace($this->appellation,'appellation_'.$this->appellation, str_replace('_','/',$appellation_hash));
            return array('cepage' => str_replace('cepage/','cepage_',$appellation_hash), 'lieu' => $matches[3]);
        }
        
    }
    

    public function updateVol($kind, $prodKey, $volume) {
        if(is_array($prodKey)){
            if ($this->getDocument()->get($prodKey['cepage'])) {
            $this->getDocument()->get($prodKey['cepage'])->updateVolume($kind,$volume,$prodKey['lieu']);
            return;
            }
        }
        if ($this->getDocument()->get($prodKey)) {
            $this->getDocument()->get($prodKey)->updateVolume($kind,$volume);
        }
    }
     
}
