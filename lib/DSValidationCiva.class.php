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
    
  }

  public function controle()
  {
      if($this->document->isDsNeant()){          
          $this->addPoint('vigilance', 'stock_null',' produit(s) concerné(s)', $this->generateUrl('ds_autre', $this->document)); 
      }
      else
      {
          $dss = DSCivaClient::getInstance()->findDssByDS($this->document);
          foreach ($dss as $current_ds) {
            foreach($current_ds->declaration->getAppellations() as $hash => $appellation) {
                if(is_null($appellation->total_stock)){
                    $this->addPoint('vigilance', 'stock_null_appellation',' '.$appellation->getLibelle().' ('.$current_ds->getLieuStockage().')', $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => $hash))); 
                }
                if($appellation->total_stock == 0) {
                    $this->addPoint('vigilance', 'stock_zero_appellation',' '.$appellation->getLibelle().' ('.$current_ds->getLieuStockage().')', $this->generateUrl('ds_edition_operateur', array('id' => $this->document->_id, 'hash' => $hash))); 
                }
            }
          }
      }
    }
}