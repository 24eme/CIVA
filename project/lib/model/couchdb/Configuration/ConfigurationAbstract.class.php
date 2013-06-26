<?php

abstract class ConfigurationAbstract extends acCouchdbDocumentTree {

    protected $produits = null;
    protected $produits_filter = array(self::TYPE_DECLARATION_DR => null, self::TYPE_DECLARATION_DS => null);

    const TYPE_DECLARATION_DR = 'DR';
    const TYPE_DECLARATION_DS = 'DS';

    abstract public function getChildrenNode();

    public function getChildrenNodeArray() {
        $items = array();
        foreach($this->getChildrenNode() as $item) {
            $items[$item->getKey()] = $item;
        }

        return $items;
    }

    public function getChildrenFilter($type_declaration = null) {
      $children = array();
      foreach($this->getChildrenNode() as $item) {
        if($type_declaration == self::TYPE_DECLARATION_DR && !$item->isForDR()) {
          continue;
        }

        if($type_declaration == self::TYPE_DECLARATION_DS && !$item->isForDS()) {
          continue;
        }

        $children[$item->getKey()] = $item;
      }

      return $children;
    }

    public function getLibelleLong() {
      if($this->exist('libelle_long') && $this->_get('libelle_long')) {

        return $this->_get('libelle_long');
      }

      return $this->getLibelle();
    }

    protected function loadAllData() {
        parent::loadAllData();
        $this->getProduits();
    }

    public function getParentNode() {
      if ($this->getKey() == 'recolte') {
 
        throw new sfException('Noeud racine atteint');
      }

      return $this->getParent();
    }

    public function getChildrenNodeDeep($level = 1) {
      if($this->hasManyNoeuds()) {
          
          throw new sfException("getChildrenNodeDeep() peut uniquement être appelé d'un noeud qui contient un seul enfant...");
      }

      $node = $this->getChildrenNode()->getFirst();
      
      if($level > 1) {
        
        return $node->getChildrenNodeDeep($level - 1);
      }

      return $node->getChildrenNode();
    }

    public function hasManyNoeuds(){
        if(count($this->getChildrenNode()) > 1){
            return true;
        }
        return false;
    }

    public function getProduits() {
      if(is_null($this->produits)) {
        $this->produits = array();
        foreach($this->getChildrenNode() as $key => $item) {
            $this->produits = array_merge($this->produits, $item->getProduits());
        }
      }

      return $this->produits;
    }

    public function getProduitsFilter($type_declaration = null) {
      if(!$type_declaration) {

        return $this->getProduits();
      }
      
      if(is_null($this->produits[$type_declaration])) {
        $this->produits[$type_declaration] = array();
        foreach($this->getChildrenFilter($type_declaration) as $key => $item) {
            $this->produits[$type_declaration] = array_merge($this->produits[$type_declaration], $item->getProduitsFilter($type_declaration));
        }
      }

      return $this->produits[$type_declaration];
    }

    public function getRendement() {

        return $this->store('rendement', array($this, 'getInternalRendement'));
    }

    protected function getInternalRendement() {
        $key = 'rendement';
        if ($this->exist($key) && $this->_get($key)) {

            return $this->_get($key);
        }

        return $this->getParentNode()->getRendementAppellation();
    }

    public function getRendementAppellation() {
        $key = 'rendement_appellation';
        if ($this->exist($key) && $this->_get($key)) {

            return $this->_get($key);
        }

        return $this->getParentNode()->getRendementAppellation();
    }
  
    public function hasRendementAppellation() {
        $r = $this->getRendementAppellation();

        return ($r && $r > 0);
    }
    
    public function getRendementCouleur() {
        $key = 'rendement_couleur';
        if ($this->exist($key) && $this->_get($key)) {

            return $this->_get($key);
        }

        return $this->getParentNode()->getRendementAppellation();
    }
    
    public function hasRendementCouleur() {
        $r = $this->getRendementCouleur();
        return ($r && $r > 0);
    }

    public function hasMout() {
        if ($this->exist('mout')) {
            
            return ($this->mout == 1);
        } 

        return $this->getParentNode()->hasMout();
    }
    
    public function excludeTotal() 
    {
        return ($this->exist('exclude_total') && $this->get('exclude_total'));
    }

    public function hasTotalCepage() {
      if ($this->exist('no_total_cepage')) {
        
          return !($this->no_total_cepage == 1);
      }

      if ($this->exist('min_quantite') && $this->get('min_quantite')) {
          
          return false;
      } 

      return $this->getParentNode()->hasTotalCepage();
    }

    public function hasVtsgn() {
        if ($this->exist('no_vtsgn')) {
            return (! $this->get('no_vtsgn'));
        }


        if ($this->exist('min_quantite') && $this->get('min_quantite')) {
            return false;
        }

        return $this->getParentNode()->hasVtsgn();
    }

    public function isForDR() {
      
        return !$this->exist('no_dr') || !$this->get('no_dr');
    }

    public function isForDS() {

        return !$this->exist('no_ds') || !$this->get('no_ds');
    }
    
    public function isAutoDs() {
        if ($this->exist('auto_ds')) {
            return $this->get('auto_ds');
        }
        
        return $this->getParentNode()->isAutoDs();
    }

}
