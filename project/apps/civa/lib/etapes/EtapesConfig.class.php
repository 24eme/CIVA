<?php
    class EtapesConfig {
        protected $_items = array();
        protected $_orders = array();
        protected $_current_etape_order = null;

        public function  __construct() {
            if (sfConfig::has('app_etapes_items') && sfConfig::has('app_etapes_orders')) {
                $this->_items = sfConfig::get('app_etapes_items');
                $this->_orders = sfConfig::get('app_etapes_orders');
            } else {
                throw new sfException('Etapes config error');
            }
        }

        public function isAutorized($etapeCourante, $etapeCheck) {

            return array_search($etapeCourante, $this->_orders) >= array_search($etapeCheck, $this->_orders);
        }

        public function setCurrentEtape($current_etape) {
            $key = array_search($current_etape, $this->_orders);
            if ($key !== false) {
                $this->_current_etape_order = $key;
            } else {
                throw new sfException(sprintf('This etape does not exist : %s', $current_etape));
            }
        }

        private function getCurrentEtapeRequired() {
            if (!is_null($this->_current_etape_order)) {
                return $this->_current_etape_order;
            } else {
                throw new sfException('No current etape');
            }
        }

        public function getUrl($etape) {
            if(!isset($this->_items[$etape]) || !isset($this->_items[$etape]['url'])) {
                throw new sfException('Etape does not exist');
            }
            return $this->_items[$etape]['url'];
        }

        public function needToChangeEtape() {
            if (array_key_exists('next_is_new_etape', $this->_items[$this->_orders[$this->getCurrentEtapeRequired()]])) {
                return $this->_items[$this->_orders[$this->getCurrentEtapeRequired()]]['next_is_new_etape'];
            } else {
                return false;
            }
        }

        protected function next() {
            if(isset($this->_orders[$this->getCurrentEtapeRequired() + 1])) {
                return $this->_items[$this->_orders[$this->getCurrentEtapeRequired() + 1]];
            } else {
                throw new sfException('Next etape does not exist');
            }
        }

        protected function previous() {
            if(isset($this->_orders[$this->getCurrentEtapeRequired() - 1])) {
                return $this->_items[$this->_orders[$this->getCurrentEtapeRequired() - 1]];
            } else {
                throw new sfException('Previous etape does not exist');
            }
        }

        public function nextUrl() {
            $item = $this->next();
            if (isset($item['url'])) {
                return $item['url'];
            } else {
                throw new sfException('Next url does not exist');
            }
        }

        public function previousUrl() {
            $item = $this->previous();
            if (isset($item['url'])) {
                return $item['url'];
            } else {
                throw new sfException('Next url does not exist');
            }
        }
    }
