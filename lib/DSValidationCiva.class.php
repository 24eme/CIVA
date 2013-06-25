<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DSValidationCiva
 * @author mathurin
 */
class DSValidationCiva  extends DSValidation
{
  public function configure() {
      
    $this->addControle('vigilance', 'stock_null_appellation', acCouchdbManager::getClient('Messages')->getMessage('stock_null_appellation'));
    $this->addControle('vigilance', 'stock_zero_appellation', acCouchdbManager::getClient('Messages')->getMessage('stock_zero_appellation'));
    
    $this->addControle('vigilance', 'stock_null_lieu', acCouchdbManager::getClient('Messages')->getMessage('stock_null_lieu'));
    $this->addControle('vigilance', 'stock_zero_lieu', acCouchdbManager::getClient('Messages')->getMessage('stock_zero_lieu'));
    
    $this->addControle('vigilance', 'stock_null_cepage', acCouchdbManager::getClient('Messages')->getMessage('stock_null_cepage'));
    $this->addControle('vigilance', 'stock_zero_cepage', acCouchdbManager::getClient('Messages')->getMessage('stock_zero_cepage'));
    
    $this->addControle('erreur', 'autres_nul', acCouchdbManager::getClient('Messages')->getMessage('autres_nul'));
    $this->addControle('erreur', 'ds_autres_nul_principale', acCouchdbManager::getClient('Messages')->getMessage('ds_autres_nul_principale'));
  }

  public function controle()
  {
            foreach($this->document->declaration->getAppellations() as $hash => $appellation) {
                    $appellation_vigilence = false;
                    if(!$appellation->total_stock){
                        $this->addPoint('vigilance', 'stock_null_appellation',' '.$appellation->getLibelle(), $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => preg_replace("/^appellation_/", '', $hash)))); 
                        $appellation_vigilence = true;
                    }
                    if(!$appellation_vigilence){
                        foreach($appellation->getLieux() as $hash_lieu => $lieu) {
                            $lieu_vigilence = false;
                            if($hash_lieu!='lieu'){
                                if(!$lieu->total_stock){
                                    $this->addPoint('vigilance', 'stock_null_lieu',' '.$appellation->getLibelle().' '. $lieu->getLibelle(), $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => preg_replace("/^appellation_/", '', $hash).'-'.strtoupper(preg_replace("/^lieu/", '', $hash_lieu))))); 
                                    $lieu_vigilence = true;
                                    }
                                }
                                if(!$lieu_vigilence){
                                    $cepage_vigilence = false;
                                    
                                    foreach ($lieu->getCouleurs() as $hash_couleur => $couleur) {
                                        foreach ($couleur->getCepages() as $hash_cepage => $cepage) {
                                            foreach ($cepage->getProduitsDetails() as $detail) {
                                                
                                             if((!$detail->volume_normal) && (!$detail->volume_vt) && (!$detail->volume_sgn)){
                                                    $this->addPoint('vigilance', 'stock_null_cepage',' '.$cepage->getAppellation()->getLibelle().' '.$cepage->getLibelle(), $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => preg_replace("/^appellation_/", '', $hash)))); 
                                                    $cepage_vigilence = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
      $this->document->declaration->cleanAllNodes();           
      if($this->document->isDsPrincipale() && !$this->document->isDsNeant() && $this->document->hasNoAppellation()){    
          if($this->document->isAutresNul()){
              $this->addPoint('erreur', 'autres_nul',' revenir à lieux de stockage', $this->generateUrl('ds_lieux_stockage', $this->document));               
          }
      }
     }
     
     public function isPoints(){
         foreach ($this->points as $type_point) {
             if(count($type_point)>0) return true;
         }
         return false;
     }
     
     public function isAnyPointBloquant() {
         foreach ($this->points as $type_point) {
             if(array_key_exists('erreur',$type_point) &&  count($type_point['erreur'])>0) return true;
         }
         return false;
     }
     
     public function validate(){
            $this->declaration->cleanAllNodes(); 
         
     }
}