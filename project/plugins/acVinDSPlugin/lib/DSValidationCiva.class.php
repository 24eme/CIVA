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
    
    $this->addControle('vigilance', 'stock_aucun_produit', acCouchdbManager::getClient('Messages')->getMessage('stock_aucun_produit'));
  }

  public function controle()
  {
        foreach($this->document->declaration->getAppellationsSorted() as $key => $appellation) {
            $appellation_vigilence = false;
            if(!$appellation->total_stock){
                $this->addPoint('vigilance', 'stock_null_appellation',' '.$appellation->getLibelle(), $this->generateUrl('ds_edition_operateur', array('sf_subject' => $appellation)));
                $appellation_vigilence = true;
            }
            if(!$appellation_vigilence){
                if($appellation instanceof DSGenre) {
                    $this->validationLieu($appellation, $appellation, 'lieu');
                    continue;
                }
                foreach($appellation->getLieux() as $hash_lieu => $lieu) {
                    $this->validationLieu($appellation, $lieu, $hash_lieu);
                }
            }
        }

        $this->document->declaration->cleanAllNodes();
        if($this->document->isDsPrincipale() && !$this->document->isDsNeant() && $this->document->hasNoAppellation()){
          $this->addPoint('vigilance', 'stock_aucun_produit', null, $this->generateUrl('ds_lieux_stockage', $this->document));

      }
     }

     public function validationLieu($appellation, $lieu, $hash_lieu) {
         $lieu_vigilence = false;
         $lieu_libelle = '';
         if($hash_lieu != 'lieu'){
             $lieu_libelle = ' '.$lieu->getLibelle();
             if(!$lieu->total_stock){
                 $this->addPoint('vigilance', 'stock_null_lieu',' '.$appellation->getLibelle().' '. $lieu->getLibelle(), $this->generateUrl('ds_edition_operateur', array('sf_subject' => $lieu)));
                 $lieu_vigilence = true;
             }
         }
         if(!$lieu_vigilence){
            $cepage_vigilence = false;

            foreach ($lieu->getProduitsDetails() as $detail) {
                if(!$detail->isSaisi()){
                    $this->addPoint('vigilance', 'stock_null_cepage',' '.$appellation->getLibelle().$lieu_libelle.' '.$detail->getLibelle(), $this->generateUrl('ds_edition_operateur', array('sf_subject' => $lieu, 'produit' => $detail->getHashForKey())));
                         $cepage_vigilence = true;
                }
            }
         }
     }

     public function hasPoints(){
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
