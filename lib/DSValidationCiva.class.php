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
    $this->addControle('vigilance', 'stock_null', 'Il reste des stocks non saisis');
    
    $this->addControle('vigilance', 'stock_null_appellation', "Les stocks de cette appellation n'ont pas été saisis");
    $this->addControle('vigilance', 'stock_zero_appellation', "Les stocks de cette appellation sont saisis à nul");
    
    $this->addControle('vigilance', 'stock_null_lieu', "Les stocks de ce lieu n'ont pas été saisis");
    $this->addControle('vigilance', 'stock_zero_lieu', "Les stocks de ce lieu sont saisis à nul");    
    
    $this->addControle('vigilance', 'stock_null_cepage', "Les stocks de ce cepage n'ont pas été saisis");
    $this->addControle('vigilance', 'stock_zero_cepage', "Les stocks de ce cepage sont saisis à nul");
    
    $this->addControle('erreur', 'autres_nul', "Les moûts, rebêches, dépassements et lies n'ont pas été saisis et il s'agit d'une DS qui n'est pas à néant");    
    
  }

  public function controle()
  {
      if($this->document->isDsPrincipale() && !$this->document->isDsNeant() && $this->document->hasNoAppellation()){    
          if($this->document->isAutresNul()){
              $this->addPoint('erreur', 'autres_nul',' revenir à autres', $this->generateUrl('ds_autre', $this->document));               
          }
      }
      else
      {
            foreach($this->document->declaration->getAppellations() as $hash => $appellation) {
                    $appellation_vigilence = false;
                    
                    if(is_null($appellation->total_stock)){
                        $this->addPoint('vigilance', 'stock_null_appellation',' '.$appellation->getLibelle(), $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => $hash))); 
                        $appellation_vigilence = true;
                    }
                    if(!$appellation_vigilence){
                        $lieu_vigilence = false;
                        foreach($appellation->getLieux() as $hash_lieu => $lieu) {
                            if($hash_lieu!='lieu'){
                                if(is_null($lieu->total_stock)){
                                    $this->addPoint('vigilance', 'stock_null_lieu',' '.$lieu->getLibelle(), $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => $hash_lieu))); 
                                    $lieu_vigilence = true;
                                }
                                if(!$lieu_vigilence){
                                    $cepage_vigilence = false;
                                    foreach ($lieu->getCouleurs() as $hash_couleur => $couleur) {
                                        foreach ($couleur->getCepages() as $hash_cepage => $cepage){
                                            if(is_null($cepage->total_stock)){
                                                $this->addPoint('vigilance', 'stock_null_cepage',' '.$cepage->getAppellation()->getLibelle().' '.$cepage->getLibelle(), $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => $hash_lieu))); 
                                                $cepage_vigilence = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
           }
     }
     
     public function isPoints(){
         foreach ($this->points as $type_point) {
             if(count($type_point)>0) return true;
         }
         return false;
     }
}