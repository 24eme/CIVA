<?php
/**
 * Model for VracAppellation
 *
 */

class VracAppellation extends BaseVracAppellation {
    
    public function getGenre()
    {
        return $this->getParent();
    }
    
    public function getChildrenNode() 
    {
        return $this->getMentions();
    }

    public function getMentions()
    {
        return $this->filter('^mention');
    }

    public function getLieux() 
    {  
        return $this->mention->getLieux();
    }

    public function getLieuxSorted() 
    {  
        return $this->mention->getLieuxSorted();
    }
    
     public function getLibelleComplet() 
     {
     	return $this->libelle;
     }
     
     public function getCodeCiva(){
         $appellation = preg_replace("/^appellation_/", "", $this->getKey());
         switch ($appellation) {
             case "GRDCRU":
                 return "Grds crus";
             case "CREMANT":
                 return "Crémant";
             case "COMMUNALE":
                 return "Communales";
             case "LIEUDIT":
                 return "Lieux-dits";
         }
         return "Alsace";
     }
}