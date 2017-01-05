<?php

class DSEditionFormCiva extends acCouchdbForm {

    protected $ds = null;
    protected $noeud = null;
    protected $no_vtsgn = null;

    public function __construct(acCouchdbJson $ds, DSLieu $noeud, $defaults = array(), $options = array(), $CSRFSecret = null) {

       $this->ds = $ds;
       $this->noeud = $noeud;
       $this->no_vtsgn = true;
       foreach ($this->getProduitsDetails() as $hash => $detail) {
            $form_key = $detail->getHashForKey();

            if(!$detail->getCepage()->no_vtsgn){
                $this->no_vtsgn = false;
                $defaults[DSCivaClient::VOLUME_VT.$form_key] = (!is_null($detail->volume_vt)) ? sprintf("%01.02f", round($detail->volume_vt, 2)) : null;
                $defaults[DSCivaClient::VOLUME_SGN.$form_key] = (!is_null($detail->volume_sgn)) ? sprintf("%01.02f", round($detail->volume_sgn, 2)) : null;
            }
            $defaults[DSCivaClient::VOLUME_NORMAL.$form_key] = (!is_null($detail->volume_normal)) ? sprintf("%01.02f", round($detail->volume_normal, 2)) : null;
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
	      if (substr($prodKey, 0, strlen(DSCivaClient::VOLUME_SGN)) === DSCivaClient::VOLUME_SGN){
		$this->updateVol(DSCivaClient::VOLUME_SGN,$this->keyTohash(substr($prodKey,strlen(DSCivaClient::VOLUME_SGN))), $volumeRev);
              }
            }
    }

    public function getProduitsDetails() {


        return $this->noeud->getProduitsDetailsSorted();
    }

    private function keyTohash($key) {

        return str_replace('-','/',$key);
    }


    public function updateVol($kind, $prodKey, $volume) {
        if ($this->getDocument()->get($prodKey)) {
            $this->getDocument()->get($prodKey)->updateVolume($kind,$volume);
        }
    }

    public function hasVTSGN() {
        return !$this->no_vtsgn;
    }

}
