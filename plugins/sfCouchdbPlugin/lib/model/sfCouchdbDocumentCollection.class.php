<?php

class sfCouchdbDocumentCollection extends sfCouchdbCollection {

    protected function load($data) {
        if (!is_null($data)) {
            try {
                if ($this->_hydrate == sfCouchdbClient::HYDRATE_ARRAY) {
                    foreach ($data["rows"] as $item) {
                        $this->_datas[$item['id']] = $item["doc"];
                    }
                } else {
                    foreach ($data->rows as $item) {
                        if ($this->_hydrate == sfCouchdbClient::HYDRATE_ON_DEMAND) {
                            $this->_datas[$item->id] = null;
                        } elseif ($this->_hydrate == sfCouchdbClient::HYDRATE_ON_DEMAND_WITH_DATA) {
                            $this->_datas[$item->id] = $item->doc;
                        } elseif ($this->_hydrate == sfCouchdbClient::HYDRATE_JSON) {
                            $this->_datas[$item->id] = $item->doc;
                        } elseif ($this->_hydrate == sfCouchdbClient::HYDRATE_DOCUMENT) {
                            $this->_datas[$item->id] = sfCouchdbManager::getClient()->createDocumentFromData($item->doc);
                        }
                    }
                }
            } catch (Exception $exc) {
                throw new sfCouchdbException('Load error : data invalid');
            }
        }
    }

    public function getDocs() {
        return $this->getDatas();
    }

    public function get($id) {
        if ($this->contains($id)) {
            if ($this->_hydrate == sfCouchdbClient::HYDRATE_ON_DEMAND_WITH_DATA && !($this->_datas[$id] instanceof sfCouchdbDocument)) {
                $this->_datas[$id] = sfCouchdbManager::getClient()->createDocumentFromData($this->_datas[$id]);
            }
            if ($this->_hydrate == sfCouchdbClient::HYDRATE_ON_DEMAND && is_null($this->_datas[$id])) {
                $this->_datas[$id] = sfCouchdbManager::getClient()->retrieveDocumentById($id);
            }
            return $this->_datas[$id];
        } else {
            throw new sfCouchdbException('This collection does not contains this id');
        }
    }

}