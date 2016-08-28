<?php

class DRRecolte extends BaseDRRecolte {

    public function getChildrenNode() {

        return $this->getCertifications();
    }

    public function getNoeudAppellations() {

        return $this->add('certification')->add('genre');
    }

    public function getAppellations() {

        return $this->getNoeudAppellations()->getAppellations();
    }

    public function getAppellationsSorted() {
        if(!$this->exist('certification')) return array();
        return $this->getChildrenNodeDeep(2)->getAppellationsSorted();
    }

    public function getCertifications() {

        return $this->filter('^certification');
    }

    public function setLies() {

    }

    /**
     *
     * @param array $params
     */
    protected function update($params = array()) {
        parent::update($params);

        if (in_array('from_acheteurs',$params)) {
            $acheteurs = $this->getCouchdbDocument()->getAcheteurs();
            foreach($acheteurs->getNoeudAppellations() as $appellation_key => $appellation) {
                foreach($appellation as $mention_key => $mention) {
                    $app = $this->getNoeudAppellations()->add($appellation_key)->add($mention_key);
                    if (!$app->getConfig()->hasManyLieu()) {
                        $lieu = $app->add('lieu');
                        foreach ($lieu->getConfig()->getChildrenNode() as $k => $v) {
                            $this->getDocument()->getOrAdd(HashMapper::inverse($v->getHash()));
                        }
                    }
                }
            }
            $list_to_remove = array();
            foreach($this->getAppellations() as $key => $appellation) {
                if (!$acheteurs->getNoeudAppellations()->exist($key)) {
                    $list_to_remove[] = $this->getNoeudAppellations()->get($key)->getHash();
                }
            }
            foreach ($list_to_remove as $hash_to_remove) {
               $this->getDocument()->remove($hash_to_remove);
            }

        }
    }

}
