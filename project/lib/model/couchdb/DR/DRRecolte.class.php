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
            $mentions = array();
            foreach($acheteurs->getNoeudAppellations() as $appellation_key => $appellation) {
                if(in_array($appellation_key, array("mentionVT", "mentionSGN"))) {
                    $mentions[$appellation_key] = $appellation_key;

                    continue;
                }
                $mentions["mention"] = "mention";

            }
            foreach($mentions as $mention_key => $mention) {
                foreach($acheteurs->getNoeudAppellations() as $appellation_key => $appellation) {
                    if(in_array($appellation_key, array("mentionVT", "mentionSGN"))) {

                        continue;
                    }
                    if(!$this->getConfig()->getDocument()->exist(HashMapper::convert($this->getNoeudAppellations()->add($appellation_key)->getHash()."/".$mention_key))) {

                        continue;
                    }
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
            foreach($this->getAppellations() as $appellation) {
                echo $appellation->getKey()."\n";
                foreach($mentions as $mention_key) {
                    if($mention == "mention") {
                        continue;
                    }
                    if(!$acheteurs->getNoeudAppellations()->exist($mention_key)) {
                        $list_to_remove[$appellation->getHash()."/".$mention_key] = $appellation->getHash()."/".$mention_key;
                    }
                }
                if (!$acheteurs->getNoeudAppellations()->exist($appellation->getKey())) {
                    $list_to_remove[$appellation->getHash()] = $appellation->getHash();
                }
            }
            foreach ($list_to_remove as $hash_to_remove) {
                if(count($this->getDocument()->getProduitsDetails()) > 0) {
                    continue;
                }
                $this->getDocument()->remove($hash_to_remove);
            }

        }
    }

}
