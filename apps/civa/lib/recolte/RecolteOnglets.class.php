<?php

class RecolteOnglets {
    protected $_configuration = null;
    protected $_declaration = null;
    protected $_current_key_appellation = null;
    protected $_current_key_lieu = null;
    protected $_current_key_cepage = null;
    protected $_prefix_key_appellation = null;
    protected $_prefix_key_lieu = null;
    protected $_prefix_key_cepage = null;

    public function __construct(sfCouchdbJson $configuration, sfCouchdbJson $declaration) {
        $this->_configuration = $configuration;
        $this->_declaration = $declaration;
        $this->_prefix_key_appellation = 'appellation_';
        $this->_prefix_key_lieu = 'lieu';
        $this->_prefix_key_cepage = 'cepage_';
    }

    public function init($appellation, $lieu, $cepage) {
        return (($this->_current_key_appellation = $this->verifyCurrent($appellation, $this->_prefix_key_appellation, 'getItemsAppellation'))
           &&  ($this->_current_key_lieu = $this->verifyCurrent($lieu, $this->_prefix_key_lieu, 'getItemsLieu'))
           &&  ($this->_current_key_cepage = $this->verifyCurrent($cepage, $this->_prefix_key_cepage, 'getItemsCepage')));
    }

    public function getItemsAppellation() {
        return $this->_declaration->get('recolte')->filter('^appellation');
    }

    public function getLieu($appellation = null, $lieu = null) {
      if (!$appellation)
	$appellation = $this->_current_key_appellation;
      if (!$lieu) 
	$lieu = $this->_current_key_lieu;
      return $this->_declaration->get('recolte')->get($appellation)->get($lieu);
    }

    public function getItemsLieu($appellation = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }
        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        return $this->_declaration->get('recolte')->get($appellation)->filter('^lieu');
    }

    public function getItemsCepage($appellation = null, $lieu = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }
        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        if (is_null($lieu)) {
            $lieu = $this->getCurrentKeyLieu();
        }
        $lieu = $this->convertValueToKey($lieu, $this->_prefix_key_lieu);

        return $this->_configuration->get('recolte')->get($appellation)->get($lieu)->filter('^cepage');
    }

    public function setCurrentAppellation($value = null) {
        $result = ($this->_current_key_appellation = $this->verifyCurrent($value, $this->_prefix_key_appellation, 'getItemsAppellation'));
        if ($result) {
            $this->setCurrentLieu();
        }
        return $result;
    }

    public function setCurrentLieu($value = null) {
        $result = ($this->_current_key_lieu = $this->verifyCurrent($value, $this->_prefix_key_lieu, 'getItemsLieu'));
        if ($result) {
            $this->setCurrentCepage();
        }
        return $result;
    }

    public function setCurrentCepage($value = null) {
        return $this->_current_key_cepage = $this->verifyCurrent($value, $this->_prefix_key_cepage, 'getItemsCepage');
    }

    public function getCurrentKeyAppellation() {
        return $this->_current_key_appellation;
    }

    public function getCurrentKeyLieu() {
        return $this->_current_key_lieu;
    }

    public function getCurrentKeyCepage() {
        return $this->_current_key_cepage;
    }

    public function getCurrentCepage() {
        return $this->getItemsCepage()->get($this->_current_key_cepage);
    }

    public function getCurrentValueAppellation() {
        return $this->convertKeyToValue($this->getCurrentKeyAppellation(), $this->_prefix_key_appellation);
    }

    public function getCurrentValueLieu() {
        return $this->convertKeyToValue($this->getCurrentKeyLieu(), $this->_prefix_key_lieu);
    }

    public function getCurrentValueCepage() {
        return $this->convertKeyToValue($this->getCurrentKeyCepage(), $this->_prefix_key_cepage);
    }

    public function getPreviousAppellation() {
        return $this->previous('getItemsAppellation', 'getCurrentKeyAppellation');
    }

    public function previousAppellation() {
        $key = $this->getPreviousAppellation();
        if ($key) {
            $this->setCurrentAppellation($key);
        }
        return $key;
    }

    public function getNextAppellation() {
        return $this->next('getItemsAppellation', 'getCurrentKeyAppellation');
    }

    public function nextAppellation() {
        $key = $this->getNextAppellation();
        echo $key;
        if ($key) {
            $this->setCurrentAppellation($key);
        }
        return $key;
    }

    public function hasPreviousAppellation() {
        return ($this->getPreviousAppellation() !== false);
    }

    public function hasNextAppellation() {
        return ($this->getNextAppellation() !== false);
    }

    public function getLastAppellation() {
        return $this->last('getItemsAppellation');
    }

    public function getLastCepage() {
        return $this->last('getItemsCepage');
    }

    public function getPreviousCepage() {
        return $this->previous('getItemsCepage', 'getCurrentKeyCepage');
    }

    public function hasPreviousCepage() {
        return ($this->getPreviousCepage() !== false);
    }

    public function previousCepage() {
        $key = $this->getPreviousCepage();
        if ($key) {
            $this->setCurrentCepage($key);
        }
        return $key;
    }

    public function nextLieu() {
        $key = $this->next('getItemsLieu', 'getCurrentKeyLieu');
        if ($key) {
            $this->setCurrentlieu($key);
        }
        return $key;
    }

    public function getNextCepage() {
        return $this->next('getItemsCepage', 'getCurrentKeyCepage');
    }

    public function hasNextCepage() {
        return ($this->getNextCepage() !== false);
    }

    public function nextCepage() {
        $key = $this->getNextCepage();
        if ($key) {
            $this->setCurrentCepage($key);
        }
        return $key;
    }

    protected function previous($method_items, $method_get_key) {
        $prev_key = false;
        foreach($this->$method_items() as $key => $item) {
            if ($key == $this->$method_get_key()) {
                return $prev_key;
            }
            $prev_key = $key;
        }
        return false;
    }

    protected function next($method_items, $method_get_key) {
        $next = false;
        foreach($this->$method_items() as $key => $item) {
            if ($next) {
                return $key;
            }
            $next = ($key == $this->$method_get_key());
        }
        return false;
    }

    protected function first($method_items) {
        return $this->$method_items()->getFirstKey();
    }

    protected function last($method_items) {
        return $this->$method_items()->getLastKey();
    }

    public function getUrl($sf_route, $appellation = null, $lieu = null, $cepage = null) {
        if (is_null($appellation)) {
            if (!is_null($this->getCurrentKeyAppellation())) {
                $appellation = $this->getCurrentValueAppellation();
            } else {
                $appellation = $this->getItemsAppellation()->getFirstKey();
                $lieu = $this->getItemsLieu($appellation)->getFirstKey();
                $cepage = $this->getItemsCepage($appellation, $lieu)->getFirstKey();
            }
        }
        $appellation = $this->convertKeyToValue($appellation, $this->_prefix_key_appellation);

        if (is_null($lieu)) {
            if (!is_null($this->getCurrentKeyLieu()) && $this->getCurrentValueAppellation() == $appellation) {
                $lieu = $this->getCurrentValueLieu();
            } else {
                $lieu = $this->getItemsLieu($appellation)->getFirstKey();
                $cepage = $this->getItemsCepage($appellation, $lieu)->getFirstKey();
            }
        }
        $lieu = $this->convertKeyToValue($lieu, $this->_prefix_key_lieu);

        if (is_null($cepage)) {
            if (!is_null($this->getCurrentKeyCepage()) && $this->getCurrentValueAppellation() == $appellation && $this->getCurrentValueLieu() == $lieu) {
               $cepage = $this->getCurrentValueCepage();
            } else {
               $cepage = $this->getItemsCepage($appellation, $lieu)->getFirstKey();
            }
        }
        $cepage = $this->convertKeyToValue($cepage, $this->_prefix_key_cepage);


	$lieu_str = '';
	if ($lieu) {
	  $lieu_str = '-'.$lieu;
	}
        if (!$cepage) {
            return array('sf_route' => $sf_route, 'appellation_lieu' => $appellation.$lieu_str);
        } else {
            return array('sf_route' => $sf_route, 'appellation_lieu' => $appellation.$lieu_str, 'cepage' => $cepage);
        }
    }


    public function getPreviousUrlCepage() {
        if (!$this->hasPreviousCepage()) {
            return false;
        } else {
            return $this->getUrl('recolte', null, null, $this->getPreviousCepage());
        }

    }

    public function getNextUrlCepage() {
        if (!$this->hasNextCepage()) {
            return false;
        } else {
            return $this->getUrl('recolte', null, null, $this->getNextCepage());
        }

    }

    public function getPreviousUrl() {
        if (!$this->hasPreviousAppellation()) {
            return '@exploitation_autres';
        } else {
            return $this->getUrl('recolte', $this->getPreviousAppellation());
        }

    }

    public function getNextUrl() {
        if (!$this->hasNextAppellation()) {
            return '@validation';
        } else {
            return $this->getUrl('recolte', $this->getNextAppellation());
        }
        
    }

    public function getUrlRecap($with_redirect = false) {
        $url = $this->getUrl('recolte_recapitulatif', null, null, false);
        if (!$with_redirect) {
            return $url;
        } else {
            return array_merge($url, array('redirect' => true));
        }
    }

    protected function verifyCurrent($value, $prefix, $method) {
        if (!$value) {
            $value = $this->first($method);
        }
        $value = $this->convertValueToKey($value, $prefix);
        if ($this->$method()->exist($value)) {
            return $value;
        } else {
            return false;
        }
    }

    protected function convertKeyToValue($key, $prefix) {
        return str_replace($prefix, '', $key);
    }

    protected function convertValueToKey($value, $prefix) {
        return $prefix.$this->convertKeyToValue($value, $prefix);
    }
}

