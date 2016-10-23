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
                foreach($appellation->getMentions() as $mention_key => $mention) {
                    if($mention_key == "mention") {
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
                if(count($this->getDocument()->get($hash_to_remove)->getProduitsDetails()) > 0) {
                    continue;
                }
                $this->getDocument()->remove($hash_to_remove);
            }
        }
    }

    public function getAppellationsAvecVtsgn() {
        $appellations = array();
        foreach($this->getConfig()->getArrayAppellations() as $appellationConfig) {
            if($appellationConfig->getKey() == "PINOTNOIR") {
                $appellations["mentionVT"] = null;
                $appellations["mentionSGN"] = null;
            }
            $hash = HashMapper::inverse($appellationConfig->getHash());
            if(!$this->getDocument()->exist($hash)) {

                continue;
            }
            $appellations[$hash] = null;
        }

        $appellations["mentionVT"] = array("libelle" => "Mention VT", "hash" => "mentionVT", "noeuds" => array(), "lieux" => array());
        $appellations["mentionSGN"] = array("libelle" => "Mention SGN", "hash" => "mentionSGN", "noeuds" => array(), "lieux" => array());

        foreach($appellations as $hash => $null) {
            if(!$this->getDocument()->exist($hash)) {
                continue;
            }
            $appellation = $this->getDocument()->get($hash);

            $appellations[$appellation->getHash()] = array("libelle" => $appellation->getLibelle(), "hash" => $appellation->getHash()."/mention", "lieux" => array(), "noeuds" => array());
            foreach($appellation->getMentions() as $mention) {
                $key = ($mention->getKey() == "mention") ? $appellation->getHash() : $mention->getKey();
                $appellations[$key]['noeuds'][$mention->getHash()] = $mention;
                if($mention->getConfig()->hasManyLieu() || $mention->getKey() != "mention") {
                    foreach($mention->getConfig()->getLieux() as $lieuConfig)  {
                        $hashLieu = HashMapper::inverse($lieuConfig->getHash());
                        if(!$this->getDocument()->exist($hashLieu)) {
                            continue;
                        }
                        $lieu = $this->getDocument()->get($hashLieu);
                        $appellations[$key]['lieux'][] = $lieu;
                    }
                }
            }
        }

        foreach($appellations as $key => $appellation) {
            if(!count($appellations[$key]["noeuds"])) {
                unset($appellations[$key]);
            }
        }

        return $appellations;
    }


}
