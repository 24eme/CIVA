<?php

class RecolteOnglets {

    protected $_declaration = null;
    protected $_current_key_appellation = null;
    protected $_current_key_mention = null;
    protected $_current_key_lieu = null;
    protected $_current_key_couleur = null;
    protected $_current_key_cepage = null;
    protected $_prefix_key_appellation = null;
    protected $_prefix_key_lieu = null;
    protected $_prefix_key_couleur = null;
    protected $_prefix_key_cepage = null;
    protected $_sf_route_previous_etape = null;
    protected $_sf_route_next_etape = null;

    public function __construct(sfCouchdbJson $declaration, $sf_route_previous_etape = null, $sf_route_next_etape = null) {
        $this->_declaration = $declaration;
        $this->_prefix_key_appellation = 'appellation_';
        $this->_prefix_key_mention = 'mention';
        $this->_prefix_key_lieu = 'lieu';
        $this->_prefix_key_cepage = 'cepage_';
        $this->_prefix_key_couleur = 'couleur';
        $this->_sf_route_previous_etape = str_replace('@', '', $sf_route_previous_etape);
        $this->_sf_route_next_etape = str_replace('@', '', $sf_route_next_etape);
    }

    public function init($appellation, $mention, $lieu, $couleur, $cepage) {
        return (($this->_current_key_appellation = $this->verifyCurrent($appellation, $this->_prefix_key_appellation, 'getItemsAppellation', 'getFirstKeyAppellation'))
        && ($this->_current_key_mention = $this->verifyCurrent($mention, $this->_prefix_key_mention, 'getItemsMention', 'getFirstKeyMention'))
        && ($this->_current_key_lieu = $this->verifyCurrent($lieu, $this->_prefix_key_lieu, 'getItemsLieu', 'getFirstKeyLieu'))
        && ($this->_current_key_couleur = $this->verifyCurrent($couleur, $this->_prefix_key_couleur, 'getItemsCouleur', 'getFirstKeyCouleur'))
        && ($this->_current_key_cepage = $this->verifyCurrent($cepage, $this->_prefix_key_cepage, 'getItemsCepage', 'getFirstKeyCepage')));
    }

    public function getItemsAppellation() {
        return $this->_declaration->get('recolte')->filter('^appellation');
    }

    public function getItemsAppellationConfig() {
        return $this->_declaration->get('recolte')->getConfig()->filter('^appellation');
    }

    public function getItemsMention($appellation = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }
        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        return $this->_declaration->get('recolte')->get($appellation)->filter('^mention');
    }

    public function getItemsMentionConfig($appellation = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }

        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        return $this->_declaration->get('recolte')->get($appellation)->getConfig()->filter('^mention');
    }

    public function getMention($appellation = null, $mention = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }
        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        if (is_null($mention)) {
            $mention = $this->getCurrentKeyMention();
        }
        $mention = $this->convertValueToKey($mention, $this->_prefix_key_mention);
        return $this->_declaration->get('recolte')->get($appellation)->get($mention);
    }


    public function getLieu($appellation = null, $mention = null, $lieu = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }
        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        if (is_null($mention)) {
            $mention = $this->getCurrentKeyMention();
        }
        $mention = $this->convertValueToKey($mention, $this->_prefix_key_mention);
        if (is_null($lieu)) {
            $lieu = $this->getCurrentKeyLieu();
        }
        $lieu = $this->convertValueToKey($lieu, $this->_prefix_key_lieu);

        return $this->_declaration->get('recolte')->get($appellation)->get($mention)->get($lieu);
    }

    public function getItemsLieu($appellation = null, $mention = null) {
        if (is_null($appellation)) {
            $appellation = $this->getCurrentKeyAppellation();
        }
        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        if (is_null($mention)) {
            $mention = $this->getCurrentKeyMention();
        }
        $mention = $this->convertValueToKey($mention, $this->_prefix_key_mention);

        return $this->_declaration->get('recolte')->get($appellation)->get($mention)->filter('^lieu');
    }

    public function getCouleur($appellation = null, $mention = null, $lieu = null, $couleur = null) {
        if (is_null($couleur)) {
            $couleur = $this->getCurrentKeyCouleur();
        }
        $couleur = $this->convertValueToKey($couleur, $this->_prefix_key_couleur);

        return $this->getLieu($appellation, $mention, $lieu)->get($couleur);
    }

    public function getItemsCouleur($appellation = null, $mention = null, $lieu = null) {
        return $this->getLieu($appellation, $mention, $lieu)->filter('^couleur');
    }

    public function getItemsCepage($appellation = null, $mention = null, $lieu = null, $couleur = null) {
        return $this->getCouleur($appellation, $mention, $lieu, $couleur)->getConfig()->filter('^cepage');
    }

    public function getItemsCepageLieu($appellation = null, $mention = null, $lieu = null) {
        return $this->getLieu($appellation, $mention, $lieu)->getConfig()->getCepages();
    }

    public function setCurrentAppellation($value = null) {
        $result = ($this->_current_key_appellation = $this->verifyCurrent($value, $this->_prefix_key_appellation, 'getItemsAppellation', 'getFirstKeyAppellation'));
        if ($result) {
            $this->setCurrentMention();
        }
        return $result;
    }

    public function setCurrentMention($value = null) {

        $result = ($this->_current_key_mention = $this->verifyCurrent($value, $this->_prefix_key_mention, 'getItemsMention', 'getFirstKeyMention'));
        if ($result) {
            $this->setCurrentLieu();
        }
        return $result;
    }

    public function setCurrentLieu($value = null) {
        $result = ($this->_current_key_lieu = $this->verifyCurrent($value, $this->_prefix_key_lieu, 'getItemsLieu', 'getFirstKeyLieu'));
        if ($result) {
            $this->setCurrentCouleur();
        }
        return $result;
    }

    public function setCurrentCouleur($value = null) {
        $result = ($this->_prefix_key_couleur = $this->verifyCurrent($value, $this->_prefix_key_couleur, 'getItemsCouleur', 'getFirstKeyCouleur'));
        if ($result) {
            $this->setCurrentCepage();
        }
        return $result;
    }

    public function setCurrentCepage($value = null) {
        return $this->_current_key_cepage = $this->verifyCurrent($value, $this->_prefix_key_cepage, 'getItemsCepage', 'getFirstKeyCepage');
    }

    public function getCurrentKeyAppellation() {
        return $this->_current_key_appellation;
    }

    public function getCurrentAppellation() {
        return $this->_declaration->recolte->get($this->_current_key_appellation);
    }

    public function getCurrentKeyMention() {
        return $this->_current_key_mention;
    }

    public function getCurrentMention() {
        return $this->getCurrentAppellation()->get($this->_current_key_mention);
    }

    public function getCurrentKeyLieu() {
        return $this->_current_key_lieu;
    }

    public function getCurrentLieu() {
        return $this->getCurrentMention()->get($this->_current_key_lieu);
    }

    public function getCurrentKeyCouleur() {
        return $this->_current_key_couleur;
    }

    public function getCurrentCouleur() {
        return $this->getCurrentLieu()->get($this->_current_key_couleur);
    }

    public function getCurrentKeyCepage() {
        return $this->_current_key_cepage;
    }

    public function getCurrentCepage() {
        return $this->getCurrentCouleur()->get($this->_current_key_cepage);
    }

    public function getCurrentValueAppellation() {
        return $this->convertKeyToValue($this->getCurrentKeyAppellation(), $this->_prefix_key_appellation);
    }

    public function getCurrentValueMention() {
        return $this->convertKeyToValue($this->getCurrentKeyMention(), $this->_prefix_key_mention);
    }


    public function getCurrentValueLieu() {
        return $this->convertKeyToValue($this->getCurrentKeyLieu(), $this->_prefix_key_lieu);
    }

    public function getCurrentValueCouleur() {
        return $this->convertKeyToValue($this->getCurrentKeyCouleur(), $this->_prefix_key_couleur);
    }

    public function getCurrentValueCepage() {
        return $this->convertKeyToValue($this->getCurrentKeyCepage(), $this->_prefix_key_cepage);
    }

    public function getPreviousAppellation() {
        return $this->previous('getItemsAppellationConfig', 'getItemsAppellation', 'getCurrentKeyAppellation');
    }

    public function previousAppellation() {
        $key = $this->getPreviousAppellation();
        if ($key) {
            $this->setCurrentAppellation($key);
        }
        return $key;
    }

    public function getNextAppellation() {
        return $this->next('getItemsAppellationConfig', 'getItemsAppellation', 'getCurrentKeyAppellation');
    }

    public function nextAppellation() {
        $key = $this->getNextAppellation();
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

    public function getPreviousLieu() {
        return $this->previous('getItemsLieu', 'getItemsLieu', 'getCurrentKeyLieu');
    }

    public function previousLieu() {
        $key = $this->getPreviousLieu();
        if ($key) {
            $this->setCurrentLieu($key);
        }
        return $key;
    }

    public function getNextLieu() {
        return $this->next('getItemsLieu', 'getItemsLieu', 'getCurrentKeyLieu');
    }

    public function nextLieu() {
        $key = $this->getNextLieu();
        if ($key) {
            $this->setCurrentLieu($key);
        }
        return $key;
    }

    public function hasPreviousLieu() {
        return ($this->getPreviousLieu() !== false);
    }

    public function hasNextLieu() {
        return ($this->getNextLieu() !== false);
    }

    public function getLastLieu() {
        return $this->last('getItemsLieu');
    }

    public function getLastCouleur() {
        return $this->last('getItemsCouleur');
    }

    public function getPreviousCouleur() {
        return $this->previous('getItemsCouleur', 'getItemsCouleur', 'getCurrentKeyCouleur');
    }

    public function hasPreviousCouleur() {
        return ($this->getPreviousCouleur() !== false);
    }

    public function previousCouleur() {
        $key = $this->getPreviousCouleur();
        if ($key) {
            $this->setCurrentCouleur($key);
        }
        return $key;
    }

    public function getNextCouleur() {
        return $this->next('getItemsCouleur', 'getItemsCouleur', 'getCurrentKeyCouleur');
    }

    public function hasNextCouleur() {
        return ($this->getNextCepage() !== false);
    }

    public function nextCouleur() {
        $key = $this->getNextCouleur();
        if ($key) {
            $this->setCurrentCouleur($key);
        }
        return $key;
    }

    public function getLastCepage() {
        return $this->last('getItemsCepageLieu');
    }

    public function getPreviousCepage() {
        return $this->previous('getItemsCepageLieu', 'getItemsCepageLieu', 'getCurrentKeyCepage');
    }

    public function hasPreviousCepage() {
        return ($this->getPreviousCepage() !== false);
    }

    /*public function previousCepage() {
        $key = $this->getPreviousCepage();
        if ($key) {
            $this->setCurrentCepage($key);
        }
        return $key;
    }*/

    public function getNextCepage() {
        return $this->next('getItemsCepageLieu', 'getItemsCepageLieu', 'getCurrentKeyCepage');
    }

    public function hasNextCepage() {
        return ($this->getNextCepage() !== false);
    }

    /*public function nextCepage() {
        $key = $this->getNextCepage();
        if ($key && !$this->getCurrentCouleur()->exist($key)) {
            $this->nextCouleur();
        } elseif($key) {
            $this->setCurrentCepage($key);
        }
        return $key;
    }*/

    protected function previous($method_items_config, $method_items, $method_get_key) {
        $prev_key = false;
        foreach ($this->$method_items_config() as $key => $item) {
            if ($method_items_config == $method_items || $this->$method_items()->exist($key)) {
                if ($key == $this->$method_get_key()) {
                    return $prev_key;
                }
                $prev_key = $key;
            }
        }
        return false;
    }

    protected function next($method_items_config, $method_items, $method_get_key) {
        $next = false;
        foreach ($this->$method_items_config() as $key => $item) {
            if ($method_items_config == $method_items || $this->$method_items()->exist($key)) {
                if ($next) {
                    return $key;
                }
                $next = ($key == $this->$method_get_key());
            }
        }
        return false;
    }

    protected function first($method_items) {
        return $this->$method_items()->getFirstKey();
    }

    protected function getFirstKeyAppellation() {
        foreach ($this->getItemsAppellationConfig() as $key => $item) {
            if ($this->_declaration->recolte->exist($key)) {
                return $key;
            }
        }
        return $this->getItemsAppellation()->getFirstKey();
    }

    protected function getFirstKeyMention($appellation = null) {
        if (!$appellation)
            $appellation= $this->getCurrentKeyAppellation();

        $appellation = $this->convertValueToKey($appellation, $this->_prefix_key_appellation);
        foreach ($this->getItemsMentionConfig($appellation) as $key => $item) {
            if ($this->_declaration->recolte->$appellation->exist($key)) {
               return $key;
            }
        }
        return $this->getItemsMention()->getFirstKey();
    }

    protected function getFirstKeyLieu($appellation = null, $mention = null) {

        if (!$appellation)
            $appellation= $this->getCurrentKeyAppellation();
        if (!$mention)
            $mention = $this->getCurrentKeyMention();
        return $this->getItemsLieu($appellation, $mention)->getFirstKey();
    }

    protected function getFirstKeyCouleur($appellation = null, $mention = null, $lieu = null, $mention = null) {
        if (!$mention) {
            $mention = $this->getFirstKeyMention($appellation, $mention);
        }

        if (!$lieu) {
            $lieu = $this->getFirstKeyLieu($appellation, $mention);
        }
        return $this->getItemsCouleur($appellation, $mention, $lieu)->getFirstKey();
    }

    protected function getFirstKeyCepage($appellation = null, $mention = null, $lieu = null, $couleur = null) {
        foreach ($this->getItemsCepage($appellation, $mention, $lieu, $couleur) as $key => $item) {
            if ($this->getCouleur($appellation, $mention, $lieu, $couleur)->exist($key)) {
                return $key;
            }
        }
        return $this->getItemsCepage($appellation, $mention, $lieu, $couleur)->getFirstKey();
    }

    protected function last($method_items) {
        return $this->$method_items()->getLastKey();
    }

    public function getUrl($sf_route, $appellation = null, $mention = null,  $lieu = null, $couleur = null, $cepage = null, $sf_anchor = '#onglets_majeurs') {
        if (is_null($appellation)) {
            if (!is_null($this->getCurrentKeyAppellation())) {
                $appellation = $this->getCurrentValueAppellation();
            } else {
                $appellation = $this->getFirstKeyAppellation();
                $mention = $this->getFirstKeyMention($appellation, $mention);
                $lieu = $this->getFirstKeylieu($appellation, $mention);
                $couleur = $this->getFirstKeyCouleur($appellation, $mention, $lieu);
                $cepage = $this->getFirstKeyCepage($appellation, $mention, $lieu, $couleur);
            }
        }
        $appellation = $this->convertKeyToValue($appellation, $this->_prefix_key_appellation);
        if (is_null($mention)){
            if (!is_null($this->getCurrentKeyMention()) && $this->getCurrentValueAppellation() == $appellation) {
            $mention = $this->getFirstKeyMention($appellation, $mention);
            } else {
                $mention = $this->getFirstKeyMention($appellation);
                $lieu = $this->getFirstKeyLieu($appellation, $mention);
                $couleur = $this->getFirstKeyCouleur($appellation, $mention, $lieu);
                $cepage = $this->getFirstKeyCepage($appellation, $mention, $lieu, $couleur);
            }
        }
        $mention = $this->convertKeyToValue($mention, $this->_prefix_key_mention);

        if (is_null($lieu)) {
            if (!is_null($this->getCurrentKeyLieu()) && $this->getCurrentKeyLieu() && $this->getCurrentValueAppellation() == $appellation) {
              $lieu = $this->getCurrentValueLieu();
            } else {
                $lieu = $this->getFirstKeyLieu($appellation, $mention);
                $couleur = $this->getFirstKeyCouleur($appellation, $mention, $lieu);
                $cepage = $this->getFirstKeyCepage($appellation, $mention, $lieu, $couleur);
            }
        }
        $lieu = $this->convertKeyToValue($lieu, $this->_prefix_key_lieu);
        if (is_null($couleur)) {
            if (!is_null($this->getCurrentKeyCouleur()) && $this->getCurrentValueAppellation() == $appellation && $this->getCurrentValueLieu() == $lieu) {
                $couleur = $this->getCurrentValueCouleur();
            } else {
                $couleur = $this->getFirstKeyCouleur($appellation, $mention, $lieu);
                $cepage = $this->getFirstKeyCepage($appellation, $mention, $lieu, $couleur);
            }
        }
        $couleur = $this->convertKeyToValue($couleur, $this->_prefix_key_couleur);

        if (is_null($cepage)) {
            if (!is_null($this->getCurrentKeyCepage()) && $this->getCurrentValueCouleur() == $couleur && $this->getCurrentValueAppellation() == $appellation && $this->getCurrentValueLieu() == $lieu) {
                $cepage = $this->getCurrentValueCepage();
            }
            if (!$cepage) {
                $cepage = $this->getFirstKeyCepage($appellation, $mention, $lieu, $couleur);
            }
        }
        $cepage = $this->convertKeyToValue($cepage, $this->_prefix_key_cepage);
        if ($sf_route == 'recolte') {
            $cepage_key = $this->convertValueToKey($cepage, $this->_prefix_key_cepage);
            if (!$this->getCouleur($appellation, $mention, $lieu, $couleur)->exist($cepage_key) || !$this->getCouleur($appellation, $mention, $lieu, $couleur)->get($cepage_key)->detail->count() > 0) {
                $sf_route = 'recolte_add';
            }
        }

        $mention_str = '';
        if ($mention) {
            $mention_str = '-' . $mention;
        }

        $lieu_str = '';
        if ($lieu) {
            $lieu_str = '-' . $lieu;
        }

        $couleur_cepage = $cepage;
        if ($couleur) {
            $couleur_cepage = $couleur . '-'. $cepage;
        }

        return array('sf_route' => $sf_route, 'appellation_mention_lieu' => $appellation . $mention_str . $lieu_str, 'couleur_cepage' => $couleur_cepage, 'sf_anchor' => $sf_anchor);
    }

    public function getUrlParams($appellation = null, $mention = null, $lieu = null, $couleur = null, $cepage = null, $sf_anchor = '#onglets_majeurs') {
        $url = $this->getUrl(null, $appellation, $mention, $lieu, $couleur, $cepage, $sf_anchor);
        unset($url['sf_route']);
        return $url;
    }

    public function getPreviousUrlCepage() {
        if (!$this->hasPreviousCepage()) {
            return false;
        } elseif($this->getCurrentCouleur()->getConfig()->filter('^cepage')->getFirstKey() == $this->getCurrentKeyCepage() && $this->hasPreviousCouleur()) {
            return $this->getUrl('recolte',null, null, null, $this->getPreviousCouleur(), $this->getPreviousCepage());
        } else {
            return $this->getUrl('recolte', null, null, null, null, $this->getPreviousCepage());
        }
    }

    public function getNextUrlCepage() {
        if (!$this->hasNextCepage()) {
            return false;
        } elseif($this->getCurrentCouleur()->getConfig()->filter('^cepage')->getLastKey() == $this->getCurrentKeyCepage() && $this->hasNextCouleur()) {
            return $this->getUrl('recolte', null, null, null, $this->getNextCouleur(), $this->getNextCepage());
        } else {
            return $this->getUrl('recolte', null, null, null, null, $this->getNextCepage());
        }
    }

    public function getPreviousUrl() {
        if (!$this->hasPreviousLieu() && !$this->hasPreviousAppellation()) {
            if (is_null($this->_sf_route_previous_etape)) {
                throw new sfException("sf route previous etape note defined");
            }
            return array('sf_route' => $this->_sf_route_previous_etape);
        } elseif ($this->hasPreviousLieu()) {
            return $this->getUrl('recolte', null, null, $this->getPreviousLieu());
        } else {
            return $this->getUrl('recolte', $this->getPreviousAppellation());
        }
    }

    public function getNextUrl() {
        if (!$this->hasNextLieu() && !$this->hasNextAppellation()) {
            if (is_null($this->_sf_route_next_etape)) {
                throw new sfException("sf route next etape note defined");
            }
            return array('sf_route' => $this->_sf_route_next_etape);
        } elseif ($this->hasNextLieu()) {
            return $this->getUrl('recolte', null, null, $this->getNextLieu());
        } else {
            return $this->getUrl('recolte', $this->getNextAppellation());
        }
    }

    public function getUrlRecap($with_redirect = false) {
        $url = $this->getUrl('recolte_recapitulatif');
        unset($url['couleur_cepage']);

        if (!$with_redirect) {
            return $url;
        } else {
            return array_merge($url, array('redirect' => true));
        }
    }

    protected function verifyCurrent($value, $prefix, $method, $first_method) {
        if (!$value) {
            $value = $this->$first_method();
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
        return $prefix . $this->convertKeyToValue($value, $prefix);
    }
}

