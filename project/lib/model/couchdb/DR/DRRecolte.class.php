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
     * @return boolean
     */
    public function hasOneOrMoreAppellation() {

        return count($this->getAppellationsAvecVtsgn()) > 0;
    }

    /**
     *
     * @param array $params
     */
    protected function update($params = array()) {
        parent::update($params);

        if (in_array('from_acheteurs',$params)) {
            $mentions = array();
            foreach($this->getDocument()->getAcheteurs()->getNoeudAppellations() as $appellation_key => $appellation) {
                if(in_array($appellation_key, array("mentionVT", "mentionSGN"))) {
                    $mentions[$appellation_key] = $appellation_key;

                    continue;
                }
                $mentions["mention"] = "mention";

            }
            foreach($mentions as $mention_key => $mention) {
                foreach($this->getDocument()->getAcheteurs()->getNoeudAppellations() as $appellation_key => $appellationAcheteurs) {
                    if(in_array($appellation_key, array("mentionVT", "mentionSGN"))) {

                        continue;
                    }

                    $hashConfig = HashMapper::convert($this->getNoeudAppellations()->add($appellation_key)->getHash()."/".$mention_key);
                    if(!$this->getDocument()->getConfig()->exist($hashConfig)) {

                        continue;
                    }
                    $appellation = $this->getNoeudAppellations()->add($appellation_key)->add($mention_key);
                    if (!$appellation->getConfig()->hasManyLieu()) {
                        $lieu = $appellation->add('lieu');
                        foreach ($lieu->getConfig()->getChildrenNode() as $couleur) {
                            $this->getDocument()->getOrAdd(HashMapper::inverse($couleur->getHash()));
                        }
                    }
                }
            }

            $list_to_remove = array();
            foreach($this->getMentions() as $mention) {
                if($mention->getKey() == "mention") {
                    continue;
                }

                if(!$this->getDocument()->getAcheteurs()->getNoeudAppellations()->exist($mention->getKey())) {
                    $list_to_remove[$mention->getHash()] = $mention->getHash();
                }
            }

            foreach($this->getAppellations() as $appellation) {
                $appellation_key = $appellation->getKey();
                if(!$this->getDocument()->getAcheteurs()->getNoeudAppellations()->exist($appellation_key)) {
                    $list_to_remove[$appellation->getHash()] = $appellation->getHash();
                }
            }

            foreach ($list_to_remove as $hash_to_remove) {
                $this->getDocument()->remove($hash_to_remove);
            }
        }
    }

    public function getAppellationsAvecVtsgn() {
        $appellations = array();

        foreach($this->getDocument()->getConfigAppellationsAvecVtsgn() as $appellationConfig) {
            $appellations[$appellationConfig["hash"]] = $appellationConfig;
            $appellations[$appellationConfig["hash"]]["lieux"] = array();
            foreach($appellationConfig["lieux"] as $hashLieu => $lieuConfig) {
                if(!$this->getDocument()->exist($hashLieu)) {
                    continue;
                }
                $appellations[$appellationConfig["hash"]]["lieux"][] = $this->getDocument()->get($hashLieu);
            }
        }

        foreach($this->getMentions() as $mention) {
            if($mention->getKey() == 'mention') {
                $appellations[$mention->getHash()]["noeuds"][$mention->getHash()] = $mention;
                if(!$mention->getConfig()->hasManyLieu()) {
                    $appellations[$mention->getHash()]["lieux"] = array();
                }
                continue;
            }

            $appellations[$mention->getKey()]["noeuds"][$mention->getHash()] = $mention;
        }

        foreach($appellations as $key => $appellation) {
            if(!isset($appellations[$key]["noeuds"]) || !count($appellations[$key]["noeuds"])) {
                unset($appellations[$key]);
                continue;
            }
        }

        return $appellations;
    }

}
