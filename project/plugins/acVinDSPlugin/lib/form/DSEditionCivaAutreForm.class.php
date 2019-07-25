<?php

class DSEditionCivaAutreForm extends acCouchdbObjectForm {

     public function configure() {  
         
        $this->setWidget('rebeches', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('rebeches', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('rebeches', 'Rebêches');
        
        $this->setWidget('dplc', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('dplc', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('dplc', 'Dépassements&nbsp;de&nbsp;rendements blanc');

        $this->setWidget('dplc_rouge', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('dplc_rouge', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('dplc_rouge', 'Dépassements&nbsp;de&nbsp;rendements rouge');

        $this->setWidget('lies', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('lies', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('lies', 'Lies&nbsp;en&nbsp;Stocks');
        
        $this->setWidget('mouts', new sfWidgetFormInputFloat(array(), array('size' => '6')));
        $this->setValidator('mouts', new sfValidatorNumber(array('required' => true)));
        $this->widgetSchema->setLabel('mouts', 'Moûts&nbsp;concentrés&nbsp;rectifiés');
        
        $this->widgetSchema->setNameFormat('ds[%s]');
    }
    
}
